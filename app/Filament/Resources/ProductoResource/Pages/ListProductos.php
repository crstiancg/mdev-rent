<?php

namespace App\Filament\Resources\ProductoResource\Pages;

use App\Filament\Resources\ProductoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductos extends ListRecords
{
    protected static string $resource = ProductoResource::class;
    protected static ?string $title = 'Productos';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Producto')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->tooltip('Crear nuevo producto'),
        ];
    }
}
