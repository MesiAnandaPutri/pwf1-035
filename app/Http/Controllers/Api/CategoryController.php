<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Display a listing of all categories.
     */
    public function index()
    {
        try {
            $categories = Category::withCount('products')->latest()->get();

            return response()->json([
                'message' => 'Categories retrieved successfully',
                'data'    => $categories,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Gagal mengambil daftar kategori', ['message' => $e->getMessage()]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil data kategori',
            ], 500);
        }
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name',
            ]);

            $category = Category::create($validated);

            Log::info('Kategori berhasil ditambahkan', ['category' => $category]);

            return response()->json([
                'message' => 'Kategori berhasil ditambahkan',
                'data'    => $category,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Error saat menambah kategori', ['message' => $e->getMessage()]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan kategori',
            ], 500);
        }
    }

    /**
     * Display the specified category along with its products.
     */
    public function show(int $id)
    {
        try {
            $category = Category::with('products')->find($id);

            if (!$category) {
                return response()->json([
                    'message' => 'Kategori tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'message' => 'Category retrieved successfully',
                'data'    => $category,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Gagal mengambil data kategori', ['message' => $e->getMessage()]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil kategori',
            ], 500);
        }
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, int $id)
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    'message' => 'Kategori tidak ditemukan',
                ], 404);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories,name,' . $id,
            ]);

            $category->update($validated);

            Log::info('Kategori berhasil diperbarui', ['category' => $category]);

            return response()->json([
                'message' => 'Kategori berhasil diperbarui',
                'data'    => $category,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Error saat memperbarui kategori', ['message' => $e->getMessage()]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat memperbarui kategori',
            ], 500);
        }
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(int $id)
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    'message' => 'Kategori tidak ditemukan',
                ], 404);
            }

            // Cek apakah masih ada produk yang menggunakan kategori ini
            if ($category->products()->count() > 0) {
                return response()->json([
                    'message' => 'Kategori tidak dapat dihapus karena masih memiliki produk terkait',
                ], 409);
            }

            $category->delete();

            Log::info('Kategori berhasil dihapus', ['id' => $id]);

            return response()->json([
                'message' => 'Kategori berhasil dihapus',
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error saat menghapus kategori', ['message' => $e->getMessage()]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus kategori',
            ], 500);
        }
    }
}
