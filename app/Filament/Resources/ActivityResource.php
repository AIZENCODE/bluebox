<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use App\Filament\Resources\ActivityResource\RelationManagers;
use App\Models\Activity;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;


    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static ?string $navigationGroup = 'Gestión Operativa';

    protected static ?string $navigationLabel = 'Actividades';
    // nombre del grupo

    protected static ?string $modelLabel = 'Actividades';
    // Numero de orden
    protected static ?int $navigationSort = 12;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Actividad')
                    ->description('Informacion de la actividad.')
                    ->columns(2)
                    ->schema([

                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\DatePicker::make('start_date')
                            ->label('Fecha de Inicio')
                        // ->required()
                        ,
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Fecha de Fin')
                        // ->required()
                        ,
                        Forms\Components\Select::make('stage')
                            ->label('Etapa')
                            ->options([
                                'pendiente' => 'Pendiente',
                                'en_progreso' => 'En Progreso',
                                'en_revision' => 'En Revisión',
                                'finalizada' => 'Finalizada',
                                'bloqueada' => 'Bloqueada',
                            ])
                            ->required(),
                        // Forms\Components\Toggle::make('state')
                        //     ->required(),
                        Forms\Components\Select::make('proyect_id')
                            ->relationship('proyect', 'name')
                            ->required()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('users', null);
                            })
                            ->searchable()
                            ->label('Proyecto'),
                        Forms\Components\Select::make('users')
                            ->label('Usuarios')
                            ->multiple()
                            ->searchable()
                            ->preload()

                            ->options(function (callable $get) {
                                $proyectId = $get('proyect_id'); // o el ID que tengas del proyecto

                                if (!$proyectId) {
                                    return [];
                                }

                                return \App\Models\User::whereHas('proyects', function ($query) use ($proyectId) {
                                    $query->where('proyects.id', $proyectId);
                                })->pluck('name', 'id');
                            }),
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            // ->required()
                            ->columnSpanFull(),


                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('proyect.name')
                    ->label('Proyecto')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_fin')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('etapa'),
                // Tables\Columns\IconColumn::make('estado')
                //     ->boolean(),



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
            // RelationManagers\UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
            'create' => Pages\CreateActivity::route('/create'),
            'edit' => Pages\EditActivity::route('/{record}/edit'),
        ];
    }
}
