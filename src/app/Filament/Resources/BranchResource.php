<?php

namespace App\Filament\Resources;



use App\Models\Branch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\BranchResource\Pages;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canViewAny(): bool
    {
        return Auth::user()?->hasRole('Super Admin') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(150),
            Forms\Components\Select::make('province_id')
                ->relationship('province', 'name')
                ->searchable()->preload()->required(),
            Forms\Components\Select::make('district_id')
                ->relationship('district', 'name')
                ->searchable()->preload()->nullable(),
            Forms\Components\Toggle::make('is_active')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('province.name')->label('Province')->sortable(),
                Tables\Columns\TextColumn::make('district.name')->label('District')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
            // 'view' => Pages\ViewBranch::route('/{record}'),
        ];
    }
}
