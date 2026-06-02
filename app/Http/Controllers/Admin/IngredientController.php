<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class IngredientController extends Controller
{
    /**
     * Menampilkan daftar stok bahan baku.
     * Diurutkan agar stok yang paling sedikit muncul di atas (prioritas restock).
     */
    public function index()
    {
        $ingredients = Ingredient::with('user')->orderBy('updated_at', 'desc')->get();
        return view('admin.stok.index', compact('ingredients'));
    }

    /**
     * Menyimpan bahan baku baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'stok' => 'required|integer|min:0'
        ]);

        Ingredient::create([
            'nama' => $request->nama,
            'stok' => $request->stok,
        ]);

        return back()->with('success', "Bahan baku {$request->nama} berhasil ditambahkan.");
    }

    /**
     * Memperbarui data bahan baku (Nama atau Jumlah Stok).
     */
    // app/Http/Controllers/Admin/IngredientController.php
    public function update(Request $request, $id)
    {
        // 1. Validasi hanya untuk data dari form
        $request->validate([
            'nama' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
        ]);

        // 2. Cari data bahan baku
        $ingredient = \App\Models\Ingredient::findOrFail($id);

        // 3. Update data dan masukkan Auth::id() secara manual
        $ingredient->update([
            'nama'            => $request->nama,
            'stok'            => $request->stok,
            'last_updated_by' => Auth::id(), // Masukkan ID user di sini
        ]);

        return back()->with('success', 'Stok berhasil diperbarui oleh ' . Auth::user()->name);
    }

    /**
     * Menghapus bahan baku dari sistem.
     */
    public function destroy($id) // Gunakan $id bukan Ingredient $ingredient
    {
        $ingredient = Ingredient::findOrFail($id);
        $ingredient->delete();

        return back()->with('success', 'Bahan baku berhasil dihapus.');
    }
}