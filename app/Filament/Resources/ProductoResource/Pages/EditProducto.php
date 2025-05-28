<?php

namespace App\Filament\Resources\ProductoResource\Pages;

use App\Filament\Resources\ProductoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProducto extends EditRecord
{
    protected static string $resource = ProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('Ver Producto')
                ->icon('heroicon-o-eye')
                ->color('primary')
                ->tooltip('Ver este producto'),
            Actions\DeleteAction::make()
                ->label('Eliminar Producto')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->tooltip('Eliminar este producto'),
        ];
    }
}
