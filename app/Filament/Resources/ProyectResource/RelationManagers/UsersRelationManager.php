<?php

namespace App\Filament\Resources\ProyectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\Select::make('record_id')
                //     ->label('Usuario')
                //     ->options(\App\Models\User::pluck('name', 'id'))
                //     ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                //     ->searchable()
                //     ->preload()
                //     ->required()

                // Forms\Components\Select::make('role')
                //     ->options([
                //         'admin' => 'Administrador',
                //         'user' => 'Usuario',
                //     ])
                //     ->required()
                //     ->label('Rol'),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo')
                    ->searchable(),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('Agregar usuario')
                    ->recordTitleAttribute('name') // 👈 esto muestra el nombre del usuario en el select
                    ->recordSelectSearchColumns(['name', 'email'])
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(function ($query, $livewire) {
                        return $query->whereDoesntHave('proyects', function ($q) use ($livewire) {
                            $q->where('proyects.id', $livewire->getOwnerRecord()->id);
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
