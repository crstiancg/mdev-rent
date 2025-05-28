<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoResource\Pages;
use App\Filament\Resources\ProductoResource\RelationManagers;
use App\Models\Producto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationGroup = 'Inventario y Control';
    protected static ?int $navigationSort = 2;
    // protected static ?string $slug = 'inventario/productos';
    protected static ?string $navigationLabel = 'Artículos';
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('categoria_id')
                    ->relationship('categoria', 'name')
                    ->label('Categoría')
                    ->searchable()
                    ->preload()
                    ->placeholder('Selecciona una categoría')
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre de la categoría')
                            ->required()
                            ->maxLength(255),
                    ]),
                Forms\Components\Textarea::make('descripcion')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('precio_venta')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('precio_alquiler')
                    ->required()
                    ->numeric(),
                Forms\Components\FileUpload::make('imagen')
                    ->acceptedFileTypes(['image/*'])
                    ->label('Imagen')
                    ->image()
                    ->maxSize(1024)
                    ->directory('productos')
                    ->preserveFilenames()
                    ->columnSpanFull()
                    ->helperText('Sube una imagen para el producto.')
                    ->required(),
                Forms\Components\Toggle::make('estado')
                    ->required(),
                Forms\Components\Toggle::make('disponible')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('precio_venta')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('precio_alquiler')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('categoria.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('imagen')
                    ->label('Imagen')
                    ->circular(),
                Tables\Columns\IconColumn::make('estado')
                    ->boolean(),
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
                Tables\Filters\Filter::make('estado')
                    ->query(fn (Builder $query) => $query->where('estado', true))
                    ->label('Estado Activo'),
                SelectFilter::make('categoria_id')
                    ->relationship('categoria', 'name', function ($query) {
                        // dd($query); 
                        return $query->orderBy('name');
                    })
                    ->label('Categoría'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver')
                    ->icon('heroicon-o-eye'),
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary'),
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
            'index' => Pages\ListProductos::route('/'),
            'create' => Pages\CreateProducto::route('/create'),
            'view' => Pages\ViewProducto::route('/{record}'),
            'edit' => Pages\EditProducto::route('/{record}/edit'),
        ];
    }
    
}
