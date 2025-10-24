<?php

namespace App\Filament\Resources;

use App\Models\Supplier;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function canViewAny(): bool
    {
        $u = Auth::user();
        return $u?->hasRole('Super Admin') || $u?->hasRole('Admin');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['branch']);

        $u = auth()->user();
        if ($u?->hasRole('Admin')) {
            $query->where('branch_id', $u->branch_id);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Supplier Name')
                ->required()
                ->maxLength(150),

            Forms\Components\TextInput::make('phone')
                ->label('Phone')
                ->tel()
                ->maxLength(30),

            Forms\Components\Textarea::make('address')
                ->label('Address')
                ->columnSpanFull(),

            Forms\Components\Select::make('branch_id')
                ->label('Branch')
                ->relationship('branch', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->placeholder('Select a branch') // SA must choose
                ->default(fn() => auth()->user()?->hasRole('Admin') ? auth()->user()->branch_id : null)
                ->disabled(fn() => auth()->user()?->hasRole('Admin')),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Supplier Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->sortable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label('Branch')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Added On')
                    ->date('M d, Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn() => Auth::user()?->hasRole('Admin')),
            ]);
    }

    /**
     * Enforce branch rules server-side:
     * - Admin: force to their own branch
     * - Super Admin: must select a branch (no NULL)
     */
    protected static function mutateFormDataBeforeCreate(array $data): array
    {
        $me = auth()->user();

        if ($me?->hasRole('Admin')) {
            $data['branch_id'] = $me->branch_id;
        } elseif (empty($data['branch_id'])) {
            throw ValidationException::withMessages([
                'branch_id' => 'Please select a branch.',
            ]);
        }

        return $data;
    }

    protected static function mutateFormDataBeforeSave(array $data): array
    {
        $me = auth()->user();

        if ($me?->hasRole('Admin')) {
            $data['branch_id'] = $me->branch_id;
        } elseif (empty($data['branch_id'])) {
            throw ValidationException::withMessages([
                'branch_id' => 'Please select a branch.',
            ]);
        }

        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index'  => SupplierResource\Pages\ListSuppliers::route('/'),
            'create' => SupplierResource\Pages\CreateSupplier::route('/create'),
            'edit'   => SupplierResource\Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
