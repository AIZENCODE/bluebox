<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContractResource\Pages;
use App\Filament\Resources\ContractResource\RelationManagers;
use App\Models\Contract;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
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
                Forms\Components\Toggle::make('state')
                    ->label('Estado')
                    ->inline()
                    ->required(),

                Section::make('Contrato')
                    ->description('Informacion del contrato.')
                    ->columns(2)
                    ->schema([

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
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Fecha de fin')
                            ->required(),

                        Forms\Components\Select::make('stage')
                            ->label('Etapa')
                            ->default('inicio')
                            ->options([
                                'inicio' => 'Inicio',
                                'proceso' => 'Proceso',
                                'finalizado' => 'Finalizado',
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


                    ]),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('state')
                    ->boolean(),
                Tables\Columns\TextColumn::make('stage'),
                Tables\Columns\TextColumn::make('quatation_id')
                    ->numeric()
                    ->sortable(),
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
            'create' => Pages\CreateContract::route('/create'),
            'edit' => Pages\EditContract::route('/{record}/edit'),
        ];
    }
}
