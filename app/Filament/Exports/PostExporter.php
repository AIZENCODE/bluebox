<?php

namespace App\Filament\Exports;

use App\Models\Post;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PostExporter extends Exporter
{
    protected static ?string $model = Post::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('category_id'),
            ExportColumn::make('title'),
            ExportColumn::make('slug'),
            ExportColumn::make('excerpt'),
            ExportColumn::make('body'),
            ExportColumn::make('image_url'),
            ExportColumn::make('is_published'),
            ExportColumn::make('published_at'),
            ExportColumn::make('author_id'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your post export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }

    public function getJobQueue(): ?string
    {
        return null; // ← no usar ninguna cola
    }

    public function getJobConnection(): ?string
    {
        return null; // ← no usar ninguna conexión de cola
    }
}
