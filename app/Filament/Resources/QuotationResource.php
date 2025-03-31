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
                Forms\Components\Toggle::make('estado')
                    ->required(),
                Forms\Components\TextInput::make('codigo')
                    ->label('Código')
                    ->disabled()
                    ->visible(fn(?Model $record) => $record !== null), // Solo en editar
                Section::make('Cotizaciones')
                    ->description('Imformacion de la cotización.')
                    ->schema([

                        Forms\Components\DatePicker::make('fecha_creacion')
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
                        Forms\Components\Select::make('etapa')
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
                                titleAttribute: 'nombre',
                                modifyQueryUsing: fn(Builder $query) => $query->where('estado', true)
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

                Forms\Components\Repeater::make('productos')
                    ->label('Productos')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('Producto')
                            ->options(\App\Models\Product::pluck('nombre', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('cantidad')->numeric()->required(),
                        Forms\Components\TextInput::make('precio')->numeric()->required(),
                    ])
                    ->defaultItems(1)
                    ->columns(3)
                    ->createItemButtonLabel('Agregar producto')
                    ->columnSpan('full'),

                Section::make('Servicios')
                    ->description('Servicios de la cotización.')
                    ->schema([

                        Forms\Components\Repeater::make('servicios')
                            ->label(false)
                            ->schema([
                                Forms\Components\Select::make('service_id')
                                    ->label('Servicio')
                                    ->options(\App\Models\Service::pluck('nombre', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->required()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $service = \App\Models\Service::find($state);
                                        if ($service) {
                                            $set(
                                                'precio_info',
                                                "Min: S/ " . number_format($service->precio_min ?? 0, 2) . " - " .
                                                    "Actual: S/ " . number_format($service->precio, 2) . " - " .
                                                    "Max: S/ " . number_format($service->precio_max ?? 0, 2)
                                            );
                                            // $set('precio', $service->precio); 
                                        } else {
                                            $set('precio_info', null);
                                            $set('precio', null);
                                        }
                                    }),

                                Forms\Components\TextInput::make('cantidad')
                                    ->label('Cantidad')
                                    ->numeric()
                                    ->live()
                                    ->required()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $precio = (float) $get('precio');
                                        $set('subtotal', number_format((float) $state * $precio, 2));
                                    }),

                                Forms\Components\TextInput::make('precio')
                                    ->label('Precio')
                                    ->numeric()
                                    ->step('0.01') // Define el paso de incremento para permitir decimales
                                    ->live()
                                    ->required()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $cantidad = (float) $get('cantidad');
                                        $set('subtotal', number_format((float) $state * $cantidad, 2));
                                    }),

                                    Forms\Components\TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->disabled()
                                    ->dehydrated(false) // No lo guarda
                                    ->reactive() // importante para que escuche cambios
                                    ->visible()
                                    ->afterStateUpdated(function ($set, $get) {
                                        $cantidad = (float) $get('cantidad') ?? 0;
                                        $precio = (float) $get('precio') ?? 0;
                                        $set('subtotal', number_format($cantidad * $precio, 2, '.', ''));
                                    })
                                    ->afterStateHydrated(function ($set, $get) {
                                        $cantidad = (float) $get('cantidad') ?? 0;
                                        $precio = (float) $get('precio') ?? 0;
                                        $set('subtotal', number_format($cantidad * $precio, 2, '.', ''));
                                    }),
                                

                                Forms\Components\Placeholder::make('precio_info')
                                    ->label('Rango de Precios')
                                    ->content(fn($get) => nl2br(e($get('precio_info'))) ?? '—')
                                    ->columnSpanFull(), // Ocupa una columna completa si quieres
                            ])
                            ->columns(4)
                            ->defaultItems(1)
                            ->createItemButtonLabel('Agregar servicio')
                            ->columnSpan('full'),

                    ])
                    ->columns(2),


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
