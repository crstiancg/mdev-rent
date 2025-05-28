<?php

namespace App\Filament\Resources\InventarioResource\Pages;

use App\Filament\Resources\InventarioResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditInventario extends EditRecord
{
    protected static string $resource = InventarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    // public function getRecord(): Model
    // {
    //     return parent::getRecord()->loadMissing('producto');
    // }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // \dd($data);
        $data['categoria_id'] = $this->record->producto->categoria_id ?? null;

        // \dd($this->record->producto);
        return $data;
    }
}
