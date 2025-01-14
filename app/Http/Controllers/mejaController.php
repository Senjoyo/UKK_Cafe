<?php

namespace App\Http\Controllers;

use App\Models\Meja;
use Illuminate\Http\Request;

class mejaController extends Controller
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
            return response()->json(['data' => Meja::all()]);
        } else {
            return response()->json(['message' => 'Role tidak Valid'], 422);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $roles = $request->User()->role;
        if ($roles == 'admin') {
            $data = $request->validate([
                'nomerMeja' => 'required|string',
            ]);
            $meja = Meja::create($data);
            return response()->json(['data' => $meja], 201); // 201 Created status code
        } else {
            return response()->json(['message' => 'Role tidak Valid'], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        $roles = $request->user()->role;
        if ($roles == 'admin') {
            $meja = Meja::find($id);
            if (!$meja) {
                return response()->json(['message' => "Meja tidak ada"]);
            } else {
                return response()->json(['message' => $meja]);
            }
        } else {
            return response()->json(['message' => 'Role tidak Valid'], 422);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id, Meja $meja, Request $request)
    {
        $roles = $request->user()->role;
        if ($roles == 'admin') {
            return response()->json(['data' => $meja->find($id)]);
        } else {
            return response()->json(['message' => 'Role tidak Valid'], 422);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $roles = $request->user()->role;
        if ($roles == 'admin') {
            $meja = Meja::find($id);
            // Check if the record exists
            if (!$meja) {
                return response()->json(['error' => 'Meja Tidak Ada'], 404);
            }
            $data = $request->validate([
                'nomerMeja' => 'required|string',
            ]);
            $meja->update($data);

            return response()->json(['data' => $meja], 200);
        } else {
            return response()->json(['message' => 'Role tidak Valid'], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        $roles = $request->user()->role;
        if ($roles == 'admin') {
            $meja = Meja::find($id);
            // Check if the record exists
            if (!$meja) {
                return response()->json(['error' => 'Meja Tidak ada'], 404);
            }

            // Delete the record
            $meja->delete();

            // Return a success response
            return response()->json(['message' => 'Meja Berhasil di Hapus'], 200);
        } else {
            return response()->json(['message' => 'Role tidak Valid'], 422);
        }
    }
}
