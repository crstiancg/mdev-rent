<?php

namespace App\Filament\Resources\AlquilerResource\Pages;

use App\Filament\Resources\AlquilerResource;
use App\Models\Alquiler;
use Filament\Actions;
use Filament\Forms\Components\Builder;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditAlquiler extends EditRecord
{
    protected static string $resource = AlquilerResource::class;
    protected static ?string $title = 'Registrar Articulo Alquilado';
    
    protected function getHeaderActions(): array
    {
        return [
            // Actions\ViewAction::make(),
            // Actions\DeleteAction::make(),
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

    // protected function afterValidate(): void
    // {
    //     $agrupados = collect($this->data['alquilerDetalles'] ?? [])
    //         ->groupBy('inventario_id')
    //         ->map(fn ($items) => $items->sum('cantidad'));

    //     foreach ($agrupados as $inventarioId => $cantidadTotal) {
    //         $inventario = \App\Models\Inventario::find($inventarioId);
    //         $disponible = $inventario?->cantidad_disponible ?? 0;

    //         if ($cantidadTotal > $disponible) {
    //             $this->addError(
    //                 'alquilerDetalles',
    //                 "El producto \"{$inventario->producto->nombre}\" excede el stock disponible de {$disponible}."
    //             );
    //         }
    //     }
    // }

    protected function afterValidate(): void
    {
        $agrupados = collect($this->data['alquilerDetalles'] ?? [])
            ->groupBy('inventario_id')
            ->map(fn ($items) => $items->sum('cantidad'));

        foreach ($agrupados as $inventarioId => $cantidadTotal) {
            $inventario = \App\Models\Inventario::find($inventarioId);
            $disponible = $inventario?->cantidad_disponible ?? 0;

            if ($cantidadTotal > $disponible) {
                $this->addError(
                    'alquilerDetalles',
                    "El producto \"{$inventario->producto->nombre}\" excede el stock disponible de {$disponible}."
                );
            }
        }
    }

    protected static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('alquilerDetalles.inventario.producto');
    }
    

     protected function getFormActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Guardar')
                ->icon('heroicon-o-check')
                ->color('success')
                , // renombrar "Create"
            // Actions\CreateAction::make('createAnother')
            //     ->label('Guardar y nuevo')
            //     ->icon('heroicon-o-plus')
            //     ->color('primary')
            //     ->createAnother(true),
            Actions\Action::make('cancel')
                ->label('Cancelar')
                ->icon('heroicon-o-x-mark')
                ->url($this->getResource()::getUrl('index'))
                ->color('warning'),
        ];
    }



}
