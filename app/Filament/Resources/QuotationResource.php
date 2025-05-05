<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuotationResource\Pages;
use App\Filament\Resources\QuotationResource\RelationManagers;
use App\Models\Quotation;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuotationResource extends Resource
{
    protected static ?string $model = Quotation::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    // nombre del grupo
    protected static ?string $navigationGroup = 'Negocios';
    protected static ?string $navigationLabel = 'Cotizaciones';
    protected static ?string $modelLabel = 'Cotizaciones';
    protected static ?int $navigationSort = 9;


    public static function form(Form $form): Form
    {
        return $form

            ->schema([
                // Forms\Components\Toggle::make('state')
                //     ->label('Estado')
                //     ->required(),
                Forms\Components\TextInput::make('code')
                    ->label('Código')
                    ->disabled()
                    ->visible(fn(?Model $record) => $record !== null), // Solo en editar


                Section::make('Cotizaciones')
                    ->description('Informacion de la cotización.')
                    ->schema([

                        Forms\Components\TextInput::make('name')
                            ->label('Titulo')
                            ->required()
                            ->live(),

                        Forms\Components\DatePicker::make('creation_date')
                            ->label('Fecha de Creación')
                            ->required()
                            ->default(now())
                            ->live(),

                        Forms\Components\TextInput::make('days')
                            ->label('Días de validación')
                            ->required()
                            ->numeric()
                            ->live()
                            ->afterStateUpdated(function ($set, $get) {
                                $fecha = $get('fecha_creacion') ?? now();
                                $dias = (int) $get('days') ?? 0;
                                $set('fecha_vencimiento_mostrar', \Carbon\Carbon::parse($fecha)->addDays($dias)->format('d/m/Y'));
                            }),

                        Forms\Components\TextInput::make('fecha_vencimiento_mostrar')
                            ->label('Válido hasta')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn($get) => !empty($get('days')))
                            ->afterStateHydrated(function ($set, $get) {
                                $fecha = $get('fecha_creacion') ?? now();
                                $dias = $get('days') ?? 0;
                                $set('fecha_vencimiento_mostrar', \Carbon\Carbon::parse($fecha)->addDays($dias)->format('d/m/Y'));
                            }),

                        Forms\Components\Select::make('stage')
                            ->label('Etapa')
                            ->options(function (callable $get) {
                                $state = $get('state');

                                if ($state) {
                                    return [
                                        'borrador'  => 'Borrador',
                                        'enviada'   => 'Enviada',
                                        'aceptada'  => 'Aceptada',
                                        'rechazada' => 'Rechazada',
                                    ];
                                }

                                return [
                                    'borrador' => 'Borrador', // Solo 'borrador' si no está activo
                                ];
                            })
                            ->default('borrador')
                            ->native(false)
                            ->live() // 👈 necesario para actualizar dinámicamente
                            ->visible(fn(?Model $record) => $record !== null)
                            ->required(),


                        Forms\Components\Select::make('companie_id')
                            ->label('Compañía')
                            ->relationship(
                                name: 'companie',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn(Builder $query) => $query->where('state', true)
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                        Forms\Components\Select::make('currency_id')
                            ->label('Moneda')
                            ->relationship(
                                name: 'currency',
                                titleAttribute: 'name',
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->afterStateUpdated(function ($state, callable $set) {
                                static::setCurrencyExchangeRate($state, $set);
                            })
                            ->afterStateHydrated(function ($state, callable $set) {
                                static::setCurrencyExchangeRate($state, $set);
                            })
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (!$state) {
                                    $set('currency_exchange_rate', '');
                                    return;
                                }

                                $currency = \App\Models\Currency::find($state);

                                if ($currency && $currency->name === 'Dolar') { // <-- AQUI corregimos: comparamos el "name", no el "symbol"
                                    $today = now()->toDateString();

                                    // Intentar traer tipo de cambio hoy
                                    $exchange = \App\Models\Exchange::where('from_currency_id', $currency->id)
                                        ->where('to_currency_id', 2) // ID de Soles = 2
                                        ->whereDate('date', $today)
                                        ->first();

                                    // Si no hay tipo de cambio de hoy, buscar el último
                                    if (!$exchange) {
                                        $exchange = \App\Models\Exchange::where('from_currency_id', $currency->id)
                                            ->where('to_currency_id', 2) // <-- también debe ser 2 aquí
                                            ->orderBy('date', 'desc')
                                            ->first();
                                    }

                                    if ($exchange) {
                                        $set('currency_exchange_rate', $exchange->rate);
                                    } else {
                                        $set('currency_exchange_rate', '');
                                    }
                                } else {
                                    $set('currency_exchange_rate', '');
                                }
                            }),

                        Forms\Components\TextInput::make('currency_exchange_rate')
                            ->label('Tipo de cambio (a soles)')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(function (callable $get) {
                                $currencyId = $get('currency_id');

                                if (!$currencyId) {
                                    return false;
                                }

                                $currency = \App\Models\Currency::find($currencyId);

                                return $currency && $currency->name === 'Dolar'; // <-- Aquí también corregimos: comparando por nombre
                            }),



                        Forms\Components\Select::make('igv_id')
                            ->label('Tipo de IGV')
                            ->relationship(
                                name: 'igv',
                                titleAttribute: 'type',
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $igv = \App\Models\Igv::find($state);
                                $set('igv_porcentage', $igv?->percentage . '%');
                            })
                            ->afterStateHydrated(function ($state, callable $set) {
                                $igv = \App\Models\Igv::find($state);
                                $set('igv_porcentage', $igv?->percentage . '%');
                            }),

                        Forms\Components\TextInput::make('igv_porcentage')
                            ->label('Porcentaje IGV')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn($get) => !empty($get('igv_id'))),

                    ])
                    ->columns(2),

                Section::make('Envio de correo')
                    ->description('Solicitud para enviar cotizacion por correo nuevamente.')
                    ->visible(fn(Get $get) => $get('stage') === 'enviada' && !empty($get('mail_date')))
                    ->schema([
                        Forms\Components\Toggle::make('mail')
                            ->label('Volver a enviar ')
                            ->default(false)
                            ->reactive()
                   
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('email', (bool) $state);
                            }),

                        Forms\Components\TextInput::make('mail_date')
                            ->label('Ultima fecha enviada')
                            ->disabled(true),
                    ]),

                Section::make('Productos')
                    ->description('Productos de la cotización.')
                    ->schema([
                        Forms\Components\Repeater::make('productos')
                            ->label(false)
                            ->addActionLabel('Agregar productos')
                            ->disabled(fn(Forms\Get $get) => $get('stage') === 'aceptada') // 🔥 aquí bloqueamos si la etapa es "aceptada"
                            ->dehydrated() // 🔥🔥🔥 necesario para que no se pierdan
                            // ->addActionAlignment(Alignment::Start)
                            ->reorderable(false)
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Producto')
                                    ->options(fn() => \App\Models\Product::where('state', true)->pluck('name', 'id'))
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems() // 🔥 deshabilita automáticamente repetidos
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        if ($state) {
                                            $product = \App\Models\Product::find($state);
                                            if ($product) {
                                                $set('price', $product->price);
                                                $set('price_min', $product->price_min);
                                                $set('price_max', $product->price_max);

                                                $amount = (float) ($get('amount') ?? 1);
                                                $subtotal = $amount * $product->price;
                                                $set('subtotal', number_format($subtotal, 2, '.', ''));
                                            }
                                        }
                                    })
                                    ->helperText(function (Forms\Get $get) {
                                        $priceMin = $get('price_min');
                                        $priceMax = $get('price_max');

                                        if (!$priceMin && !$priceMax) {
                                            return null;
                                        }

                                        return "Precio mínimo: S/ " . number_format($priceMin, 2) . " — Precio máximo: S/ " . number_format($priceMax, 2);
                                    }),

                                Forms\Components\TextInput::make('amount')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $amount = (float) $state;
                                        $price = (float) $get('price');
                                        $set('subtotal', number_format($amount * $price, 2, '.', ''));
                                    }),

                                Forms\Components\TextInput::make('price')
                                    ->label('Precio')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $amount = (float) $get('amount');
                                        $price = (float) $state;
                                        $set('subtotal', number_format($amount * $price, 2, '.', ''));
                                    }),

                                Forms\Components\TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(false),
                            ])
                            ->columns(4)
                            ->defaultItems(0)
                            ->collapsible()
                            ->afterStateHydrated(function ($state, callable $set, ?Model $record) {
                                if ($record) {
                                    $productos = $record->products()
                                        ->withPivot(['amount', 'price'])
                                        ->get()
                                        ->map(function ($product) {
                                            return [
                                                'product_id' => $product->id,
                                                'amount' => $product->pivot->amount,
                                                'price' => $product->pivot->price,
                                                'subtotal' => number_format($product->pivot->amount * $product->pivot->price, 2, '.', ''),
                                                'price_min' => $product->price_min,
                                                'price_max' => $product->price_max,
                                            ];
                                        })
                                        ->toArray();
                                    $set('productos', $productos);
                                }
                            }),
                    ]),



                Section::make('Servicios')
                    ->description('Servicios de la cotización.')
                    ->schema([
                        Repeater::make('servicios')
                            ->label(false)
                            ->addActionLabel('Agregar servicios')
                            ->disabled(fn(Forms\Get $get) => $get('stage') === 'aceptada') // 🔥 aquí bloqueamos si la etapa es "aceptada"
                            ->dehydrated() // 🔥🔥🔥 necesario para que no se pierdan
                            // ->addActionAlignment(Alignment::Start)
                            ->reorderable(false)
                            ->schema([
                                Select::make('service_id')
                                    ->label('Servicio')
                                    ->options(fn() => \App\Models\Service::where('state', true)->pluck('name', 'id'))
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        if ($state) {
                                            $service = \App\Models\Service::find($state);
                                            if ($service) {
                                                $set('price', $service->price);
                                                $set('price_min', $service->price_min);
                                                $set('price_max', $service->price_max);

                                                $amount = (float) ($get('amount') ?? 1);
                                                $subtotal = $amount * $service->price;
                                                $set('subtotal', number_format($subtotal, 2, '.', ''));
                                            }
                                        }
                                    })
                                    ->helperText(function (Forms\Get $get) {
                                        $priceMin = $get('price_min');
                                        $priceMax = $get('price_max');

                                        if (!$priceMin && !$priceMax) {
                                            return null;
                                        }

                                        return "Precio mínimo: S/ " . number_format($priceMin, 2) . " — Precio máximo: S/ " . number_format($priceMax, 2);
                                    }),


                                TextInput::make('amount')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $amount = (float) $state;
                                        $price = (float) $get('price');
                                        $set('subtotal', number_format($amount * $price, 2, '.', ''));
                                    }),

                                TextInput::make('price')
                                    ->label('Precio')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $amount = (float) $get('amount');
                                        $price = (float) $state;
                                        $set('subtotal', number_format($amount * $price, 2, '.', ''));
                                    }),

                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(false),
                            ])
                            ->columns(4)
                            ->defaultItems(0)
                            ->collapsible()
                            ->afterStateHydrated(function ($state, Set $set, ?Model $record) {
                                if ($record) {
                                    $servicios = $record->services()
                                        ->withPivot(['amount', 'price'])
                                        ->get()
                                        ->map(function ($service) {
                                            return [
                                                'service_id' => $service->id,
                                                'amount' => $service->pivot->amount,
                                                'price' => $service->pivot->price,
                                                'subtotal' => number_format($service->pivot->amount * $service->pivot->price, 2, '.', ''),
                                                'price_min' => $service->price_min,
                                                'price_max' => $service->price_max,
                                            ];
                                        })
                                        ->toArray();
                                    $set('servicios', $servicios);
                                }
                            }),
                    ]),

                Section::make('Totales')
                    ->description('Resumen de la cotización.')
                    ->schema([
                        Forms\Components\Placeholder::make('subtotal_total')
                            ->label('Subtotal')
                            ->content(function ($get) {
                                $productos = $get('productos') ?? [];
                                $servicios = $get('servicios') ?? [];

                                $total_productos = collect($productos)->sum(function ($item) {
                                    return (float) str_replace(',', '', $item['subtotal'] ?? 0);
                                });

                                $total_servicios = collect($servicios)->sum(function ($item) {
                                    return (float) str_replace(',', '', $item['subtotal'] ?? 0);
                                });

                                return 'S/ ' . number_format($total_productos + $total_servicios, 2);
                            }),

                        Forms\Components\Placeholder::make('igv_total')
                            ->label('IGV')
                            ->content(function (Forms\Get $get) {
                                $productos = $get('productos') ?? [];
                                $servicios = $get('servicios') ?? [];

                                $total_productos = collect($productos)->sum(function ($item) {
                                    return (float) str_replace(',', '', $item['subtotal'] ?? 0);
                                });

                                $total_servicios = collect($servicios)->sum(function ($item) {
                                    return (float) str_replace(',', '', $item['subtotal'] ?? 0);
                                });

                                $igv_id = $get('igv_id');
                                $igv = \App\Models\Igv::find($igv_id);
                                $igv_porcentaje = $igv ? $igv->percentage / 100 : 0; // 👈 cuidado que era percentage, no porcentaje

                                $total = $total_productos + $total_servicios;
                                $igv_monto = $total * $igv_porcentaje;

                                return 'S/ ' . number_format($igv_monto, 2);
                            })
                            ->reactive(), // 👈 IMPORTANTE


                        Forms\Components\Placeholder::make('total_final')
                            ->label('Total')
                            ->content(function ($get) {
                                $productos = $get('productos') ?? [];
                                $servicios = $get('servicios') ?? [];

                                $total_productos = collect($productos)->sum(function ($item) {
                                    return (float) str_replace(',', '', $item['subtotal'] ?? 0);
                                });

                                $total_servicios = collect($servicios)->sum(function ($item) {
                                    return (float) str_replace(',', '', $item['subtotal'] ?? 0);
                                });

                                $igv_id = $get('igv_id');
                                $igv = \App\Models\Igv::find($igv_id);
                                $igv_porcentaje = $igv ? $igv->percentage / 100 : 0;

                                $total = $total_productos + $total_servicios;
                                $igv_monto = $total * $igv_porcentaje;

                                return 'S/ ' . number_format($total + $igv_monto, 2);
                            }),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('companie.name')
                    ->label('Compañia')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('creation_date')
                    ->label('Fecha de Creación')
                    ->formatStateUsing(fn($state) => $state ? \Carbon\Carbon::parse($state)->translatedFormat('d \d\e F \d\e Y') : null)
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_vencimiento')
                    ->label('Fecha de Vencimiento')
                    ->getStateUsing(function ($record) {
                        if (!$record->creation_date || !$record->days) {
                            return null;
                        }

                        return \Carbon\Carbon::parse($record->creation_date)
                            ->addDays($record->days)
                            ->translatedFormat('d \d\e F \d\e Y'); // 👈 formato humano
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('stage')
                    ->label('Etapa')
                    // ->enum([
                    //     'borrador'  => 'Borrador',
                    //     'enviada'   => 'Enviada',
                    //     'aceptada'  => 'Aceptada',
                    //     'rechazada' => 'Rechazada',
                    // ])
                    ->sortable()
                    ->searchable(),

                Tables\Columns\IconColumn::make('state')
                    ->label('Estado')
                    ->boolean(),

                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('deleted_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    protected static function setCurrencyExchangeRate($currencyId, callable $set): void
    {
        if (!$currencyId) {
            $set('currency_exchange_rate', '');
            return;
        }

        $currency = \App\Models\Currency::find($currencyId);

        if ($currency && $currency->name === 'Dolar') {
            $today = now()->toDateString();

            $exchange = \App\Models\Exchange::where('from_currency_id', $currency->id)
                ->where('to_currency_id', 2) // Soles
                ->whereDate('date', $today)
                ->first();

            if (!$exchange) {
                $exchange = \App\Models\Exchange::where('from_currency_id', $currency->id)
                    ->where('to_currency_id', 2)
                    ->orderBy('date', 'desc')
                    ->first();
            }

            if ($exchange) {
                $set('currency_exchange_rate', $exchange->rate);
            } else {
                $set('currency_exchange_rate', '');
            }
        } else {
            $set('currency_exchange_rate', '');
        }
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuotations::route('/'),
            'create' => Pages\CreateQuotation::route('/create'),
            'edit' => Pages\EditQuotation::route('/{record}/edit'),
        ];
    }
}
