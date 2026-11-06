<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Ingredient;
use App\Models\IngredientHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class MenuManagementController extends Controller
{
    public function index()
    {
        $menus = Menu::latest()->get();
        return view('admin.menu.index', compact('menus'));
    }

    public function create()
    {
        return view('admin.menu.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'required|string|max:255',
            'harga' => 'required|numeric',
            'image' => 'nullable|image|max:2048',
            'stok'  => 'nullable|numeric|min:0', // Field baru dari form
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menu', 'public');
        }

        $ingredientId = null;

        // Jika kategorinya makanan, otomatis buatkan master stoknya
        if (strtolower($request->jenis) === 'makanan') {
            $ingredient = Ingredient::create([
                'nama' => $request->nama,
                'stok' => $request->stok ?? 0,
                'last_updated_by' => \Illuminate\Support\Facades\Auth::id(),
            ]);

            // HAPUS BARIS KETERANGAN DI SINI
            IngredientHistory::create([
                'ingredient_id' => $ingredient->id,
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'old_stok' => 0,
                'new_stok' => $request->stok ?? 0,
                'difference' => $request->stok ?? 0,
            ]);

            $ingredientId = $ingredient->id;
        }

        Menu::create([
            'nama' => $request->nama,
            'jenis' => $request->jenis,
            'harga' => $request->harga,
            'image_path' => $imagePath,
            'ingredient_id' => $ingredientId, // Langsung tautkan
        ]);

        return redirect()->route('menu.index')->with('success', 'Menu dan stok awal berhasil ditambahkan.');
    }

    public function edit(Menu $menu)
    {
        return view('admin.menu.edit', compact('menu'));
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'jenis' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric',
            'image' => 'nullable|image|max:2048',
            'stok'  => 'nullable|numeric|min:0',
        ]);

        if ($request->hasFile('image')) {
            if ($menu->image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($menu->image_path);
            }
            $menu->image_path = $request->file('image')->store('menu', 'public');
        }

        $ingredientId = $menu->ingredient_id;

        // Cek jika kategori diubah menjadi makanan atau sudah makanan
        if (strtolower($request->jenis) === 'makanan') {
            if ($ingredientId) {
                // Update stok yang sudah ada
                $ingredient = Ingredient::find($ingredientId);
                if ($ingredient) {
                    $oldStok = $ingredient->stok;
                    $newStok = $request->stok ?? $oldStok;
                    $diff = $newStok - $oldStok;

                    $ingredient->update([
                        'nama' => $request->nama, // Singkronkan jika nama menu diubah
                        'stok' => $newStok,
                        'last_updated_by' => \Illuminate\Support\Facades\Auth::id()
                    ]);

                    if ($diff != 0) {
                        // HAPUS BARIS KETERANGAN DI SINI
                        IngredientHistory::create([
                            'ingredient_id' => $ingredient->id,
                            'user_id' => \Illuminate\Support\Facades\Auth::id(),
                            'old_stok' => $oldStok,
                            'new_stok' => $newStok,
                            'difference' => $diff,
                        ]);
                    }
                }
            } else {
                // Jika sebelumnya bukan makanan, lalu diubah jadi makanan, buatkan stok baru
                $ingredient = Ingredient::create([
                    'nama' => $request->nama,
                    'stok' => $request->stok ?? 0,
                    'last_updated_by' => \Illuminate\Support\Facades\Auth::id(),
                ]);

                // HAPUS BARIS KETERANGAN DI SINI
                IngredientHistory::create([
                    'ingredient_id' => $ingredient->id,
                    'user_id' => \Illuminate\Support\Facades\Auth::id(),
                    'old_stok' => 0,
                    'new_stok' => $request->stok ?? 0,
                    'difference' => $request->stok ?? 0,
                ]);
                $ingredientId = $ingredient->id;
            }
        }

        $menu->update([
            'jenis' => $request->jenis,
            'nama' => $request->nama,
            'harga' => $request->harga,
            'image_path' => $menu->image_path,
            'ingredient_id' => $ingredientId,
        ]);

        return redirect()->route('menu.index')->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Menu $menu)
    {
        if ($menu->image_path) {
            Storage::disk('public')->delete($menu->image_path);
        }

        $menu->delete();

        return back()->with('success', 'Menu berhasil dihapus');
    }
}
