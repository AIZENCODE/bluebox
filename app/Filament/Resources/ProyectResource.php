<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProyectResource\Pages;
use App\Filament\Resources\ProyectResource\RelationManagers;
use App\Models\Proyect;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProyectResource extends Resource
{
    protected static ?string $model = Proyect::class;

    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';
    protected static ?string $navigationGroup = 'Gestión Operativa';

    protected static ?string $navigationLabel = 'Proyectos';
    // nombre del grupo

    protected static ?string $modelLabel = 'Proyectos';

    // Numero de orden
    protected static ?int $navigationSort = 11;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Proyectos')
                    ->description('Imformacion del proyecto.')
                    ->schema([

                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('descripcion')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('etapa')
                            ->required(),
                        Forms\Components\Toggle::make('estado')
                            ->required(),
                        Forms\Components\TextInput::make('contract_id')
                            ->numeric()
                            ->default(null),

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
                Tables\Columns\TextColumn::make('etapa'),
                Tables\Columns\IconColumn::make('estado')
                    ->boolean(),
                Tables\Columns\TextColumn::make('contract_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
            'index' => Pages\ListProyects::route('/'),
            'create' => Pages\CreateProyect::route('/create'),
            'edit' => Pages\EditProyect::route('/{record}/edit'),
        ];
    }
}
