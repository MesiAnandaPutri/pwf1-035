<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class ProductController extends Controller
{
    use AuthorizesRequests; 

   public function index()
    {
        $products = Product::latest()->paginate(10); 
        
        return view('product.index', compact('products'));
    }
    
    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'quantity' => 'required|integer',
        'price' => 'required|numeric',
    ], [
        'name.required' => 'Nama produk wajib diisi.',
        'name.max' => 'Nama produk tidak boleh lebih dari 255 karakter.',

        'quantity.required' => 'Jumlah (kuantitas) produk wajib diisi.',
        'quantity.integer' => 'Jumlah produk harus berupa angka bulat (tidak boleh desimal).',

        'price.required' => 'Harga produk wajib diisi.',
        'price.numeric' => 'Harga produk harus berupa angka yang valid.',
    ]);

    $validated['user_id'] = Auth::id();

    try {
        Product::create($validated);

        return redirect()
            ->route('product.index')
            ->with('success', 'Product created successfully.');

    } catch (QueryException $e) {
        Log::error('Product store database error', [
            'message' => $e->getMessage(),
        ]);

        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Database error while creating product.');

    } catch (\Throwable $e) {
        Log::error('Product store unexpected error', [
            'message' => $e->getMessage(),
        ]);

        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Unexpected error occurred.');
    }
}

    public function create()
    {        
        $users = User::orderBy('name')->get();
        return view('product.create', compact('users'));
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
    
        return view('product.view', compact('product'));
    }

     public function update(Request $request, $id)
     {
        
       $validated = $request->validate([
        'name' => 'required|string|max:255',
        'quantity' => 'required|integer',
        'price' => 'required|numeric',
    ], [
        'name.required' => 'Nama produk wajib diisi.',
        'name.max' => 'Nama produk tidak boleh lebih dari 255 karakter.',

        'quantity.required' => 'Jumlah (kuantitas) produk wajib diisi.',
        'quantity.integer' => 'Jumlah produk harus berupa angka bulat (tidak boleh desimal).',

        'price.required' => 'Harga produk wajib diisi.',
        'price.numeric' => 'Harga produk harus berupa angka yang valid.',
    ]);

    $validated['user_id'] = Auth::id();

    try {
        Product::create($validated);

        return redirect()
            ->route('product.index')
            ->with('success', 'Product created successfully.');

    } catch (QueryException $e) {
        Log::error('Product store database error', [
            'message' => $e->getMessage(),
        ]);

        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Database error while creating product.');

    } catch (\Throwable $e) {
        Log::error('Product store unexpected error', [
            'message' => $e->getMessage(),
        ]);

        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Unexpected error occurred.');
    }
    }

    public function edit(Product $product)
    {

        $users = User::orderBy('name')->get();
        return view('product.edit', compact('product', 'users'));
    }

    public function delete($id)
    {

        $product = Product::findOrFail($id);

        $product->delete();

        return redirect()->route('product.index')->with('success', 'Product deleted successfully.');
    }
}