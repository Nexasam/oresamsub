<?php

namespace App\Http\Controllers;

use App\Models\Network;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(){
        $product_categories = ProductCategory::where('active_status',1)->get();
        $networks = Network::get();
        $products = Product::with('product_category')->get();
        $data['product_categories'] = $product_categories;
        $data['networks'] = $networks;
        $data['products'] = $products;
        // dd($data);
        return view('admin.products.index')->with($data);
    }

    public function store(Request $request){
        // dd($request->all());
 
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|max:255|unique:products,product_name',
            'product_category_id' => 'required|exists:product_categories,id',
            'visibility' => 'required',
            'active_status' => 'required'
          ]);
          
    
          if ($validator->stopOnFirstFailure()->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
          }
    
          $data['product_name'] = $request->product_name;
          $data['product_categories_id'] = $request->product_category_id;
          $data['visibility'] = $request->visibility;
          $data['active_status'] = $request->active_status;
         
          $create_product = Product::create($data);
    
          if($create_product){
            Session::flash('success','Product successfully created');
          }else{
            Session::flash('failure','Error occurred while creating product');
          }
    
          return redirect()->route('admin.products.index');
    }
}
