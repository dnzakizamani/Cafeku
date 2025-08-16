<?php

namespace App\Filament\Widgets;

use App\Models\SubscriptionPayment;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Product;

class DashboardOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalTransactions = 0;
        $totalAmounts = 0;

        $totalTransactions = Transaction::where('user_id', Auth::user()->id)
            ->where('status', 'success')
            ->count();

        $totalAmounts = Transaction::where('user_id', Auth::user()->id)
            ->where('status', 'success')
            ->sum('total_price');

        if(Auth::user()->role === 'admin') {
            return [
                Stat::make('Total Pengguna', User::where('role','!=', 'admin')->count())
                    ->color('primary'),
                Stat::make('Total Pendapatan Langganan', 'Rp ' . number_format(SubscriptionPayment::where('status', 'success')->count() * 50000))
                    ->color('success'),
                Stat::make('Total Produk', Product::count())
                    ->color('warning'),
            ];
        } else{
            return [
                Stat::make('Total Transaksi', $totalTransactions)
                    ->color('primary'),
                Stat::make('Total Pendapatan', 'Rp ' . number_format($totalAmounts))
                    ->color('success'),
                Stat::make('Total Produk', Product::where('user_id', Auth::user()->id)->count())
                    ->color('warning'),
                Stat::make(
                    'Rata-rata Pendapatan',
                    $totalTransactions > 0
                        ? 'Rp ' . number_format($totalAmounts / $totalTransactions)
                        : 'Rp 0'
                )->color('info'),

            ];
        }
    }
}
