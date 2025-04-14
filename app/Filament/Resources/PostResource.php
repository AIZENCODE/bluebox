<?php

namespace App\Filament\Resources;

use App\Exports\PostExport;
use App\Filament\Exports\PostExporter;
use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Exports\Concerns\WithColumns;
use pxlrbt\FilamentExcel\Columns\Column;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Pagina Web';

    protected static ?string $navigationLabel = 'Articulos';
    // nombre del grupo

    protected static ?string $modelLabel = 'Articulos';
    // Numero de orden
    protected static ?int $navigationSort = 13;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Toggle::make('is_published')
                    ->label('Publicado'),
                Forms\Components\TextInput::make('slug')
                    ->disabled() // no editable
                    ->dehydrated() // igual se guarda en la BD
                    ->required(),

                Forms\Components\TextInput::make('title')
                    ->label('Titulo')
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255)
                    ->live() // actualiza en tiempo real mientras escribes
                    ->afterStateUpdated(function (string $state, callable $set) {
                        $set('slug', Str::slug($state));
                    }),




                Textarea::make('excerpt')
                    ->label('Extracto')
                    ->columnSpanFull()
                    ->maxLength(255),

                RichEditor::make('body')
                    ->label('Contenido')
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

                FileUpload::make('image_url')
                    ->label('Imagen')
                    ->image()
                    ->disk('public')
                    ->directory('posts')
                    ->preserveFilenames(),

                Select::make('category_id')
                    ->label('Categoría')
                    ->relationship('category', 'name')
                    ->required(),

                // Select::make('author_id')
                //     ->label('Autor')
                //     ->relationship('author', 'name')
                //     ->required(),

                Select::make('tags')
                    ->relationship('tags', 'name') // nombre visible
                    ->label('Etiquetas')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->required(),



                DatePicker::make('published_at')
                    ->label('Fecha de publicación')
                    ->seconds(false)
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table

            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titulo')
                    ->searchable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoria')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tags.name')
                    ->label('Etiquetas')
                    ->searchable(),


                Tables\Columns\TextColumn::make('created_at')
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

                    BulkAction::make('exportar_con_estilos')
                        ->label('Exportar con estilo')
                        ->action(function ($records) {
                            return Excel::download(new PostExport($records), 'articulos_estilizados.xlsx');
                        })
                        ->icon('heroicon-m-arrow-down-tray')
                        ->requiresConfirmation(),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
