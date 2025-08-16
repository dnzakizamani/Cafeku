<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Filament\Resources\SubscriptionResource\RelationManagers;
use App\Models\Subscription;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\FormsComponent;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Model;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-fire';

    protected static ?string $navigationLabel = 'Subscription';
    protected static ?string $navigationGroup = 'Manajemen';

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return parent::getEloquentQuery();
        }

        return parent::getEloquentQuery()->where('user_id', $user->id);
    }

    public static function canEdit(Model $record): bool
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return true;
        }

        return false;
    }

    public static function canDelete(Model $record): bool
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return true;
        }

        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->options(User::all()->pluck('name', 'id')->toArray())
                    ->label('Toko')
                    ->hidden(fn() => Auth::user()->role !== 'admin')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif/Nonaktif')
                    ->hidden(fn() => Auth::user()->role !== 'admin')
                    ->required(),
                Forms\Components\Repeater::make('subscriptionPayments')
                    ->relationship()
                    ->schema([
                        Forms\Components\FileUpload::make('proof')
                            ->label('Bukti Transfer Ke Rekening 2357583563 (BCA) A\N Zaki sebesar Rp 100.000')
                            ->required()
                            ->columnSpanFull()
                            ->maxSize(2048),
                        Forms\Components\Select::make('status')
                            ->label('Payment Status')
                            ->options([
                                'pending' => 'Pending',
                                'success' => 'Berhasil',
                                'failed' => 'Gagal',
                            ])
                            ->required()
                            ->columnspanFull()
                            ->hidden(fn() => Auth::user()->role !== 'admin'),
                        ])
                        ->columnSpanFull()
                        ->addable(false)
                
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
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Mulai')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Tanggal Berakhir')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('subscriptionPayments.proof')
                    ->label('Bukti Pembayaran')
                    // ->hidden(fn() => Auth::user()->role !== 'admin')
                    ->size(50),
                Tables\Columns\TextColumn::make('subscriptionPayments.status')
                    ->label('Status Pembayaran')
                    // ->hidden(fn() => Auth::user()->role !== 'admin')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
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
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
