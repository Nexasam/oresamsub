<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index(){
        $data = ProductCategory::get();
        return view('admin.product_categories.index')->with([
            'data' => $data
        ]);
    }
}
