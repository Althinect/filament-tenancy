<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->columns(2)->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\DateTimePicker::make('email_verified_at')
                        ->visibleOn(['view']),
                    Forms\Components\TextInput::make('password')
                        ->visibleOn(['create', 'edit'])
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context): bool => $context === 'create'),
                    Forms\Components\Select::make('roles')
                        ->relationship(
                            name: 'roles',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn ($query) => $query->whereBelongsTo(Filament::getTenant())
                        )
                        ->saveRelationshipsUsing(function (User $record, $state) {
                            $record->roles()->syncWithPivotValues($state, [
                                'model_type' => User::class,
                                'organization_id' => Filament::getTenant()->id,
                            ]);
                        })
                        ->multiple()
                        ->preload()
                        ->searchable(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                if (Auth::user()->isSuperAdmin()) {
                    return $query;
                }

                return $query->nonSuperAdmins();
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
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
            ])
            ->actions([
                Impersonate::make()->visible(fn () => Auth::user()->isSuperAdmin()),
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

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
