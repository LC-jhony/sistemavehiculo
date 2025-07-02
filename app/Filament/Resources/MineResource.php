<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MineResource\Pages;
use App\Filament\Resources\MineResource\RelationManagers;
use App\Models\Mine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MineResource extends Resource
{
    protected static ?string $model = Mine::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Minas';

    protected static ?string $modelLabel = 'Mina';

    protected static ?string $pluralModelLabel = 'Minas';

    protected static ?string $navigationGroup = 'Gestión de Minas';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make('Información de la Mina')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('location')
                            ->label('Ubicación')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Radio::make('status')
                            ->label('Estado')
                            ->boolean()
                            ->required()
                            ->inline()
                            ->inlineLabel(false)
                            ->default(true),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('Ubicación')
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->label('Estado')
                    ->boolean()
                    ->boolean(),
                Tables\Columns\TextColumn::make('assignments_count')
                    ->label('Conductores Asignados')
                    ->counts('activeAssignments')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Estado')
                    ->boolean()
                    ->trueLabel('Activas')
                    ->falseLabel('Inactivas')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListMines::route('/'),
            'create' => Pages\CreateMine::route('/create'),
            'edit' => Pages\EditMine::route('/{record}/edit'),
        ];
    }
}
