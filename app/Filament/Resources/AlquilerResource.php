<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlquilerResource\Pages;
use App\Filament\Resources\AlquilerResource\RelationManagers;
use App\Models\Alquiler;
use App\Models\Cliente;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AlquilerResource extends Resource
{
    protected static ?string $model = Alquiler::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

public static function form(Form $form): Form
{
    return $form
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
                    TextInput::make('dni')
                        ->required()
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
                                $token = 'apis-token-12213.QvZkSOvaj1LtNqaRSjdIGEguBnF0kacY';
                                $client = new Client([
                                    'base_uri' => 'https://api.apis.net.pe',
                                    'verify' => false,
                                ]);

                                $parameters = [
                                    'http_errors' => false,
                                    'connect_timeout' => 5,
                                    'headers' => [
                                        'Authorization' => 'Bearer ' . $token,
                                        'Referer' => 'https://apis.net.pe/getDni',
                                        'User-Agent' => 'laravel/guzzle',
                                        'Accept' => 'application/json',
                                    ],
                                    'query' => ['numero' => $dni],
                                ];

                                $res = $client->request('GET', '/v2/reniec/dni', $parameters);
                                $response = json_decode($res->getBody()->getContents(), true);

                                if (isset($response['numeroDocumento'])) {
                                    $set('nombre', $response['nombres']);
                                    $set('apellido_paterno', $response['apellidoPaterno']);
                                    $set('apellido_materno', $response['apellidoMaterno']);

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
                    TextInput::make('celular')->required(),
                    TextInput::make('correo')->required(),
                ])
                ->createOptionUsing(function (array $data) {
                    return Cliente::create($data)->getKey();
                })
                ->afterStateUpdated(function (Set $set, $state) {
                    $set('cliente_id', $state);
                })
                ->required(),

            DatePicker::make('fecha_alquiler')
                ->label('Fecha de alquiler')
                ->required(),

            DatePicker::make('fecha_entrega')
                ->label('Fecha de entrega'),

            DatePicker::make('fecha_devolucion')
                ->label('Fecha de devolución'),

            TextInput::make('monto_total')
                ->label('Monto total')
                ->required()
                ->numeric(),

            TextInput::make('estado')
                ->default('pendiente')
                ->required(),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cliente.dni')
                    ->sortable(),
                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_alquiler')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_entrega')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_devolucion')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('monto_total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado')
                    ->searchable(),
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
            'index' => Pages\ListAlquilers::route('/'),
            'create' => Pages\CreateAlquiler::route('/create'),
            'view' => Pages\ViewAlquiler::route('/{record}'),
            'edit' => Pages\EditAlquiler::route('/{record}/edit'),
        ];
    }
}
