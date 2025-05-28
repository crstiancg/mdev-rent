<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventarioResource\Pages;
use App\Filament\Resources\InventarioResource\RelationManagers;
use App\Models\Categoria;
use App\Models\Inventario;
use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class InventarioResource extends Resource
{
    protected static ?string $model = Inventario::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Inventario y Control';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Inventarios';

    public static function getNavigationBadge(): ?string
    {
        return Inventario::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Inventario')
                    ->columns(2)
                    ->schema([
                        Select::make('categoria_id')
                        ->label('Seleccionar Categoría')
                        // ->relationship(name : 'country', titleAttribute:'name')
                        ->options(Categoria::all()->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->live()
                        // ->afterStateUpdated(fn (callable $set) => \dd($set('producto_id', null)))
                        ->afterStateUpdated(function (Set $set) {
                            $test = $set('producto_id', null);
                            // \dd($test);
                        })
                        ->required(),

                        Select::make('producto_id')
                        ->label('Seleccionar Producto')
                        ->options(fn (Get $get): Collection => Producto::query()
                            ->where('categoria_id', $get('categoria_id'))
                            ->pluck('nombre', 'id')
                        )->searchable()
                        ->preload()
                        ->live()
                        ->disabled(fn (Get $get) => !$get('categoria_id'))
                        // ->options(function (callable $get) {
                        //     $categoriaId = $get('categoria_id');
                        //     if (!$categoriaId) {
                        //         return Producto::pluck('nombre', 'id'); 
                        //     }

                        //     return Producto::where('categoria_id', $categoriaId)->pluck('nombre', 'id');
                        // })
                        ->required(),
                        
                        Forms\Components\TextInput::make('cantidad_total')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('cantidad_disponible')
                            ->required()
                            ->numeric()
                            ->default(0),
                            ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('producto.nombre')
                    ->label('Producto')
                    ->sortable(),
                Tables\Columns\TextColumn::make('producto.categoria.name')
                    ->label('Categoría')
                    ->sortable(),
                Tables\Columns\TextColumn::make('cantidad_total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cantidad_disponible')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('disponible')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventarios::route('/'),
            'create' => Pages\CreateInventario::route('/create'),
            'view' => Pages\ViewInventario::route('/{record}'),
            'edit' => Pages\EditInventario::route('/{record}/edit'),
        ];
    }
}
