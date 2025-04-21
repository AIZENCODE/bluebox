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

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    // nombre del grupo
    protected static ?string $navigationGroup = 'Maestro';
    protected static ?string $navigationLabel = 'Empresa';
    protected static ?string $modelLabel = 'Información';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre de la Empresa')
                    ->required(),
                Forms\Components\TextInput::make('company_name')
                    ->label('Razón Social')
                    ->required(),
                Forms\Components\TextInput::make('ruc')
                    ->label('RUC')
                    ->required(),
                Forms\Components\TextInput::make('phone_one')
                    ->label('Teléfono Principal')
                    ->tel()
                    ->required(),
                Forms\Components\TextInput::make('phone_two')
                    ->label('Teléfono Secundario')
                    ->tel()
                    ->nullable(),
                Forms\Components\TextInput::make('email_one')
                    ->label('Correo Principal')
                    ->email()
                    ->required(),
                Forms\Components\TextInput::make('email_two')
                    ->label('Correo Secundario')
                    ->email()
                    ->nullable(),
                Forms\Components\Textarea::make('address_one')
                    ->label('Dirección Principal')
                    ->required(),
                Forms\Components\Textarea::make('address_two')
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
