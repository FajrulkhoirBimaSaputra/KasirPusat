<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\IngredientHistory; // Pastikan model ini sudah dibuat
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IngredientController extends Controller
{
    /**
     * Menampilkan daftar stok bahan baku beserta riwayatnya.
     */
    public function index()
    {
        // Tarik data beserta user yang mengupdate dan riwayat log-nya (diurutkan dari yang terbaru)
        $ingredients = Ingredient::with(['user', 'histories' => function ($query) {
            $query->latest();
        }, 'histories.user'])->orderBy('updated_at', 'desc')->get();

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

        $ingredient = Ingredient::create([
            'nama' => $request->nama,
            'stok' => $request->stok,
            'last_updated_by' => Auth::id(),
        ]);

        // Catat riwayat stok awal
        IngredientHistory::create([
            'ingredient_id' => $ingredient->id,
            'user_id' => Auth::id(),
            'old_stok' => 0,
            'new_stok' => $request->stok,
            'difference' => $request->stok,
        ]);

        return back()->with('success', "Bahan baku {$request->nama} berhasil ditambahkan.");
    }

    /**
     * Memperbarui data bahan baku (Nama atau Jumlah Stok).
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
        ]);

        $ingredient = Ingredient::findOrFail($id);
        $oldStok = $ingredient->stok;
        $newStok = $request->stok;
        $difference = $newStok - $oldStok;

        // LOGIKA KEAMANAN: Kasir hanya boleh mengurangi stok (stok baru < stok lama)
        if (Auth::user()->role === 'kasir' && $difference > 0) {
            return back()->with('error', 'Akses ditolak! Kasir hanya dapat memperbarui stok jika barang berkurang/habis.');
        }

        // Update data master
        $ingredient->update([
            'nama' => $request->nama,
            'stok' => $newStok,
            'last_updated_by' => Auth::id(),
        ]);

        // Jika ada perubahan angka stok, catat di riwayat
        if ($difference != 0) {
            IngredientHistory::create([
                'ingredient_id' => $ingredient->id,
                'user_id' => Auth::id(),
                'old_stok' => $oldStok,
                'new_stok' => $newStok,
                'difference' => $difference,
            ]);
        }

        return back()->with('success', 'Stok berhasil diperbarui oleh ' . Auth::user()->name);
    }

    /**
     * Menghapus bahan baku dari sistem.
     */
    public function destroy($id)
    {
        $ingredient = Ingredient::findOrFail($id);
        $ingredient->delete(); // Riwayat akan otomatis terhapus karena CASCADE di SQL

        return back()->with('success', 'Bahan baku berhasil dihapus.');
    }
}
