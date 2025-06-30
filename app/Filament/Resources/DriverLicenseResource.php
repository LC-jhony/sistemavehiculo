<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Driver;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\DriverLicense;
use Filament\Resources\Resource;
use App\Tables\Columns\StatusColumn;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DriverLicenseResource\Pages;
use Hugomyb\FilamentMediaAction\Tables\Actions\MediaAction;
use Asmit\FilamentUpload\Forms\Components\AdvancedFileUpload;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class DriverLicenseResource extends Resource
{
    protected static ?string $model = DriverLicense::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Licencia';
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
                StatusColumn::make('status')
                    ->label('Estado de Licencia'),
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
                        'vencido' => 'Vencido',
                    ])
                    ->query(function ($query, array $data) {
                        if (!$data['value']) {
                            return $query;
                        }

                        $today = now();

                        return match ($data['value']) {
                            'vigente' => $query->where('expiration_date', '>', $today->copy()->addDays(30)),
                            'por-vencer' => $query->whereBetween('expiration_date', [$today, $today->copy()->addDays(30)]),
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
                    ->visible(fn($record) => ! empty($record->file)),
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
                        ->defaultPageOrientation('landscape')
                        ->pageOrientationFieldLabel('Page Orientation')

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
