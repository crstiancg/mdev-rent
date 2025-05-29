<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClienteResource\Pages;
use App\Filament\Resources\ClienteResource\RelationManagers;
use App\Models\Cliente;
use Filament\Forms;
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

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Clientes y Proveedores';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationLabel = 'Clientes';

    public static function getNavigationBadge(): ?string
    {
        return Cliente::count();
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            TextInput::make('dni')
                ->label('DNI')
                ->unique(Cliente::class, 'dni', ignorable: fn (?Cliente $record) => $record)
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

                TextInput::make('nombre')
                    ->label('Nombres')
                    ->required()
                    ->maxLength(255),

                TextInput::make('apellido_paterno')
                    ->label('Apellido Paterno')
                    ->required()
                    ->maxLength(255),

                TextInput::make('apellido_materno')
                    ->label('Apellido Materno')
                    ->required()
                    ->maxLength(255),

                TextInput::make('celular')
                    ->label('Celular')
                    ->tel(9)
                    ->required()
                    ->maxLength(9)
                    ->helperText('Debe contener exactamente 9 dígitos.'),

                TextInput::make('correo')
                    ->label('Correo electrónico')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('dni')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('apellido_paterno')
                    ->searchable(),
                Tables\Columns\TextColumn::make('apellido_materno')
                    ->searchable(),
                Tables\Columns\TextColumn::make('celular')
                    ->searchable(),
                Tables\Columns\TextColumn::make('correo')
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    // ->requiresConfirmation()
                    ->action(function (Cliente $cliente) {
                        if ($cliente->alquileres()->exists()) {
                            Notification::make()
                                ->title('No se puede eliminar')
                                ->body('Este cliente tiene alquileres asociados.')
                                ->danger()
                                ->send();
                            return;
                        }
                        $cliente->delete();
                        Notification::make()
                            ->title('Cliente eliminado')
                            ->body('El cliente ha sido eliminado correctamente.')
                            ->success()
                            ->send();
                        // \dd($cliente);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageClientes::route('/'),
        ];
    }
}
