<?php

namespace App\Filament\Resources\AlquilerResource\Pages;

use App\Filament\Resources\AlquilerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAlquiler extends ViewRecord
{
    protected static string $resource = AlquilerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function getRecord(): \Illuminate\Database\Eloquent\Model
    {
        return parent::getRecord()->loadMissing('cliente');
    }
}
