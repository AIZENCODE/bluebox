<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanieResource\Pages;
use App\Filament\Resources\CompanieResource\RelationManagers;
use App\Filament\Resources\CompanieResource\RelationManagers\ClientsRelationManager;
use App\Models\Companie;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanieResource extends Resource
{
    protected static ?string $model = Companie::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    // nombre del grupo
    protected static ?string $navigationGroup = 'Negocios';
    protected static ?string $navigationLabel = 'Compañias';
    protected static ?string $modelLabel = 'Compañias';
    protected static ?int $navigationSort = 7;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('estado')
                ->required(),
                Section::make('Compañia')
                    ->description('Imformacion de la compañia.')
                    ->schema([

                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('razon_social')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('ruc')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('correo')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('telefono')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('direccion')
                            ->required()
                            ->maxLength(255),
                      

                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('razon_social')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ruc')
                    ->searchable(),
                Tables\Columns\TextColumn::make('correo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('direccion')
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
            ClientsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompanie::route('/create'),
            'edit' => Pages\EditCompanie::route('/{record}/edit'),
        ];
    }
}
