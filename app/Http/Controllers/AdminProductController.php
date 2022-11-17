<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PharIo\Manifest\Url;

class AdminProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('admin.products', [
            'products' => $products
        ]);
    }

    public function create()
    {
        return view('admin.products_create');
    }

    public function store(ProductStoreRequest $request)
    {
        $input = $request->validated();

        if (!empty($input['cover']) && $input['cover']->isValid()) {
            $file = $input['cover'];
            $path = $file->store('products');
            $input['cover'] = $path;
            $input['coverUrl'] = env('APP_URL').'/storage/'.$path;
        }

        $input['slug'] = Str::slug($input['name']);
        Product::create($input);

        return Redirect::route('admin.products');
    }

    public function edit(Product $product)
    {
        return view('admin.products_edit', [
            'product' => $product
        ]);
    }

    public function update(Product $product, ProductUpdateRequest $request)
    {
        $input = $request->validated();

        if (!empty($input['cover']) && $input['cover']->isValid()) {
            $file = $input['cover'];
            $path = $file->store('products');
            $input['cover'] = $path;
            $input['coverUrl'] = env('APP_URL').'/storage/'.$path;
        }

        $input['slug'] = Str::slug($input['name']);
        $product->fill($input);
        $product->save();

        return Redirect::route('admin.products');
    }

    public function delete(Product $product)
    {
        Storage::delete($product->cover);
        $product->delete();
        return Redirect::back();
    }

    public function deleteImage(Product $product)
    {
        Storage::delete($product->cover);
        $product->cover = null;
        $product->coverUrl = null;
        $product->save();
        return back();
    }
}
