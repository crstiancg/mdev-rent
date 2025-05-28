<?php

namespace App\Filament\Resources\AlquilerResource\Pages;

use App\Filament\Resources\AlquilerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAlquiler extends ViewRecord
{
    protected static string $resource = AlquilerResource::class;
    protected static ?string $title = 'Información Alquileres y Reservas';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->label('Editar')
                ->icon('heroicon-o-pencil-square')
                ->color('primary')
                ->tooltip('Editar Alquiler'),
        ];
    }

    public function getRecord(): \Illuminate\Database\Eloquent\Model
    {
        return parent::getRecord()->loadMissing('cliente');
    }
}
