<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Product;
use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\Employee;

// --- LOGIN ---
Route::post('/login', function (Request $request) {
    $validated = $request->validate([
        'employee_id' => 'required|string',
        'pin_code'    => 'required|string',
    ]);

    if(!Auth::attempt([
        'employee_id' => $validated['employee_id'],
        'password'    => $validated['pin_code']
    ])) {
        return response()->json(['message' => 'Invalid Employee ID or PIN'], 401);
    }
    $token = Auth::user()->createToken('api-token')->plainTextToken;
    return response()->json(['token' => $token], 200);
});

// --- SANCTUM ---

Route::middleware('auth:sanctum')->group(function () {
    // LOGOUT
    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully'], 200);
    });

    // --- Product ---

    // GET ALL
    Route::get('/products', function () {
        return response()->json(Product::all(), 200);
    });

    // GET ONE
    Route::get('/products/{product}', function (Product $product) {
        return response()->json($product, 200);
    });

    // CREATE
    Route::post('/products', function (Request $request) {
        $validated = $request->validate([
            'category_id'    => 'required|exists:category,category_id',
            'product_name'   => 'required|string|max:100',
            'description'    => 'nullable|string|max:255',
            'selling_price'  => 'required|numeric|min:0',
            'cost_price'     => 'required|numeric|min:0',
            'reorder_level'  => 'required|integer|min:0',
            'product_status' => 'required|in:Active,Inactive,Discontinued',
        ]);
    
        $product = Product::create($validated);

        return response()->json(['message' => 'Product created successfully!', 'data' => $product], 201);
    });
    
        // UPDATE
    Route::patch('/products/{product}', function (Request $request, Product $product) {
        $validated = $request->validate([
            'category_id'    => 'sometimes|exists:category,category_id',
            'product_name'   => 'sometimes|string|max:100',
            'description'    => 'sometimes|nullable|string|max:255',
            'selling_price'  => 'sometimes|numeric|min:0',
            'cost_price'     => 'sometimes|numeric|min:0',
            'reorder_level'  => 'sometimes|integer|min:0',
            'product_status' => 'sometimes|in:Active,Inactive,Discontinued',
        ]);

        $product->update($validated);

        return response()->json(['message' => 'Product updated successfully!', 'data' => $product], 200);
    });

        // DELETE
    Route::delete('/products/{product}', function (Product $product) {
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully!'], 200);
    });

    // --- Category ---
    
    // GET ALL
    Route::get('/categories', function () {
        return response()->json(Category::all(), 200);
    });

    // GET ONE
    Route::get('/categories/{category}', function (Category $category) {
        return response()->json($category, 200);
    });

    // CREATE
    Route::post('/categories', function (Request $request) {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255',
            'description'   => 'nullable|string',
        ]);

        $category = Category::create($validated);
    
        return response()->json(['message' => 'Category created successfully!', 'data' => $category], 201);
    });

    // UPDATE
    Route::patch('/categories/{category}', function (Request $request, Category $category) {
        $validated = $request->validate([
            'category_name' => 'sometimes|string|max:255',
            'description'   => 'sometimes|string',
        ]);

        $category->update($validated);

        return response()->json(['message' => 'Category updated successfully!', 'data' => $category], 201);
    });

    // DELETE
    Route::delete('/categories/{category}', function (Category $category) {
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully!'], 200);
    });

    // --- InventoryItem ---

    // GET ALL
    Route::get('/inventory-items', function () {
        return response()->json(InventoryItem::all(), 200);
    });

    // GET ONE
    Route::get('/inventory-items/{inventory_item}', function (InventoryItem $inventoryItem) {
        return response()->json($inventoryItem, 200);
    });
    
    // CREATE
    Route::post('/inventory-items', function (Request $request) {
        $validated = $request->validate([
            'branch_id'        => 'required|integer',
            'product_id'       => 'required|integer|exists:product,product_id',
            'quantity_on_hand' => 'required|integer|min:0',
            'last_restocked'   => 'nullable|date',
            'inventory_status' => 'required|in:Available,Low Stock,Out of Stock',
        ]);
    
        $inventoryItem = InventoryItem::create($validated);
    
        return response()->json(['message' => 'Inventory created!', 'data' => $inventoryItem], 201);
    });
    
    // UPDATE
    Route::patch('/inventory-items/{inventory_item}', function (Request $request, InventoryItem $inventoryItem) {
        $validated = $request->validate([
            'branch_id'        => 'sometimes|integer',
            'product_id'       => 'sometimes|integer|exists:product,product_id',
            'quantity_on_hand' => 'sometimes|integer|min:0',
            'last_restocked'   => 'sometimes|date',
            'inventory_status' => 'sometimes|in:Available,Low Stock,Out of Stock',
        ]);
    
        $inventoryItem->update($validated);
    
        return response()->json(['message' => 'Inventory updated!', 'data' => $inventoryItem], 200);
    });
    
    // DELETE
    Route::delete('/inventory-items/{inventory_item}', function (InventoryItem $inventoryItem) {
        $inventoryItem->delete();
        return response()->json(['message' => 'Inventory deleted!'], 200);
    });

    // --- Employee ---

    // GET ALL
    Route::get('/employees', function () {
        return response()->json(Employee::all(), 200);
    });
    
    // GET ONE
    Route::get('/employees/{employee}', function (Employee $employee) {
        return response()->json($employee, 200);
    });
    
    // CREATE
    Route::post('/employees', function (Request $request) {
        $validated = $request->validate([
            'employee_id'       => 'required|string|max:10|unique:employee,employee_id',
            'first_name'        => 'required|string|max:50',
            'last_name'         => 'required|string|max:50',
            'position'          => 'nullable|string|max:500',
            'phone_number'      => 'nullable|string|max:20',
            'hire_date'         => 'nullable|date',
            'employment_status' => 'nullable|in:Active,Inactive,Terminated',
            'pin_code'          => 'required|string|min:5',
        ]);
    
        $validated['pin_code'] = Hash::make($validated['pin_code']);
        $employee = Employee::create($validated);
    
        return response()->json(['message' => 'Employee created successfully!', 'data' => $employee], 201);
    });
    
    // UPDATE
    Route::patch('/employees/{employee}', function (Request $request, Employee $employee) {
        $validated = $request->validate([
            'first_name'        => 'sometimes|string|max:50',
            'last_name'         => 'sometimes|string|max:50',
            'position'          => 'sometimes|string|max:500',
            'phone_number'      => 'sometimes|string|max:20',
            'hire_date'         => 'sometimes|date',
            'employment_status' => 'sometimes|in:Active,Inactive,Terminated',
            'pin_code'          => 'sometimes|string|min:5',
        ]);
    
        if ($request->has('pin_code')) {
            $validated['pin_code'] = Hash::make($validated['pin_code']);
        }
        
        $employee->update($validated);
    
        return response()->json(['message' => 'Employee updated!', 'data' => $employee], 200);
    });
    
    // DELETE
    Route::delete('/employees/{employee}', function (Employee $employee) {
        $employee->delete();
        return response()->json(['message' => 'Employee deleted!'], 200);
    });
});