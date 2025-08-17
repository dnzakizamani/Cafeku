<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\User;

class ProductController extends Controller
{
    public function find(Request $request)
    {
        $store = User::where('username', $request->username)->first();

        if (!$store) {
            abort(404);
        }

        return view('pages.find', compact('store'));
        

    }

    public function findResults(Request $request)
    {
        $store = User::where('username', $request->username)->first();

        if (!$store) {
            abort(404);
        }

        $products = Product::where('user_id', $store->id);

        if(isset($request->category)) {
            $category = $store->productCategories->where('user_id', $store->id)
                ->where('slug', $request->category)
                ->first();
            
            $products = $products->where('product_category_id', $category->id);
        }
        
        if(isset($request->search)) {
            $products = $products->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $products->get();

        return view('pages.result', compact('store', 'products'));
    }

    public function show(Request $request)
    {
        $store = User::where('username', $request->username)->first();

        if (!$store) {
            abort(404);
        }
        $product = Product::where('user_id', $store->id)
            ->where('id', $request->id)
            ->first();

        if (!$product) {
            abort(404);
        }

        return view('pages.product', compact('store', 'product'));
    }

    public function categories($username)
    {
        $store = User::where('username', $username)->firstOrFail();
        $categories = $store->productCategories;

        return view('pages.categories', compact('store', 'categories'));
    }

    public function favorites($username)
    {
        $store = User::where('username', $username)->firstOrFail();
        $favorites = $store->products()->orderByDesc('rating')->get();

        return view('pages.favorites', compact('store', 'favorites'));
    }

    public function recommendations($username)
    {
        $store = User::where('username', $username)->firstOrFail();
        $recommendations = $store->products()->where('is_recommended', true)->get();

        return view('pages.recommendations', compact('store', 'recommendations'));
    }

}
