<?php

namespace App\Filament\Resources;

use App\Enums\GuardNames;
use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers\RolesRelationManager;
use App\Models\Permission;
use App\Models\Role;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static bool $isScopedToTenant = false;

    public function isReadOnly(): bool
    {
        return false;
    }

    protected static ?string $navigationGroup = 'Roles & Permissions';

    protected static ?string $navigationIcon = 'heroicon-o-key';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->columns(2)->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('guard_name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('roles')
                        ->relationship(
                            name: 'roles',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn ($query) => $query->whereBelongsTo(Filament::getTenant())
                        )
                        ->multiple()
                        ->preload()
                        ->searchable(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('guard_name'),
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
                Tables\Filters\SelectFilter::make('guard_name')
                    ->options(GuardNames::options()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('Attach to role(s)')
                        ->action(function (Collection $records, array $data): void {
                            Role::where('organization_id', Filament::getTenant()->id)
                                ->whereIn('id', $data['roles'])->each(function (Role $role) use ($records): void {
                                    $records->each(fn (Permission $permission) => $role->permissions()->syncWithoutDetaching($permission));
                                });

                            Notification::make()
                                ->title('Saved successfully attached Permissions to Roles.')
                                ->success()
                                ->send();
                        })
                        ->form([
                            Select::make('roles')
                                ->multiple()
                                ->options(Role::query()->where('organization_id', Filament::getTenant()->id)->pluck('name', 'id'))
                                ->required(),
                        ])
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RolesRelationManager::make(),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'view' => Pages\ViewPermission::route('/{record}'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }
}
