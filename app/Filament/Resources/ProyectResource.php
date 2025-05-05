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
use Illuminate\Database\Eloquent\Model;
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

                Forms\Components\TextInput::make('code')
                    ->label('Código')
                    ->disabled()
                    ->visible(fn(?Model $record) => $record !== null), // Solo en editar

                Section::make('Proyectos')
                    ->description('Informacion del proyecto.')
                  
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->columnSpanFull()
                            ->maxLength(255),
                        Forms\Components\Select::make('contract_id')
                            ->relationship('contract', 'code'),

                        Forms\Components\DatePicker::make('start_date')
                            ->label('Fecha de Inicio'),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Fecha de Fin'),

                        Forms\Components\Select::make('stage')
                            ->label('Etapa')
                            ->options([
                                'iniciado' => 'Iniciado',
                                'en_proceso' => 'Analisis',
                                'en_desarrollo' => 'Desarrollo',
                                'en_pruebas' => 'Pruebas',
                                'en_espera' => 'En Espera',
                                'en_espera_revision' => 'En Espera Revision',
                                'seguimiento' => 'Seguimiento',
                                'finalizado' => 'Finalizado',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            
                            ->columnSpanFull(),


                        // Forms\Components\Toggle::make('estado')
                        //     ->required(),


                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stage')
                    ->label('Etapa')

                    ->sortable()
                    ->searchable(),
                // Tables\Columns\IconColumn::make('estado')
                //     ->boolean(),
                Tables\Columns\TextColumn::make('contract.code')
                    ->label('Contrato')
                    ->placeholder('Sin contrato') // ✅ reemplaza ifNone
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
            RelationManagers\UsersRelationManager::class,
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
