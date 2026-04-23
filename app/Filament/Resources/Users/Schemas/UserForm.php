<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\TextInput::make('name')
                    ->label(__('users.fields.name'))
                    ->required(),
                Components\Select::make('department_id')
                    ->options(\App\Models\Department::all()->pluck('name', 'id'))
                    ->label(__('users.fields.department_id'))
                    ->searchable()
                    ->preload(),
                Components\TextInput::make('email')
                    ->label(__('users.fields.email'))
                    ->email()
                    ->required(),
                Components\Select::make('role')
                    ->label(__('users.fields.role'))
                    ->options(\App\Enums\UserRole::class)
                    ->required(),
                Components\TextInput::make('password')
                    ->label(__('users.fields.password'))
                    ->visibleOn('create')
                    ->password()
                    ->revealable()
                    ->live()
                    ->required(),
                Components\TextInput::make('password_confirmation')
                    ->label(__('users.fields.password_confirmation'))
                    ->visibleOn('create')
                    ->password()
                    ->revealable()
                    ->live()
                    ->required()
                    ->same('password')
                    ->hint(function (Get $get) {
                        $password = $get('password');
                        $confirmation = $get('password_confirmation');

                        if (!$password || !$confirmation) {
                            return null;
                        }

                        return $password === $confirmation ? 'Password sama' : 'Password tidak sama';
                    })
                    ->hintColor(function (Get $get) {
                        $password = $get('password');
                        $confirmation = $get('password_confirmation');

                        if (!$password || !$confirmation) {
                            return null;
                        }

                        return $password === $confirmation ? 'success' : 'danger';
                    })
                    ->hintIcon(function (Get $get) {
                        $password = $get('password');
                        $confirmation = $get('password_confirmation');

                        if (!$password || !$confirmation) {
                            return null;
                        }

                        return $password === $confirmation ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle';
                    }),
            ]);
    }
}
