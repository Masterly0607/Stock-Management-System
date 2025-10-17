<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Support\Concerns\HasCrudPermissions;
use App\Models\User;
use App\Models\Province;
use App\Models\District;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    use HasCrudPermissions;

    protected static ?string $model = User::class;
    protected static string $permPrefix = 'users';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Name')
                ->required()
                ->maxLength(191),

            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true),

            TextInput::make('password')
                ->label('Password')
                ->password()
                ->required(fn($operation) => $operation === 'create')
                ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                ->dehydrated(fn($state) => filled($state)),

            // Single-role flow (not a DB column)
            Select::make('role')
                ->label('Role')
                ->options([
                    'Super Admin' => 'Super Admin',
                    'Admin'       => 'Admin',
                    'Distributor' => 'Distributor',
                ])
                ->reactive()
                ->required(fn($operation) => $operation === 'create') // optional on edit
                ->dehydrated(false),

            // Province picker (only for Admin / Distributor)
            Select::make('province_id')
                ->label('Province')
                ->options(fn() => Province::query()->orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->reactive()
                ->visible(fn(callable $get) => in_array($get('role'), ['Admin', 'Distributor'], true))
                ->required(fn(callable $get) => in_array($get('role'), ['Admin', 'Distributor'], true))
                ->dehydrated(false),

            // District picker (only for Distributor)
            Select::make('district_id')
                ->label('District')
                ->options(
                    fn(callable $get) =>
                    $get('province_id')
                        ? District::where('province_id', $get('province_id'))->orderBy('name')->pluck('name', 'id')
                        : collect()
                )
                ->searchable()
                ->reactive()
                ->visible(fn(callable $get) => $get('role') === 'Distributor')
                ->required(fn(callable $get) => $get('role') === 'Distributor')
                ->dehydrated(false),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('roles.0.name')->label('Role'),
                Tables\Columns\TextColumn::make('branch.code')->label('Branch'),
                Tables\Columns\TextColumn::make('branch.province.name')->label('Province')->toggleable(),
            ])
            ->actions([Tables\Actions\EditAction::make()]);
    }

    /** Eager-load so relationship columns fill */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['roles', 'branch.province']);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
