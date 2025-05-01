<?php

namespace App\Filament\Resources\CompanieResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientsRelationManager extends RelationManager
{
    protected static string $relationship = 'clients';


    protected static ?string $navigationLabel = 'Clientes';
    protected static ?string $modelLabel = 'Cliente';



    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('mail')
                    ->label('Correo')
                    ->maxLength(255),

                Forms\Components\TextInput::make('phone_one')
                    ->tel()
                    ->required()
                    ->label('Teléfono 1')
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_two')
                    ->tel()
                    ->label('Teléfono 2')
                    ->required()
                    ->maxLength(255),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            // ->label('Clientes')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre'),
                Tables\Columns\TextColumn::make('mail')
                    ->label('Correo'),
                Tables\Columns\TextColumn::make('phone_one')
                    ->label('Teléfono 1'),
                Tables\Columns\TextColumn::make('phone_two')
                    ->label('Teléfono 2'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
