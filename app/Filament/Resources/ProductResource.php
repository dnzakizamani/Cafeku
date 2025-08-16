<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Produk';

    protected static ?string $navigationGroup = 'Manajemen';

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return parent::getEloquentQuery();
        }

        return parent::getEloquentQuery()->where('user_id', $user->id);
    }

    public static function canCreate(): bool
    {
        if (Auth::user()->role === 'admin') {
            return true;
        }
        
        $subscription = Subscription::where('user_id', Auth::user()->id)
            ->where('end_date', '>=', now())
            ->where('is_active', true)
            ->latest()
            ->first();
        
        $countProducts = Product::where('user_id', Auth::user()->id)->count();
        

        return !($countProducts >= 5 && !$subscription);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Toko')
                    ->relationship('user', 'name')
                    ->hidden(fn() => Auth::user()->role !== 'admin')
                    ->reactive()
                    ->required(),
                Forms\Components\Select::make('product_category_id')
                    ->label('Kategori Produk')
                    ->relationship('productCategory', 'name')
                    ->disabled(fn(callable $get) => $get('user_id') === null)
                    ->options(function (callable $get){
                        $userId = $get('user_id');
                        if(!$userId) {
                            return [];
                        }
                        return \App\Models\ProductCategory::where('user_id', $userId)->pluck('name', 'id');
                    })
                    ->hidden(fn() => Auth::user()->role !== 'admin')
                    ->required(),
                Forms\Components\Select::make('product_category_id')
                    ->label('Kategori Produk')
                    ->relationship('productCategory', 'name')
                    ->options(function (callable $get){
                        return \App\Models\ProductCategory::where('user_id', Auth::user()->id)->pluck('name', 'id');
                    })
                    ->hidden(fn() => Auth::user()->role === 'admin')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Nama Produk')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('image')
                    ->label('Gambar Produk')
                    ->image()
                    ->required()
                    ->maxSize(1024),
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi Produk')
                    ->required(),
                Forms\Components\TextInput::make('price')
                    ->label('Harga Produk')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('rating')
                    ->label('Rating Produk')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(5)
                    ->step(0.1)
                    ->required(),
                Forms\Components\Toggle::make('is_popular')
                    ->label('Produk Populer')
                    ->default(false)
                    ->required(),
                Forms\Components\Repeater::make('productIngredient')
                    ->label('Bahan Produk')
                    ->relationship('productIngredient')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Bahan')
                            ->required(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Toko')
                    ->hidden(fn() => Auth::user()->role !== 'admin')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('productCategory.name')
                    ->label('Kategori Produk')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Produk')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar Produk'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga Produk')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 2, ',', '.')),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating Produk')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 1) : 'Tidak ada rating'),
                Tables\Columns\ToggleColumn::make('is_popular')
                    ->label('Produk Populer')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->label('Toko')
                    ->relationship('user', 'name')
                    ->hidden(fn() => Auth::user()->role !== 'admin'),
                Tables\Filters\SelectFilter::make('product_category_id')
                    ->label('Kategori Produk')
                    ->options(function (){
                        if(Auth::user()->role === 'admin') {
                            return \App\Models\ProductCategory::pluck('name', 'id');
                        }
                        return \App\Models\ProductCategory::where('user_id', Auth::user()->id)->pluck('name', 'id');
                    }

                ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(''),
                Tables\Actions\EditAction::make()
                    ->label(''),
                Tables\Actions\DeleteAction::make()
                    ->label(''),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
