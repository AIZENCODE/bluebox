<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContractResource\Pages;
use App\Filament\Resources\ContractResource\RelationManagers;
use App\Models\Contract;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContractResource extends Resource
{
    protected static ?string $model = Contract::class;

    // icono
    protected static ?string $navigationIcon = 'heroicon-o-document-check';
    // nombre del grupo
    protected static ?string $navigationGroup = 'Negocios';

    protected static ?string $navigationLabel = 'Contratos';
    protected static ?string $modelLabel = 'Contrato';
    // Numero de orden
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\Toggle::make('state')
                //     ->label('Estado')
                //     ->inline()
                //     ->required(),
                Forms\Components\TextInput::make('code')
                    ->label('Código')
                    ->disabled()
                    ->visible(fn(?Model $record) => $record !== null), // Solo en editar

                Section::make('Contrato')
                    ->description('Informacion del contrato.')
                    ->columns(2)
                    ->schema([

                        Forms\Components\TextInput::make('name')
                            ->label('Titulo')
                            ->required()
                            ->live(),

                        Forms\Components\Select::make('quotation_id')
                            ->label('Cotización')
                            ->options(function () {
                                return \App\Models\Quotation::where('state', true)
                                    ->where('stage', 'aceptada')
                                    ->with('companie') // 👈 Importante traer la relación
                                    ->get()
                                    ->mapWithKeys(function ($quotation) {
                                        return [
                                            $quotation->id => $quotation->companie?->name . ' - ' . $quotation->code,
                                        ];
                                    });
                            })
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),

                        Forms\Components\DatePicker::make('start_date')
                            ->default(now())
                            ->label('Fecha de inicio')
                            ->required(fn(callable $get) => in_array($get('stage'), [
                                'enviado',
                                'inicio',
                                'proceso',
                                'finalizado',
                                'cancelado',
                            ])),

                        Forms\Components\DatePicker::make('end_date')
                            ->label('Fecha de fin')
                            ->required(fn(callable $get) => in_array($get('stage'), [
                                'enviado',
                                'inicio',
                                'proceso',
                                'finalizado',
                                'cancelado',
                            ])),
                        Forms\Components\Select::make('stage')
                            ->label('Etapa')
                            ->default('inicio')
                            ->options([
                                'pendiente' => 'Pendiente',
                                'enviado' => 'Enviado',
                                'inicio' => 'Inicio',
                                'proceso' => 'Proceso',
                                'finalizado' => 'Finalizado',
                                'cancelado' => 'Cancelado',
                            ])
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



                    ]),

                Section::make('Documento asociado')
                    // ->description('Documento.')
                    // ->columns(2)
                    ->schema([

                        Forms\Components\FileUpload::make('document')
                            ->label('Documento')
                            ->helperText('Sube el documento asociado al contrato.')
                            ->disk('local') // o el disco que estés usando
                            ->directory('pdfs/contratos') // subcarpeta en storage
                            ->preserveFilenames()
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120) // en KB, 5MB = 5120
                            ->dehydrated()
                            ->required(fn(?Model $record) => $record !== null && $record->state === true),


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

                Tables\Columns\TextColumn::make('code')
                    ->label('Codigo')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Fecha de inicio')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fecha de fin')
                    ->date()
                    ->sortable(),
                // Tables\Columns\IconColumn::make('state')
                //     ->boolean(),
                Tables\Columns\TextColumn::make('stage')
                    ->label('Etapa'),

                Tables\Columns\TextColumn::make('quotation.code')
                    ->label('Cotización')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContracts::route('/'),
            // 'create' => Pages\CreateContract::route('/create'),
            'edit' => Pages\EditContract::route('/{record}/edit'),
        ];
    }
}
