<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;


    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    // nombre del grupo
    protected static ?string $navigationGroup = 'Negocios';
    protected static ?string $navigationLabel = 'Clientes';
    protected static ?string $modelLabel = 'Cliente';
    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('state')
                    ->required(),
                Section::make('Cliente')
                    ->description('Imformacion del cliente.')
                    ->schema([


                        Forms\Components\TextInput::make('dni')
                            ->label('DNI')
                            ->maxLength(8)
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('Buscar DNI')
                                    ->icon('heroicon-o-magnifying-glass')
                                    ->action(function (Forms\Get $get, Forms\Set $set) {
                                        $dni = $get('dni');

                                        if (!$dni) return;

                                        $response = \Illuminate\Support\Facades\Http::withHeaders([
                                            'Accept' => 'application/json',
                                            'Content-Type' => 'application/json',
                                            'Authorization' => env('API_CONSULTA_RUC_DNI_TOKEN'),
                                        ])->asForm()->post(env('API_CONSULTA_RUC_DNI') . 'dni', [
                                            'dni' => $dni,
                                        ]);
                                        // dd($response->body());
                                        $json = $response->json();

                                        if ($response->ok() && ($json['success'] ?? false)) {
                                            $data = $json['data'];

                                            $set('name', $data['nombres'] . ' ' . $data['apellido_paterno'] . ' ' . $data['apellido_materno'] ?? '');
                                        } else {
                                            \Filament\Notifications\Notification::make()
                                                ->title('Error al consultar DNI')
                                                ->body('No se encontró información para el DNI ingresado.')
                                                ->danger()
                                                ->send();
                                        }
                                    })
                            ),
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('mail')
                            ->label('Correo')
                            ->maxLength(255),


                        Forms\Components\Select::make('companies')
                            ->label('Compañías')
                            ->relationship(
                                name: 'companies',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn(Builder $query) => $query->where('state', true)
                            )
                            ->searchable()
                            ->preload()
                            ->live(),


                        Forms\Components\TextInput::make('phone_one')
                            ->label('Telefono uno')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone_two')
                            ->label('Telefono dos')
                            ->tel()
                            ->maxLength(255),


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
                Tables\Columns\TextColumn::make('mail')
                    ->label('Correo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_one')
                    ->label('Telefono uno')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_two')
                    ->label('Telefono dos')
                    ->searchable(),
                Tables\Columns\IconColumn::make('state')
                    ->label('Estado')
                    ->boolean(),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
