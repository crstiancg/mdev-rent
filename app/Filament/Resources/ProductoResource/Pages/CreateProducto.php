<?php

namespace App\Filament\Resources\ProductoResource\Pages;

use App\Filament\Resources\ProductoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProducto extends CreateRecord
{
    protected static string $resource = ProductoResource::class;
    protected static ?string $title = 'Nuevo Producto';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('cancel')
                ->label('Cancelar')
                ->icon('heroicon-o-x-mark')
                ->color('secondary')
                ->url(ProductoResource::getUrl()),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Guardar')
                ->icon('heroicon-o-check')
                ->color('success')
                , // renombrar "Create"
            Actions\CreateAction::make('createAnother')
                ->label('Guardar y nuevo')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->createAnother(true),
            Actions\Action::make('cancel')
                ->label('Cancelar')
                ->icon('heroicon-o-x-mark')
                ->url($this->getResource()::getUrl('index'))
                ->color('warning'),
        ];
    }
    
}
