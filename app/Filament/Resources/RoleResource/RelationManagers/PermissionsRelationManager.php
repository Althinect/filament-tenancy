<?php

namespace App\Filament\Resources\RoleResource\RelationManagers;

use App\Enums\GuardNames;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PermissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'permissions';

    public function isReadOnly(): bool
    {
        return false;
    }

    protected function canCreate(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->searchable()
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('guard_name'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('guard_name')
                    ->options(GuardNames::options()),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
