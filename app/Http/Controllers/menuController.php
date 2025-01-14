<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class menuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $roles = $request->User()->role;
        if ($roles == 'admin' || $roles == 'kasir') {
            return response()->json(['data' => Menu::all()]);
        } else {
            return response()->json(['message' => 'Role tidak Valid'], 422);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $roles = $request->User()->role;
        if ($roles == 'admin') {
            $data = $request->validate([
                'namaMenu' => 'required|string',
                'jenis' => 'required|string',
                'deskripsi' => 'required|string',
                'gambar' => 'required|image|mimes:jpg,jpeg,png|max:2048', // Image validation
                'harga' => 'required|integer',
            ]);

            // Handle image upload
            if ($request->hasFile('gambar')) {
                $image = $request->file('gambar');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs($imageName);
                $data['gambar'] =  $imagePath; // Update image path in the database'
                $storage = 'menu';
                $image->move($storage, $imageName);
            }

            $Menu = Menu::create($data);

            return response()->json(['data' => $Menu], 201); // 201 Created status code
        } else {
            return response()->json(['message' => 'Role tidak Valid'], 422);
        }
    }

    public function show(string $id, Request $request)
    {
        $roles = $request->User()->role;
        if ($roles == 'admin') {
            $Menu = Menu::find($id);
            if (!$Menu) {
                return response()->json(['message' => 'Menu tidak ada']);
            } else {
                return response()->json(['message' => $Menu]);
            }
        } else {
            return response()->json(['message' => 'Role tidak Valid'], 422);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $roles = $request->User()->role;
        if ($roles == 'admin') {
            $Menu = Menu::find($id);

            // Check if the record exists
            if (!$Menu) {
                return response()->json(['error' => 'Menu Tidak Ada'], 404);
            }

            $data = $request->validate([
                'namaMenu' => 'required|string',
                'jenis' => 'required|string',
                'deskripsi' => 'required|string',
                'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Image validation, can be nullable if not updating the image
                'harga' => 'required|integer',
            ]);

            // Handle image upload if provided
            if ($request->hasFile('gambar')) {
                $image = $request->file('gambar');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs($imageName);
                $data['gambar'] =  $imagePath; // Update image path in the database
                $storage = 'menu';
                $image->move($storage, $imageName);
                $oldFile = 'menu/' . $Menu->gambar;
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            // Update menu data
            $Menu->update($data);

            return response()->json(['data' => $Menu], 200);
        } else {
            return response()->json(['message' => 'Role tidak Valid'], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        $roles = $request->User()->role;
        if ($roles == 'admin') {
            $Menu = Menu::find($id);

            // Check if the record exists
            if (!$Menu) {
                return response()->json(['error' => 'Menu Tidak Ada'], 404);
            }

            // Delete the record
            $Menu->delete();
            $oldFile = 'menu/' . $Menu->gambar;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }

            // Return a success response
            return response()->json(['message' => 'Menu Berhasil di Hapus'], 200);
        } else {
            return response()->json(['message' => 'Role tidak Valid'], 422);
        }
    }
}
