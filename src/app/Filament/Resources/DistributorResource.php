<?php

namespace App\Filament\Resources;

use App\Models\Distributor;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class DistributorResource extends Resource
{
    protected static ?string $model = Distributor::class;
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    public static function canViewAny(): bool
    {
        $u = Auth::user();
        return $u?->hasRole('Super Admin') || $u?->hasRole('Admin');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['branch']);
        $u = auth()->user();
        if ($u?->hasRole('Admin')) $query->where('branch_id', $u->branch_id);
        return $query;
    }

    public static function form(Form $form): Form
    {
        $u = Auth::user();

        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(150),
            Forms\Components\TextInput::make('phone')->tel(),
            Forms\Components\Textarea::make('address')->columnSpanFull(),
            Forms\Components\Select::make('branch_id')
                ->relationship('branch', 'name')
                ->searchable()->preload()->required()
                ->default($u?->branch_id)
                ->disabled(fn() => $u?->hasRole('Admin')),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('phone'),
            Tables\Columns\TextColumn::make('branch.name')->label('Branch')->sortable(),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make()->hidden(fn() => Auth::user()?->hasRole('Admin')),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => DistributorResource\Pages\ListDistributors::route('/'),
            'create' => DistributorResource\Pages\CreateDistributor::route('/create'),
            'edit'   => DistributorResource\Pages\EditDistributor::route('/{record}/edit'),
        ];
    }
}
