<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExchangeResource\Pages;
use App\Filament\Resources\ExchangeResource\RelationManagers;
use App\Models\Exchange;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExchangeResource extends Resource
{
    protected static ?string $model = Exchange::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Maestro';

    protected static ?string $navigationLabel = 'Tipos de Cambio';
    protected static ?string $modelLabel = 'Tipo de Cambio';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Tipo de Cambio')
                    ->description('Registro del tipo de cambio diario.')
                    ->schema([
                        Select::make('from_currency_id')
                            ->label('De Moneda')
                            ->relationship(
                                name: 'fromCurrency',
                                titleAttribute: 'name',
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('to_currency_id')
                            ->label('A Moneda')
                            ->relationship(
                                name: 'toCurrency',
                                titleAttribute: 'name',
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('rate')
                            ->label('Tasa de Cambio')
                            ->numeric()
                            ->required()
                            ->minValue(0.000001)
                            ->maxValue(999999999.999999),

                        DatePicker::make('date')
                            ->label('Fecha')
                            ->required()
                            ->default(now())
                            ->unique(ignoreRecord: true), // 🔥 Esto es lo ideal en Filament 3
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fromCurrency.name')
                    ->label('De Moneda')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('toCurrency.name')
                    ->label('A Moneda')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('rate')
                    ->label('Tasa de Cambio')
                    ->numeric(decimalPlaces: 6)
                    ->sortable(),

                TextColumn::make('date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Desde'),
                        Forms\Components\DatePicker::make('until')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('date', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('date', '<=', $data['until']));
                    }),
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
            'index' => Pages\ListExchanges::route('/'),
            'create' => Pages\CreateExchange::route('/create'),
            'edit' => Pages\EditExchange::route('/{record}/edit'),
        ];
    }
}
