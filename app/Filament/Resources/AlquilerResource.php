<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlquilerResource\Pages;
use App\Models\Alquiler;
use App\Models\Cliente;
use App\Models\Inventario;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use GuzzleHttp\Client;

class AlquilerResource extends Resource
{
    protected static ?string $model = Alquiler::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Alquileres y Ventas';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationLabel = 'Alquileres';

    public static function getNavigationBadge(): ?string
    {
        return Alquiler::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return Alquiler::count() > 10 ? 'warning' : 'primary';
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Alquiler')
                    ->columns(2)
                    ->schema([   
                Select::make('cliente_id')
                    ->label('Cliente')
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search) {
                        return Cliente::query()
                            ->where('dni', 'like', "%{$search}%")
                            ->orWhere('nombre', 'like', "%{$search}%")
                            ->orWhere('apellido_paterno', 'like', "%{$search}%")
                            ->orWhere('apellido_materno', 'like', "%{$search}%")
                            ->limit(10)
                            ->get()
                            ->mapWithKeys(fn ($cliente) => [
                                $cliente->id => "{$cliente->dni} - {$cliente->nombre} {$cliente->apellido_paterno} {$cliente->apellido_materno}",
                            ]);
                    })
                    ->getOptionLabelUsing(function ($value): ?string {
                        $cliente = Cliente::find($value);
                        return $cliente
                            ? "{$cliente->dni} - {$cliente->nombre} {$cliente->apellido_paterno} {$cliente->apellido_materno}"
                            : null;
                    })
                    ->createOptionForm([
                        Forms\Components\Section::make('Registrar Nuevo Cliente')
                        ->columns(2)
                        ->schema([
                        TextInput::make('dni')
                            ->required()
                            ->unique(Cliente::class, 'dni', ignorable: fn (?Cliente $record) => $record)
                            ->placeholder('Ingrese el DNI del cliente')
                            ->maxLength(8)
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('buscarDni')
                                    ->icon('heroicon-o-magnifying-glass')
                                    ->tooltip('Buscar datos por DNI')
                                    ->action(function (Get $get, Set $set) {
                                        $dni = $get('dni');

                                        if (strlen($dni) !== 8) {
                                            return Notification::make()
                                                ->title('DNI inválido')
                                                ->body('Debe contener exactamente 8 dígitos.')
                                                ->danger()
                                                ->send();
                                        }

                                        $persona = Cliente::where('dni', $dni)->first();

                                        if ($persona) {
                                            $set('nombre', $persona->nombre);
                                            $set('apellido_paterno', $persona->apellido_paterno);
                                            $set('apellido_materno', $persona->apellido_materno);
                                            $set('celular', $persona->celular);
                                            $set('correo', $persona->correo);

                                            return Notification::make()
                                                ->title('Persona encontrada')
                                                ->body('Los datos fueron cargados desde la base de datos.')
                                                ->success()
                                                ->send();
                                        }

                                        try {
                                            $client = new Client([
                                                'base_uri' => 'https://api.apis.net.pe',
                                                'verify' => false,
                                            ]);

                                            $res = $client->get('/v2/reniec/dni', [
                                                'headers' => [
                                                    'Authorization' => 'Bearer apis-token-12213.QvZkSOvaj1LtNqaRSjdIGEguBnF0kacY',
                                                    'Accept' => 'application/json',
                                                ],
                                                'query' => ['numero' => $dni],
                                            ]);

                                            $response = json_decode($res->getBody(), true);

                                            if (isset($response['numeroDocumento'])) {
                                                $set('nombre', $response['nombres']);
                                                $set('apellido_paterno', $response['apellidoPaterno']);
                                                $set('apellido_materno',  $response['apellidoMaterno']);

                                                Notification::make()
                                                    ->title('Datos encontrados')
                                                    ->body('Los datos del DNI fueron cargados correctamente.')
                                                    ->success()
                                                    ->send();
                                            } else {
                                                Notification::make()
                                                    ->title('No encontrado')
                                                    ->body('No se encontraron datos para este DNI.')
                                                    ->danger()
                                                    ->send();
                                            }
                                        } catch (\Exception $e) {
                                            Notification::make()
                                                ->title('Error al consultar')
                                                ->body('Ocurrió un error al consultar el servicio DNI.')
                                                ->danger()
                                                ->send();
                                        }
                                    })
                            ),
                        TextInput::make('nombre')->required(),
                        TextInput::make('apellido_paterno')->required(),
                        TextInput::make('apellido_materno')->required(),
                        TextInput::make('celular')->tel(9)->required()->maxLength(9),
                        TextInput::make('correo')->required(),
                    ]),
                    ])
                    ->createOptionUsing(fn (array $data) => Cliente::create($data)->getKey())
                    ->required(),

