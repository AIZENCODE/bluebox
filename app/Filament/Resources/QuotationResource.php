<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuotationResource\Pages;
use App\Filament\Resources\QuotationResource\RelationManagers;
use App\Models\Quotation;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
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
                Forms\Components\TextInput::make('code')
                    ->label('Código')
                    ->disabled()
                    ->visible(fn(?Model $record) => $record !== null), // Solo en editar
                Forms\Components\Toggle::make('state')
                    ->label('Estado')
                    ->required(),

                Section::make('Cotizaciones')
                    ->description('Imformacion de la cotización.')
                    ->schema([

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
                            ->options([
                                'borrador'  => 'Borrador',
                                'enviada'   => 'Enviada',
                                'aceptada'  => 'Aceptada',
                                'rechazada' => 'Rechazada',
                            ])
                            ->default('borrador')
                            ->native(false)
                            ->visible(fn(?Model $record) => $record !== null) // Solo visible al editar
                            ->required(), // (opcional, para estilizar mejor el select)

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
                                titleAttribute: 'tipo',
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $igv = \App\Models\Igv::find($state);
                                $set('igv_porcentaje', $igv?->porcentaje . '%');
                            })
                            ->afterStateHydrated(function ($state, callable $set) {
                                $igv = \App\Models\Igv::find($state);
                                $set('igv_porcentaje', $igv?->porcentaje . '%');
                            }),

                        Forms\Components\TextInput::make('igv_porcentaje')
                            ->label('Porcentaje IGV')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn($get) => !empty($get('igv_id'))),

                    ])
                    ->columns(2),

                Section::make('Productos')
                    ->description('productos de la cotización.')
                    ->schema([

                        Forms\Components\Repeater::make('productos')
                            ->label(false)
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Producto')
                                    ->options(function () {
                                        return \App\Models\Product::where('estado', true)
                                            ->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state) {
                                            $product = \App\Models\Product::find($state);
                                            if ($product) {
                                                $set('precio', $product->precio);
                                            }
                                        }
                                    }),
                                Forms\Components\TextInput::make('cantidad')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        $cantidad = (int) $state;
                                        $precio = (float) $get('precio');
                                        $set('subtotal', number_format($cantidad * $precio, 2));
                                    }),
                                Forms\Components\TextInput::make('precio')
                                    ->label('Precio')
                                    ->numeric()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        $cantidad = (int) $get('cantidad');
                                        $precio = (float) $state;
                                        $set('subtotal', number_format($cantidad * $precio, 2));
                                    }),
                                Forms\Components\TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->disabled()
                                    ->dehydrated(false),
                            ])
                            ->columns(4)
                            ->defaultItems(0)
                            ->reorderable(false)
                            ->collapsible()
                            ->afterStateHydrated(function ($state, callable $set, ?Model $record) {
                                if ($record) {
                                    $productos = $record->products()
                                        ->withPivot(['cantidad', 'precio'])
                                        ->get()
                                        ->map(function ($product) {
                                            return [
                                                'product_id' => $product->id,
                                                'cantidad' => $product->pivot->cantidad,
                                                'precio' => $product->pivot->precio,
                                                'subtotal' => number_format($product->pivot->cantidad * $product->pivot->precio, 2),
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

                        Forms\Components\Repeater::make('servicios')
                            ->label(false)
                            ->schema([
                                Forms\Components\Select::make('service_id')
                                    ->label('Servicio')
                                    ->options(function () {
                                        return \App\Models\Service::where('estado', true)
                                            ->pluck('nombre', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        if ($state) {
                                            $service = \App\Models\Service::find($state);
                                            if ($service) {
                                                $set('precio', $service->precio);
                                            }
                                        }
                                    }),
                                Forms\Components\TextInput::make('cantidad')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        $cantidad = (int) $state;
                                        $precio = (float) $get('precio');
                                        $set('subtotal', number_format($cantidad * $precio, 2));
                                    }),
                                Forms\Components\TextInput::make('precio')
                                    ->label('Precio')
                                    ->numeric()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        $cantidad = (int) $get('cantidad');
                                        $precio = (float) $state;
                                        $set('subtotal', number_format($cantidad * $precio, 2));
                                    }),
                                Forms\Components\TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->disabled()
                                    ->dehydrated(false),
                            ])
                            ->columns(4)
                            ->defaultItems(0)
                            ->reorderable(false)
                            ->collapsible()
                            ->afterStateHydrated(function ($state, callable $set, ?Model $record) {
                                if ($record) {
                                    $servicios = $record->services()
                                        ->withPivot(['cantidad', 'precio'])
                                        ->get()
                                        ->map(function ($service) {
                                            return [
                                                'service_id' => $service->id,
                                                'cantidad' => $service->pivot->cantidad,
                                                'precio' => $service->pivot->precio,
                                                'subtotal' => number_format($service->pivot->cantidad * $service->pivot->precio, 2),
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
                                $igv_porcentaje = $igv ? $igv->porcentaje / 100 : 0;

                                $total = $total_productos + $total_servicios;
                                $igv_monto = $total * $igv_porcentaje;

                                return 'S/ ' . number_format($igv_monto, 2);
                            }),

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
                                $igv_porcentaje = $igv ? $igv->porcentaje / 100 : 0;

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

                Tables\Columns\TextColumn::make('companie.nombre')
                    ->label('Compañia')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_creacion')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_vencimiento')
                    ->label('Fecha de Vencimiento')
                    ->getStateUsing(function ($record) {
                        if (!$record->fecha_creacion || !$record->days) {
                            return null;
                        }

                        return \Carbon\Carbon::parse($record->fecha_creacion)
                            ->addDays($record->days)
                            ->format('d/m/Y');
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('etapa'),

                Tables\Columns\IconColumn::make('estado')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListQuotations::route('/'),
            'create' => Pages\CreateQuotation::route('/create'),
            'edit' => Pages\EditQuotation::route('/{record}/edit'),
        ];
    }
}
