<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$products = [
    [
        'product_id' => 1,
        'product_name' => 'Matcha Coffee',
        'description' => 'A classic Japanese drink combined with coffee.',
        'selling_price' => 150.00,
        'cost_price' => 100.00,
        'reorder_level' => 10,
        'product_status' => 'active'
    ],
];

$nextId = 2;

// CREATE
Route::post('/products', function () use(&$products) {
    $validated = request->validate([
    'product_name'   => 'required|string',
    'selling_price'  => 'required|numeric',
    'cost_price'     => 'required|numeric',
    'reorder_level'  => 'required|integer',
    'product_status' => 'required|in:active,inactive',
    ]);
    
    $newProduct = [
    'product_id'     => $nextId,
    'product_name'   => request('product_name'),
    'description'    => request('description'),
    'selling_price'  => request('selling_price'),
    'cost_price'     => request('cost_price'),
    'reorder_level'  => request('reorder_level'),
    'product_status' => request('product_status'),
];
    
    $products[] = $newProduct;
    $nextId++;

    return response()->json(['message' => 'Product created successfully',
                             'data' => $newProduct], 201);
});

// READ ALL
Route::get('/products', function () use (&$products) {
    return response()->json ($products, 200);
});

// READ ONE
Route::get('/products/{product_id}', function ($product_id) use (&$products){
    $product = collect($products)->firstWhere('product_id', $product_id);

    if(!$product){
        return response()->json(['message' => 'Product not found.'], 404);
    }

    return response()->json($products, 200);
});

// UPDATE
Route::patch('/products/{product_id}', function ($product_id) use(&$products) {
    request()->validate([
         'product_name'   => 'sometimes|string',
         'selling_price'  => 'sometimes|numeric',
         'cost_price'     => 'sometimes|numeric',
         'reorder_level'  => 'sometimes|integer',
         'product_status' => 'sometimes|in:active,inactive',
     ]);
    
    $key = array_search($product_id, array_column($products, 'product_id'));

    if ($key === false) {
        return response()->json(['message' => 'Product not found.'], 404);
    }

    if (request()->has('product_name'))   $products[$key]['product_name']   = request('product_name');
    if (request()->has('description'))    $products[$key]['description']    = request('description');
    if (request()->has('selling_price'))  $products[$key]['selling_price']  = request('selling_price');
    if (request()->has('cost_price'))     $products[$key]['cost_price']     = request('cost_price');
    if (request()->has('reorder_level'))  $products[$key]['reorder_level']  = request('reorder_level');
    if (request()->has('product_status')) $products[$key]['product_status'] = request('product_status');

    return response()->json([
        'message' => 'Product updated successfully!',
        'data'    => $products[$key],
    ], 200);
});

// DELETE
Route::delete('/products/{product_id}', function ($product_id) use (&$products) {
    $key = array_search($product_id, array_column($products, 'product_id'));

    if ($key === false) {
        return response()->json(['message' => 'Product not found.'], 404);
    }
    
    unset($products[$key]);

    $products = array_values($products);

    return response()->json(['message' => 'Product deleted!'], 200);
});
