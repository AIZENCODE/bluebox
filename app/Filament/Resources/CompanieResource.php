<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanieResource\Pages;
use App\Filament\Resources\CompanieResource\RelationManagers;
use App\Filament\Resources\CompanieResource\RelationManagers\ClientsRelationManager;
use App\Models\Companie;
use App\Models\Department;
use App\Models\District;
use App\Models\Province;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Illuminate\Support\Collection;

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
                    ->default(true)
                    ->required(),
                Section::make('Compañia')
                    ->description('Imformacion de la compañia.')
                    ->schema([

                        Forms\Components\TextInput::make('ruc')
                            ->required()
                            ->maxLength(11)
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('Buscar RUC')
                                    ->icon('heroicon-o-magnifying-glass')
                                    ->action(function (Forms\Get $get, Forms\Set $set) {
                                        $ruc = $get('ruc');

                                        if (!$ruc) return;

                                        $response = \Illuminate\Support\Facades\Http::withHeaders([
                                            'Accept' => 'application/json',
                                            'Content-Type' => 'application/json',
                                            'Authorization' => env('API_CONSULTA_RUC_DNI_TOKEN'),
                                        ])->asForm()->post(env('API_CONSULTA_RUC_DNI') . 'ruc', [
                                            'ruc' => $ruc,
                                        ]);

                                        $json = $response->json();

                                        if ($response->ok() && ($json['success'] ?? false)) {
                                            $data = $json['data'];

                                            $set('nombre', $data['nombre_o_razon_social'] ?? '');
                                            $set('razon_social', $data['nombre_o_razon_social'] ?? '');
                                            $set('direccion', $data['direccion'] ?? '');

                                            // Buscar IDs en la base de datos
                                            $departmentId = \App\Models\Department::where('department', $data['departamento'])->value('idDepartment');
                                            $provinceId = \App\Models\Province::where('province', $data['provincia'])->value('idProvince');
                                            $districtId = \App\Models\District::where('district', $data['distrito'])->value('idDistrict');

                                            $set('idDepartment', $departmentId);
                                            $set('idProvince', $provinceId);
                                            $set('idDistrict', $districtId);
                                        } else {
                                            \Filament\Notifications\Notification::make()
                                                ->title('Error al consultar RUC')
                                                ->body('No se encontró información para el RUC ingresado.')
                                                ->danger()
                                                ->send();
                                        }
                                    })
                            ),

                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('razon_social')
                            ->required()
                            ->maxLength(255),
                        // Forms\Components\TextInput::make('ruc')
                        //     ->required()
                        //     ->maxLength(255),


                        Forms\Components\Hidden::make('idCountry')
                            ->default(1),

                        Forms\Components\Select::make('idDepartment')
                            ->label('Departamento')
                            ->relationship(name: 'department', titleAttribute: 'department')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('idProvince', null);
                                $set('idDistrict', null);
                            })
                            ->required(),

                        Forms\Components\Select::make('idProvince')
                            ->label('Provincia')
                            ->options(fn(Get $get): Collection => Province::query()
                                ->where('idDepartment', $get('idDepartment'))
                                ->pluck('province', 'idProvince'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('idDistrict', null);
                            })
                            ->required(),

                        Forms\Components\Select::make('idDistrict')
                            ->label('Distrito')
                            ->options(fn(Get $get): Collection => District::query()
                                ->where('idProvince', $get('idProvince'))
                                ->pluck('district', 'idDistrict'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                // Aquí puedes resetear provincia y distrito si quieres
                            })
                            ->required(),


                        Forms\Components\TextInput::make('direccion')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('correo')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('telefono')
                            ->tel()
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
                // Tables\Columns\TextColumn::make('razon_social')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('ruc')
                    ->searchable(),
                Tables\Columns\TextColumn::make('correo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefono')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('direccion')
                //     ->searchable(),
                Tables\Columns\IconColumn::make('estado')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Ultima Modificación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('deleted_at')
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
