<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\DriverMineAssignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DriverMineAssignmentResource\Pages;
use App\Filament\Resources\DriverMineAssignmentResource\RelationManagers;

class DriverMineAssignmentResource extends Resource
{
    protected static ?string $model = DriverMineAssignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Asignaciones';

    protected static ?string $modelLabel = 'Asignación';

    protected static ?string $pluralModelLabel = 'Asignaciones de Conductores';

    protected static ?string $navigationGroup = 'Gestión de Minas';
    public static function form(Form $form): Form
    {
        return $form

            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Section::make('Información de Asignación')
                            ->schema([
                                Forms\Components\Select::make('driver_id')
                                    ->label('Conductor')
                                    ->relationship('driver')
                                    ->getOptionLabelFromRecordUsing(fn($record) => $record->full_name . ' - ' . $record->dni)
                                    ->searchable(['name', 'last_paternal_name', 'last_maternal_name', 'dni'])
                                    ->required()
                                    ->preload()
                                    ->native(false),
                                Forms\Components\Select::make('mine_id')
                                    ->label('Mina')
                                    ->relationship('mine', 'name')
                                    ->searchable()
                                    ->required()
                                    ->preload()
                                    ->native(false),
                            ])
                            ->columns(2),
                        Forms\Components\Section::make('Período de Asignación')
                            ->schema([

                                Forms\Components\Select::make('month')
                                    ->label('Mes')
                                    ->options([
                                        1 => 'Enero',
                                        2 => 'Febrero',
                                        3 => 'Marzo',
                                        4 => 'Abril',
                                        5 => 'Mayo',
                                        6 => 'Junio',
                                        7 => 'Julio',
                                        8 => 'Agosto',
                                        9 => 'Septiembre',
                                        10 => 'Octubre',
                                        11 => 'Noviembre',
                                        12 => 'Diciembre'
                                    ])
                                    ->default(date('n'))
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        self::updateDates($set, $get);
                                    }),
                                Forms\Components\Select::make('year')
                                    ->label('Año')
                                    ->options(function () {
                                        $currentYear = date('Y');
                                        $years = [];
                                        for ($i = $currentYear - 1; $i <= $currentYear + 2; $i++) {
                                            $years[$i] = $i;
                                        }
                                        return $years;
                                    })
                                    ->default(date('Y'))
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        self::updateDates($set, $get);
                                    }),
                            ])
                            ->columns(2),
                        Forms\Components\Section::make('Fechas')
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('Fecha de Inicio')
                                    ->required()
                                    ->default(now()
                                        ->startOfMonth())
                                    ->native(false),
                                Forms\Components\DatePicker::make('end_date')
                                    ->label('Fecha de Fin')
                                    ->required()
                                    ->default(now()
                                        ->endOfMonth())
                                    ->native(false),
                            ])
                            ->columns(2),
                    ])
                    ->columns(3),
                Forms\Components\Section::make('Estado y Notas')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'Activo' => 'Activo',
                                'Completedo' => 'Completado',
                                'Cancelado' => 'Cancelado',
                            ])
                            ->default('active')
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notas')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

            ]);
    }
    protected static function updateDates(Set $set, Get $get): void
    {
        $year = $get('year');
        $month = $get('month');

        if ($year && $month) {
            $startDate = Carbon::create($year, $month, 1);
            $endDate = $startDate->copy()->endOfMonth();

            $set('start_date', $startDate->format('Y-m-d'));
            $set('end_date', $endDate->format('Y-m-d'));
        }
    }
    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->paginated([5, 10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(5)
            ->searchable()
            ->columns([
                // Tables\Columns\TextColumn::make('driver.name')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('driver.full_name')
                    ->label('Conductor')
                    ->searchable(['drivers.name', 'drivers.last_paternal_name', 'drivers.last_maternal_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('mine.name')
                    ->label('Mina')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('period')
                    ->label('Período')
                    ->sortable(['year', 'month']),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Fecha Inicio')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fecha Fin')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'active' => 'Activo',
                        'completed' => 'Completado',
                        'cancelled' => 'Cancelado',
                        default => $state,
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Vigente')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
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
                Tables\Filters\SelectFilter::make('mine_id')
                    ->label('Mina')
                    ->relationship('mine', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'Activo',
                        'completed' => 'Completado',
                        'cancelled' => 'Cancelado',
                    ]),

                Tables\Filters\SelectFilter::make('year')
                    ->label('Año')
                    ->options(function () {
                        $currentYear = date('Y');
                        $years = [];
                        for ($i = $currentYear - 2; $i <= $currentYear + 1; $i++) {
                            $years[$i] = $i;
                        }
                        return $years;
                    }),

                Tables\Filters\SelectFilter::make('month')
                    ->label('Mes')
                    ->options([
                        1 => 'Enero',
                        2 => 'Febrero',
                        3 => 'Marzo',
                        4 => 'Abril',
                        5 => 'Mayo',
                        6 => 'Junio',
                        7 => 'Julio',
                        8 => 'Agosto',
                        9 => 'Septiembre',
                        10 => 'Octubre',
                        11 => 'Noviembre',
                        12 => 'Diciembre'
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('complete')
                    ->label('Completar')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn(DriverMineAssignment $record) => $record->status === 'Activo')
                    ->requiresConfirmation()
                    ->action(fn(DriverMineAssignment $record) => $record->update(['status' => 'Completedo'])),

                Tables\Actions\Action::make('cancel')
                    ->label('Cancelar')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn(DriverMineAssignment $record) => $record->status === 'Activo')
                    ->requiresConfirmation()
                    ->action(fn(DriverMineAssignment $record) => $record->update(['status' => 'Cancelado'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('complete_selected')
                        ->label('Completar Seleccionados')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                if ($record->status === 'Activo') {
                                    $record->update(['status' => 'Completedo']);
                                }
                            });
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListDriverMineAssignments::route('/'),
            'create' => Pages\CreateDriverMineAssignment::route('/create'),
            'view' => Pages\ViewDriverMineAssignment::route('/{record}'),
            'edit' => Pages\EditDriverMineAssignment::route('/{record}/edit'),
        ];
    }
}
