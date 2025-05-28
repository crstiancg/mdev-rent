<?php

namespace App\Filament\Resources\AlquilerResource\Pages;

use App\Filament\Resources\AlquilerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAlquilers extends ListRecords
{
    protected static string $resource = AlquilerResource::class;
    protected static ?string $title = 'Alquileres y Reservas';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Alquiler')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->tooltip('Crear nuevo alquiler'),
        ];
    }
        
}
