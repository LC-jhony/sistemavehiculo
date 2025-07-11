<?php

namespace App\Filament\Resources;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\DriverLicenseResource\Pages;
use App\Models\Driver;
use App\Models\DriverLicense;
use App\Tables\Columns\StatusColumn;
use Asmit\FilamentUpload\Forms\Components\AdvancedFileUpload;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Hugomyb\FilamentMediaAction\Tables\Actions\MediaAction;

class DriverLicenseResource extends Resource
{
    protected static ?string $model = DriverLicense::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $modelLabel = 'Licencia';
    protected static ?string $navigationGroup = 'Gestión de Personal';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos del Conductor')
                    ->description('Datos generales del chofer y documnetos')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->columns(4)
                            ->schema([
                                Forms\Components\Card::make('Datos Personales')
                                    ->description('Datos generales del chofer y documnetos')
                                    ->columnSpan(1)
                                    ->schema([
                                        Forms\Components\Select::make('driver_id')
                                            ->label('Chofer')
                                            ->searchable()
                                            ->options(Driver::all()->pluck('name', 'id'))
                                            ->getSearchResultsUsing(fn(string $search): array => Driver::where('dni', 'like', "%{$search}%")->limit(50)->get()->mapWithKeys(function ($driver) {
                                                return [$driver->id => "{$driver->name} {$driver->last_paternal_name} {$driver->last_maternal_name}"];
                                            })->toArray())
                                            ->getOptionLabelsUsing(fn(array $values): array => Driver::whereIn('id', $values)->get()->mapWithKeys(function ($driver) {
                                                return [$driver->id => "{$driver->name} {$driver->last_paternal_name} {$driver->last_maternal_name}"];
                                            })->toArray())
                                            ->required(),
                                        Forms\Components\TextInput::make('license_number')
                                            ->label('Número de Licencia')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\DatePicker::make('expiration_date')
                                            ->label('Fecha de Vencimiento')
                                            ->required()
                                            ->native(false),
                                        Forms\Components\TextInput::make('license_type')
                                            ->required()
                                            ->maxLength(255),
                                    ]),
                                Forms\Components\Card::make('Documentos del Chofer')
                                    ->columnSpan(3)
                                    ->schema([
                                        AdvancedFileUpload::make('file')
                                            ->label('Documento')
                                            // ->multiple()
                                            ->columnSpanFull()
                                            ->visibility('public')
                                            ->directory('Licencias')
                                            ->default(null)
                                            ->acceptedFileTypes(['application/pdf']),
                                    ]),
                            ]),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->paginated([5, 10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(10)
            ->searchable()
            ->columns([
                Tables\Columns\TextColumn::make('driver.full_name')
                    ->label('Chofer')
                    ->getStateUsing(fn($record) => $record->driver->name . ' ' . $record->driver->last_paternal_name . ' ' . $record->driver->last_maternal_name)
                    ->searchable(['drivers.name', 'drivers.last_paternal_name', 'drivers.last_maternal_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('license_number')
                    ->label('Número de Licencia')
                    ->searchable(),
                Tables\Columns\TextColumn::make('expiration_date')
                    ->label('Fecha de Vencimiento')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('license_type')
                    ->label('Tipo de Licencia')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status_label')
                    ->label('Estado')
                    ->getStateUsing(function ($record) {
                        $expirationDate = \Carbon\Carbon::parse($record->expiration_date);
                        $today = now();

                        return match (true) {
                            $expirationDate->isPast() => 'Vencido',
                            $expirationDate->diffInDays($today) <= 7 && $expirationDate->isFuture() => 'Crítico',
                            $expirationDate->diffInDays($today) <= 30 && $expirationDate->isFuture() => 'Por vencer',
                            default => 'Vigente'
                        };
                    })
                    ->badge()
                    ->color(function ($state) {
                        return match ($state) {
                            'Vigente' => 'success',
                            'Por vencer' => 'warning',
                            'Crítico' => 'gray',
                            'Vencido' => 'danger',
                            default => 'success',
                        };
                    })
                    ->sortable(),
                // StatusColumn::make('status')
                //     ->label('Estado de Licencia'),
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
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'vigente' => 'Vigente',
                        'por-vencer' => 'Por vencer',
                        'critico' => 'Crítico',
                        'vencido' => 'Vencido',
                    ])
                    ->query(function ($query, array $data) {
                        if (! $data['value']) {
                            return $query;
                        }

                        $today = now();

                        return match ($data['value']) {
                            'vigente' => $query->where('expiration_date', '>', $today->copy()->addDays(30)),
                            'por-vencer' => $query->whereBetween('expiration_date', [$today->copy()->addDays(8), $today->copy()->addDays(30)]),
                            'critico' => $query->whereBetween('expiration_date', [$today, $today->copy()->addDays(7)]),
                            'vencido' => $query->where('expiration_date', '<', $today),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                MediaAction::make('pdf')
                    ->label('')
                    ->media(fn($record) => $record->file ? asset('storage/' . $record->file) : null)
                    // ->iconButton()
                    ->icon('bi-file-pdf-fill')
                    ->color('danger')
                    ->visible(fn($record) => !empty($record->file) && auth()->user()->hasAnyRole(['super_admin', 'admin', 'user'])),
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    FilamentExportBulkAction::make('export')
                        ->label('Exportar Licencias') // Título del botón
                        ->defaultPageOrientation('landscape')
                        // ->pageOrientationFieldLabel('Page Orientation')
                        ->defaultFormat('xlsx')
                        ->formatStates([
                            'status' => [
                                'vigente' => 'Vigente',
                                'por-vencer' => 'Por vencer',
                                'vencido' => 'Vencido',
                            ],
                        ]),

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
            'index' => Pages\ListDriverLicenses::route('/'),
            'create' => Pages\CreateDriverLicense::route('/create'),
            'view' => Pages\ViewDriverLicense::route('/{record}'),
            'edit' => Pages\EditDriverLicense::route('/{record}/edit'),
        ];
    }
}
