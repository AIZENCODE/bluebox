<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountResource\Pages;
use App\Filament\Resources\AccountResource\RelationManagers;
use App\Models\Account;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;
    // nombre del grupo
    protected static ?string $navigationGroup = 'Maestro';
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Cuentas';
    protected static ?string $modelLabel = 'Cuenta';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('bank_id')
                    ->label('Banco')
                    ->relationship(name: 'bank', titleAttribute: 'nombre')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required(),
                Forms\Components\Select::make('accounttype_id')
                    ->label('Tipo')
                    ->relationship(name: 'accounttype', titleAttribute: 'nombre')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required(),

                Forms\Components\Select::make('currency_id')
                    ->label('Moneda')
                    ->relationship(name: 'currency', titleAttribute: 'nombre')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required(),

                Forms\Components\TextInput::make('numero')
                    ->label('N° de cuenta')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('numero_interbancario')
                    ->label('N° de cuenta interbancaria')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('estado')
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('accountType.nombre')
                    ->label('Tipo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bank.nombre')
                    ->label('Banco')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency.nombre')
                    ->label('Moneda')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('numero')
                    ->label('Numero de cuenta')
                    ->searchable(),
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
            'index' => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'edit' => Pages\EditAccount::route('/{record}/edit'),
        ];
    }
}
