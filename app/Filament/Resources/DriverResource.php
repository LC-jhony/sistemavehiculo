<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DriverResource\Pages;
use App\Models\Cargo;
use App\Models\Driver;
use Asmit\FilamentUpload\Forms\Components\AdvancedFileUpload;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Hugomyb\FilamentMediaAction\Tables\Actions\MediaAction;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Conductore';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos del Chofer')
                    ->description('Datos generales del chofer y documnetos')
                    ->icon('heroicon-o-users')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make()
                            ->columns(4)
                            ->schema([
                                Forms\Components\Card::make('Datos Personales')
                                    ->columnSpan(1)
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nombre')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('last_paternal_name')
                                            ->label('Apellido Paterno')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('last_maternal_name')
                                            ->label('Apellido Materno')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('dni')
                                            ->label('DNI')
                                            ->required()
                                            ->numeric(),
                                        Forms\Components\Select::make('cargo_id')
                                            ->label('Cargo')
                                            ->options(Cargo::all()->pluck('name', 'id'))
                                            ->searchable('name')
                                            ->required()
                                            ->native(false),
                                    ]),

                                Forms\Components\Grid::make('Documento')
                                    ->columnSpan(3)
                                    ->schema([
                                        Forms\Components\Grid::make()
                                            ->schema([
                                                // Forms\Components\FileUpload::make('file')
                                                AdvancedFileUpload::make('file')
                                                    ->label('Documento')
                                                    ->default(null)
                                                    ->columnSpanFull()
                                                    ->visibility('public')
                                                    ->directory('documents')
                                                    ->acceptedFileTypes(['application/pdf']),
                                            ]),

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
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nombre Completo')
                    ->getStateUsing(fn ($record) => $record->name.' '.$record->last_paternal_name.' '.$record->last_maternal_name)
                    ->searchable(['name', 'last_paternal_name', 'last_maternal_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('dni')
                    ->label('DNI')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cargo.name')
                    ->label('Cargo')
                    ->badge()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('file')
                //     ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->label('Estado')
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
                MediaAction::make('pdf')
                    ->label('')
                    ->media(fn ($record) => $record->file ? asset('storage/'.$record->file) : null)
                    // ->iconButton()
                    ->icon('bi-file-pdf-fill')
                    ->color('danger')
                    ->visible(fn ($record) => ! empty($record->file)),
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
            'index' => Pages\ListDrivers::route('/'),
            'create' => Pages\CreateDriver::route('/create'),
            'view' => Pages\ViewDriver::route('/{record}'),
            'edit' => Pages\EditDriver::route('/{record}/edit'),
        ];
    }
}
