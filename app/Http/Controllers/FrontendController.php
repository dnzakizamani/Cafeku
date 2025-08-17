<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\User;

class FrontendController extends Controller
{
    public function index(Request $request)
    {
        $store = User::where('username', $request->username)->first();

        if (!$store) {
            abort(404);
        }

        $populars = Product::where('user_id', $store->id)
            ->where('is_popular', true)
            // ->take(6)
            ->get();

        $products = Product::where('user_id', $store->id)
            ->where('is_popular', false)
            // ->take(6)
            ->get();
        
        return view('pages.index',compact('store', 'populars', 'products'));
    }
}
