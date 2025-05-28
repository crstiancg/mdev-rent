<?php

namespace App\Filament\Resources\InventarioResource\Pages;

use App\Filament\Resources\InventarioResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateInventario extends CreateRecord
{
    protected static string $resource = InventarioResource::class;

    // protected function getFormActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make()
    //             ->label('Guardar')
    //             ->icon('heroicon-o-check')
    //             ->color('success')
    //             , // renombrar "Create"
    //         Actions\CreateAction::make('createAnother')
    //             ->label('Guardar y nuevo')
    //             ->icon('heroicon-o-plus')
    //             ->color('primary')
    //             ->createAnother(true),
    //         Actions\Action::make('cancel')
    //             ->label('Cancelar')
    //             ->icon('heroicon-o-x-mark')
    //             ->url($this->getResource()::getUrl('index'))
    //             ->color('warning'),
    //     ];
    // }
}
