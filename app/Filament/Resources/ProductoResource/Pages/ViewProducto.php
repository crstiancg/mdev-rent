<?php

namespace App\Filament\Resources\ProductoResource\Pages;

use App\Filament\Resources\ProductoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProducto extends ViewRecord
{
    protected static string $resource = ProductoResource::class;
    protected static ?string $title = 'Información del Producto';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar Producto')
                ->icon('heroicon-o-pencil')
                ->color('primary')
                ->tooltip('Editar este producto'),
        ];
    }
}
