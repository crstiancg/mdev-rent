<?php

namespace App\Filament\Resources\AlquilerResource\Pages;

use App\Filament\Resources\AlquilerResource;
use App\Models\Alquiler;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditAlquiler extends EditRecord
{
    protected static string $resource = AlquilerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    // // public function getRecord(): Alquiler
    // // {
    // //     return parent::getRecord()->load('cliente');
    // // }

    
    public function getRecord(): Model
    {
        return parent::getRecord()->loadMissing('cliente');
    }


}
