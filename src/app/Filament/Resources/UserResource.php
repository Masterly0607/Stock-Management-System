<?php

namespace App\Filament\Resources;


use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationGroup = 'Administration';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function canViewAny(): bool
    {
        $u = Auth::user();
        return $u?->hasRole('Super Admin') || $u?->hasRole('Admin');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['roles', 'branch.province']);

        $u = auth()->user();
        if ($u?->hasRole('Admin')) {
            $query->where('branch_id', $u->branch_id);
        }

        return $query;
    }


    public static function form(Form $form): Form
    {
        $me = Auth::user();

        // Roles shown in the dropdown
        $roleOptions = $me?->hasRole('Admin')
            ? ['Distributor' => 'Distributor'] // Admin can only create Distributor accounts
            : [
                'Super Admin' => 'Super Admin',
                'Admin'       => 'Admin',
                'Distributor' => 'Distributor',
            ];

        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(150),

            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true),

            // Branch select (replace "province/district" — branch already knows its province/district)
            Forms\Components\Select::make('branch_id')
                ->label('Branch')
                ->relationship('branch', 'name')
                ->required()
                ->searchable()
                ->preload()
                ->placeholder('Select a branch') // so SA sees empty prompt
                ->default(fn() => Auth::user()?->hasRole('Admin') ? Auth::user()->branch_id : null)
                ->disabled(fn() => Auth::user()?->hasRole('Admin')), // Admin locked to their branch


            Forms\Components\Select::make('role')
                ->label('Role')
                ->options($roleOptions)
                ->required()
                ->default($me?->hasRole('Admin') ? 'Distributor' : null)
                ->disabled(fn() => false),

            Forms\Components\TextInput::make('password')
                ->password()
                ->required(fn(string $context) => $context === 'create')
                ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                ->dehydrated(fn($state) => filled($state)),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('roles.0.name')->label('Role'),
                Tables\Columns\TextColumn::make('branch.name')->label('Branch')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn() => Auth::user()?->hasRole('Admin')), // Admin cannot delete users
            ]);
    }

    /**
     * Server-side enforcement:
     * - If Admin is creating, forcibly set branch_id to their branch and role to Distributor.
     */
    protected static function mutateFormDataBeforeCreate(array $data): array
    {
        $me = Auth::user();
        if ($me?->hasRole('Admin')) {
            $data['branch_id'] = $me->branch_id;
            $data['role'] = 'Distributor';
        }
        return $data;
    }

    protected static function mutateFormDataBeforeSave(array $data): array
    {
        $me = Auth::user();
        if ($me?->hasRole('Admin')) {
            // Admins cannot change branch/role on edit either
            $data['branch_id'] = $me->branch_id;
            $data['role'] = 'Distributor';
        }
        return $data;
    }

    /**
     * Sync Spatie role after create/update using the submitted role field.
     */
    protected static function afterCreate($record): void
    {
        $me = Auth::user();
        $role = $me?->hasRole('Admin') ? 'Distributor' : (request()->input('data.role') ?? null);
        if ($role) {
            $record->syncRoles([$role]);
        }
    }

    protected static function afterSave($record): void
    {
        $me = Auth::user();
        $role = $me?->hasRole('Admin') ? 'Distributor' : (request()->input('data.role') ?? null);
        if ($role) {
            $record->syncRoles([$role]);
        }
    }

    public static function getPages(): array
    {
        return [
            'index'  => UserResource\Pages\ListUsers::route('/'),
            'create' => UserResource\Pages\CreateUser::route('/create'),
            'edit'   => UserResource\Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