                DatePicker::make('fecha_alquiler')->label('Fecha de alquiler')->required(),
                DatePicker::make('fecha_entrega')->label('Fecha de entrega'),
                DatePicker::make('fecha_devolucion')->label('Fecha de devolución'),

                // TextInput::make('monto_total')->label('Monto total')
                // ->dehydrated(true) // evita que se sobrescriba desde el formulario
                // ->disabled()
                // ->numeric()
                // ->reactive()
                // ->visible(fn (string $context) => $context === 'edit'),
                Select::make('estado')
                ->label('Estado')
                ->options([
                    'pendiente' => 'Pendiente',
                    'entregado' => 'Entregado',
                    'devuelto' => 'Devuelto',
                    'cancelado' => 'Cancelado',
                ])->native(false)
                ->default('pendiente')->visible(fn (string $context) => $context === 'edit'),
                ]),

                // Repeater::make('inventario_id')
                //  ->lazy()
                //     ->schema([
                //         TextInput::make('cantidad')->required()
                //     ])
                //     ->visible(fn (string $context) => $context === 'edit'),
                Repeater::make('alquilerDetalles')
                    ->label('Productos alquilados')
                    ->relationship('alquilerDetalles')
                    ->collapsible()
                    // ->collapsed()
                    ->columnSpanFull()
                    // ->columnsrow(2)
                    ->schema([
                        Forms\Components\Section::make('Detalles del Producto')
                            ->columns(2)
                            ->schema([
                        Select::make('inventario_id')
                            ->label('Producto del inventario')
                            ->options(function () {
                                return Inventario::where('disponible', true)
                                    ->where('cantidad_disponible', '>', 0)
                                    ->get()
                                    ->mapWithKeys(fn ($inv) => [
                                        $inv->id => optional($inv->producto)->nombre ?? 'Sin nombre',
                                    ]);
                            })
                            ->getOptionLabelUsing(function ($value) {
                                    $inventario = Inventario::with('producto')->find($value);
                                    return $inventario ? $inventario->producto->nombre : 'Eliminado / no disponible';
                                })
                            ->searchable()
                            ->preload()
                            ->required()
                           ->dehydrated(true),
                        TextInput::make('cantidad')
                        ->label('Cantidad')
                        ->numeric()
                        ->required()
                        ->default(1)
                        ->reactive() // Necesario para actualizar el hint cuando cambia inventario_id
                        ->hint(function (callable $get) {
                            $inventarioId = $get('inventario_id');
                            if (!$inventarioId) return null;

                            $inventario = Inventario::find($inventarioId);
                            return $inventario
                                ? 'Máximo disponible: ' . $inventario->cantidad_disponible
                                : 'No disponible';
                        })
                        ->rule(function (callable $get) {

                            $detalles = $get('../../alquilerDetalles');

                            $totalesPorInventario = [];

                            foreach ($detalles as $detalle) {
                                $inventarioId = $detalle['inventario_id'];
                                $cantidad = is_numeric($detalle['cantidad']) ? $detalle['cantidad'] : 0;

                                if (!isset($totalesPorInventario[$inventarioId])) {
                                    $totalesPorInventario[$inventarioId] = [
                                        'inventario_id' => $inventarioId,
                                        'total' => 0
                                    ];
                                }

                                $totalesPorInventario[$inventarioId]['total'] += $cantidad;
                            }

                            // Esta es la función real de validación
                            return function ($attribute, $value, $fail) use ($get, $totalesPorInventario) {

                                $currentInventarioId = $get('inventario_id');
                                if (!$currentInventarioId) return;

                                $inventario = \App\Models\Inventario::find($currentInventarioId);
                                if (!$inventario) return;

                                $stockTotal = $inventario->cantidad_total;

                                // Obtener el total solicitado para el inventario actual
                                $totalSolicitado = $totalesPorInventario[$currentInventarioId]['total'] ?? 0;

                                if ($totalSolicitado > $stockTotal) {
                                    $fail("El total solicitado del producto \"{$inventario->producto->nombre}\" excede el stock total registrado: {$stockTotal} unidades.");
                                }
                            };
                        }),
                        TextInput::make('precio_alquiler')
                            ->numeric()
                            ->required()
                            ->default(0),

                        TextInput::make('total')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->disabled()
                            ->dehydrated()
                            ->afterStateHydrated(fn ($state, callable $set, callable $get) => $set('total', $get('cantidad') * $get('precio_alquiler')))
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set, callable $get) => $set('total', $get('cantidad') * $get('precio_alquiler'))),
                    ])
                    ])->deleteAction(
                        fn (Action $action) => $action->requiresConfirmation()->label('Eliminar producto')
                            ->modalHeading('Eliminar producto del alquiler restaura la cantidad disponible de lo eliminado.')
                            ->color('danger')
                            ->icon('heroicon-o-trash')
                            ->modalDescription('¿Estás seguro de eliminar este producto del alquiler? Esta acción no se puede deshacer.'),
                    )
                    ->defaultItems(1)
                    ->createItemButtonLabel('Agregar producto')
                    ->visible(fn (string $context) => $context === 'edit')
                    ,
            ])
            ->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cliente.dni')->sortable(),
                Tables\Columns\TextColumn::make('cliente.nombre_completo')->sortable(),
                Tables\Columns\TextColumn::make('fecha_alquiler')->date()->sortable(),
                Tables\Columns\TextColumn::make('monto_total')->badge()
                    ->formatStateUsing(fn ($state) => number_format($state, 2, '.', ','))
                    ->icon('heroicon-o-currency-dollar'),
                Tables\Columns\TextColumn::make('fecha_entrega')->date()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('fecha_devolucion')->date()->sortable()->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\SelectColumn::make('estado')->options([
                //     'pendiente' => 'Pendiente',
                //     'entregado' => 'Entregado',
                //     'devuelto' => 'Devuelto',
                //     'cancelado' => 'Cancelado',
                // ])->rules(['required'])->selectablePlaceholder(false),
                Tables\Columns\TextColumn::make('estado')->badge()
                    ->colors([
                        'primary' => 'pendiente',
                        'success' => 'entregado',
                        'warning' => 'devuelto',
                        'danger' => 'cancelado',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pendiente' => 'Pendiente',
                        'entregado' => 'Entregado',
                        'devuelto' => 'Devuelto',
                        'cancelado' => 'Cancelado',
                        default => 'Desconocido',
                    })->icon(fn ($state) => match ($state) {
                        'pendiente' => 'heroicon-o-clock',
                        'entregado' => 'heroicon-o-check-circle',
                        'devuelto' => 'heroicon-o-arrow-right-circle',
                        'cancelado' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    }),
                // Tables\Columns\TextColumn::make('id')->counts('alquiler_id')
                //     ->label('Artículos registrados'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->label('Ver')
                    ->tooltip('Ver detalles del alquiler'),
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->label('Registrar')
                    ->tooltip('Registrar artículos para este alquiler'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAlquilers::route('/'),
            // 'create' => Pages\CreateAlquiler::route('/create'),
            'view' => Pages\ViewAlquiler::route('/{record}'),
            'edit' => Pages\EditAlquiler::route('/{record}/edit'),
        ];
    }

}