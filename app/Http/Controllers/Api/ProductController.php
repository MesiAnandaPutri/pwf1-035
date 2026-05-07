<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of all products.
     */
    public function index()
    {
        try {
            $products = Product::with('category')->latest()->get();

            return response()->json([
                'message' => 'Products retrieved successfully',
                'data'    => $products,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Gagal mengambil daftar produk', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil data produk',
            ], 500);
        }
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'        => 'required|string|max:255',
                'price'       => 'required|numeric|min:0',
                'quantity'    => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,id',
            ]);

            $validated['user_id'] = Auth::id();

            $product = Product::create($validated);

            Log::info('Produk berhasil ditambahkan', ['product' => $product]);

            return response()->json([
                'message' => 'Produk berhasil ditambahkan',
                'data'    => $product->load('category'),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Error saat menambah produk', ['message' => $e->getMessage()]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan produk',
            ], 500);
        }
    }

    /**
     * Display the specified product.
     */
    public function show(int $id)
    {
        try {
            $product = Product::with('category')->find($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Produk tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'message' => 'Product retrieved successfully',
                'data'    => $product,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Gagal mengambil data produk', ['message' => $e->getMessage()]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil produk',
            ], 500);
        }
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, int $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Produk tidak ditemukan',
                ], 404);
            }

            $validated = $request->validate([
                'name'        => 'sometimes|required|string|max:255',
                'price'       => 'sometimes|required|numeric|min:0',
                'quantity'    => 'sometimes|required|integer|min:0',
                'category_id' => 'sometimes|required|exists:categories,id',
            ]);

            $product->update($validated);

            Log::info('Produk berhasil diperbarui', ['product' => $product]);

            return response()->json([
                'message' => 'Produk berhasil diperbarui',
                'data'    => $product->load('category'),
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Error saat memperbarui produk', ['message' => $e->getMessage()]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat memperbarui produk',
            ], 500);
        }
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(int $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'message' => 'Produk tidak ditemukan',
                ], 404);
            }

            $product->delete();

            Log::info('Produk berhasil dihapus', ['id' => $id]);

            return response()->json([
                'message' => 'Produk berhasil dihapus',
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error saat menghapus produk', ['message' => $e->getMessage()]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus produk',
            ], 500);
        }
    }
}
