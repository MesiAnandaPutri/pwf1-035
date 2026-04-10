<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'user_id' => 'required|exists:users,id',
        ]);

        $product = Product::create($validated);

        return redirect()->route('product.index')->with('success', 'Product created successfully.');
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
        $this->authorize('update', $id);
        
        $product = Product::findOrFail($id);
    
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0', // Pastikan divalidasi sebagai 'qty'
            'price' => 'required|numeric|min:0',
            'user_id' => 'required|exists:users,id',
        ]);

        $product->update($validated);

        return redirect()->route('product.index')->with('success', 'Product updated successfully.');
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);

        $users = User::orderBy('name')->get();
        return view('product.edit', compact('product', 'users'));
    }

    public function delete($id)
    {
        $this->authorize('update', $id);

        $product = Product::findOrFail($id);

        $product->delete();

        return redirect()->route('product.index')->with('success', 'Product deleted successfully.');
    }
}