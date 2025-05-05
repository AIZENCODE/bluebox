<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Gestión Operativa';

    protected static ?string $navigationLabel = 'Tickets';
    // nombre del grupo

    protected static ?string $modelLabel = 'Tickets';
    // Numero de orden
    protected static ?int $navigationSort = 13;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Código')
                    ->disabled()
                    ->visible(fn(?Model $record) => $record !== null), // Solo en editar
                Forms\Components\TextInput::make('title')
                    ->label('Titulo')
                    ->columnSpanFull()
                    // ->disabled() // no editable
                    // ->dehydrated() // igual se guarda en la BD    
                    ->required()
                    // ->columnSpanFull()
                    ->maxLength(255),

                Forms\Components\Select::make('proyect_id')
                    ->label('Proyecto')
                    ->relationship(
                        name: 'proyect',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(\Illuminate\Database\Eloquent\Builder $query) =>
                        $query->where('stage', 'seguimiento')
                    )
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                        $set('users', null);
                    })
                    ->placeholder('Selecciona un proyecto'),

                Forms\Components\Select::make('users')
                    ->label('Usuarios asignados')
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



                Forms\Components\Select::make('priority')
                    ->label('Prioridad')
                    ->options([
                        'baja' => 'Baja',
                        'media' => 'Media',
                        'alta' => 'Alta',
                        'urgente' => 'Urgente',
                    ])
                    ->default('media')
                    ->required(),

                Section::make('Documento asociado')
                    // ->description('Documento.')
                    // ->columns(2)
                    ->schema([

                        Forms\Components\FileUpload::make('document')
                            ->label('Documento')
                            ->helperText('Sube el documento asociado al ticket.')
                            ->disk('local') // o el disco que estés usando
                            ->directory('pdfs/tickets') // subcarpeta en storage
                            ->preserveFilenames()
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(5120) // en KB, 5MB = 5120
                            ->dehydrated()
                            ->required(fn(?Model $record) => $record !== null && $record->state === true),


                    ]),
                RichEditor::make('description')
                    ->label('Descripcion')
                    // ->required()
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'strike',
                        'link',
                        'blockquote',
                        'codeBlock',
                        'h2',
                        'h3',
                        'bulletList',
                        'orderedList',
                        'redo',
                        'undo',
                    ])
                    ->columnSpanFull(),

                // Forms\Components\Select::make('status')
                //     ->label('Estado')
                //     ->options([
                //         'abierto' => 'Abierto',
                //         'en_progreso' => 'En Progreso',
                //         'resuelto' => 'Resuelto',
                //         'cerrado' => 'Cerrado',
                //     ])
                //     ->default('abierto')
                //     ->required(),
                // Forms\Components\DatePicker::make('due_date'),
                // Forms\Components\DatePicker::make('resolved_at'),
            ])->columns([
                'sm' => 2,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('id')
                //     ->label('ID')
                //     ->sortable()
                //     ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Codigo')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Titulo')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('proyect.name')
                    ->label('Proyecto')
                    ->sortable()
                    ->searchable(),

                // Tables\Columns\TextColumn::make('description')
                //     ->label('Descripcion')
                //     ->limit(50)
                //     ->sortable()
                //     ->searchable(),
                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridad')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')

                    ->label('Creado el')
                    ->dateTime()
                    ->sortable(),
                //->searchable()



            ])->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->filters([
                Tables\Filters\Filter::make('abiertos_urgentes')
                    ->label('Abiertos y Urgentes')
                    // ->default() // 🔥 Esto lo activa por defecto
                    ->query(function (Builder $query) {
                        return $query->where('status', 'abierto')
                            ->where('priority', 'urgente');
                    }),
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
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}
