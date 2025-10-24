<?php

namespace App\Filament\Resources;

use App\Models\Product;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function canViewAny(): bool
    {
        $u = Auth::user();
        return $u?->hasRole('Super Admin') || $u?->hasRole('Admin');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(150),
            Forms\Components\TextInput::make('sku')->unique(ignoreRecord: true)->required(),
            Forms\Components\Select::make('category_id')->relationship('category', 'name')->required()->searchable()->preload(),
            Forms\Components\Select::make('unit_base_id')
                ->label('Unit')
                ->relationship('baseUnit', 'name')
                ->required()
                ->searchable()
                ->preload(),
            Forms\Components\Textarea::make('notes')->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('sku')->sortable(),
            Tables\Columns\TextColumn::make('category.name')->label('Category')->sortable(),
            Tables\Columns\TextColumn::make('baseUnit.name')->label('Unit')->sortable(),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ProductResource\Pages\ListProducts::route('/'),
            'create' => ProductResource\Pages\CreateProduct::route('/create'),
            'edit'   => ProductResource\Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
