<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $setting = Setting::getSetting();
        $parentCategories = Category::whereNull('parent_id')
            ->withCount(['children', 'products'])
            ->get();
            
        $latestProducts = Product::with(['category.parent'])
            ->latest()
            ->take(6)
            ->get();

        return view('home.index', compact('setting', 'parentCategories', 'latestProducts'));
    }
}
