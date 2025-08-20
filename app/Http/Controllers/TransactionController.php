<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function cart(Request $request)
    {
        $store = User::where('username', $request->username)->first();

        if (!$store) {
            abort(404);
        }

        $products = Product::where('user_id', $store->id)
            ->get();

        return view('pages.cart', compact('store', 'products'));
    }

    public function customerInformation(Request $request)
    {
        $store = User::where('username', $request->username)->first();

        if (!$store) {
            abort(404);
        }

        $products = Product::where('user_id', $store->id)
            ->get();

        return view('pages.customer-information', compact('store', 'products'));
    }

    public function checkout(Request $request)
    {
        $store = User::where('username', $request->username)->first();

        if (!$store) {
            abort(404);
        }

        $carts = json_decode($request->input('cart'), true);

        $total_price = 0;
        foreach ($carts as $cart) {
            $product = Product::where('id', $cart['id'])
                ->where('user_id', $store->id)
                ->first();
            if ($product) {
                $total_price += $product->price * $cart['qty'];
            }
        }

        $transaction = $store->transactions()->create([
            'code' => 'TRX-' . mt_rand(10000, 99999),
            'total_price' => $total_price,
            'name' => $request->input('name'),
            'phone_number' => $request->input('phone_number'),
            'table_number' => $request->input('table_number'),
            'payment_method' => $request->input('payment_method'),
            'status' => 'pending',
        ]);

        foreach ($carts as $cart) {
            $product = Product::where('id', $cart['id'])
                ->where('user_id', $store->id)
                ->first();
            if ($product) {
                $transaction->transactionDetails()->create([
                    'product_id' => $product->id,
                    'quantity' => $cart['qty'],
                    'note' => $cart['notes'] ?? '',
                ]);
            }
        }

        if($request->input('payment_method') === 'cash') {
            return redirect()->route('success', [
                'username' => $store->username,
                'code' => $transaction->code
            ])->with('success', 'Checkout successful!');
        } elseif ($request->input('payment_method') === 'card') {
            // Handle card payment logic here
            // You might integrate with a payment gateway API
        }

        // Here you would typically handle the checkout logic, such as processing payment and creating a transaction record.

        return redirect()->route('index', ['username' => $store->username])->with('success', 'Checkout successful!');
    }

    public function success(Request $request)
    {
        $store = User::where('username', $request->username)->first();

        if (!$store) {
            abort(404);
        }

        $transaction = $store->transactions()->where('code', $request->code)->first();

        if (!$transaction) {
            abort(404);
        }

        return view('pages.success', compact('store', 'transaction'));
    }
}
