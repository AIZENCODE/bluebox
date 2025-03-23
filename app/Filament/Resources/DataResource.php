<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DataResource\Pages;
use App\Filament\Resources\DataResource\RelationManagers;
use App\Models\Data;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DataResource extends Resource
{
    protected static ?string $model = Data::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Información de la Empresa';
    protected static ?string $modelLabel = 'Información';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre de la Empresa')
                    ->required(),
                Forms\Components\TextInput::make('razon_social')
                    ->label('Razón Social')
                    ->required(),
                Forms\Components\TextInput::make('ruc')
                    ->label('RUC')
                    ->required(),
                Forms\Components\TextInput::make('telefono_uno')
                    ->label('Teléfono Principal')
                    ->tel()
                    ->required(),
                Forms\Components\TextInput::make('telefono_dos')
                    ->label('Teléfono Secundario')
                    ->tel()
                    ->nullable(),
                Forms\Components\TextInput::make('correo_uno')
                    ->label('Correo Principal')
                    ->email()
                    ->required(),
                Forms\Components\TextInput::make('correo_dos')
                    ->label('Correo Secundario')
                    ->email()
                    ->nullable(),
                Forms\Components\Textarea::make('direccion_uno')
                    ->label('Dirección Principal')
                    ->required(),
                Forms\Components\Textarea::make('direccion_dos')
                    ->label('Dirección Secundaria')
                    ->nullable(),
            ]);
    }

    
    public static function getPages(): array
    {
        return [
            'index' => Pages\EditData::route('/'),
        ];
    }

    public static function getNavigationUrl(): string
    {
        return static::getUrl('index');
    }

    // public static function table(Table $table): Table
    // {
    //     return $table
    //         ->columns([
    //             //
    //         ])
    //         ->filters([
    //             //
    //         ])
    //         ->actions([
    //             Tables\Actions\EditAction::make(),
    //         ])
    //         ->bulkActions([
    //             Tables\Actions\BulkActionGroup::make([
    //                 Tables\Actions\DeleteBulkAction::make(),
    //             ]),
    //         ]);
    // }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }


    public static function canCreate(): bool
    {
        return false; // ❌ No permitir creación
    }

    public static function canDelete($record): bool
    {
        return false; // ❌ No permitir eliminación
    }

    public static function canDeleteAny(): bool
    {
        return false; // ❌ No permitir eliminación en lista
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true; // ✅ Asegurar que el menú aparece
    }

}
