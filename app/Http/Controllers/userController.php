<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;


class UserController extends Controller
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
        if ($roles == 'admin') {
            return response()->json(['data' => User::all()]);
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
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'role' => 'required|string',
            ]);

            $user = User::create($data);

            // $token = Auth::guard('api')->login($user);
            return response()->json([
                'status' => 'success',
                'message' => 'User Berhasil Di Buat',
                'user' => $user,
            ]);
        } else {
            return response()->json(['message' => 'Role tidak Valid'], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        $roles = $request->User()->role;
        if ($roles == 'admin') {
            $User = User::find($id);
            if (!$User) {
                return response()->json(['message' => 'User tidak ada']);
            } else {
                return response()->json(['message' => $User]);
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
            $User = User::find($id);

            // Check if the record exists
            if (!$User) {
                return response()->json(['error' => 'User tidak ada'], 404);
            }
            $data = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email',
                'password' => 'required|string',
                'role' => ['required', Rule::in(['admin', 'manajer', 'kasir'])],
            ]);
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            } else {
                unset($data['password']);
            }
            $User->update($data);

            return response()->json(['data' => $User], 200);
        } else {
            return response()->json(['message' => 'Role tidak Valid'], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $roles = $request->User()->role;
        if ($roles == 'admin') {
            $User = User::find($id);

            // Check if the record exists
            if (!$User) {
                return response()->json(['error' => 'User tidak ada'], 404);
            }

            // Delete the record
            $User->delete();

            // Return a success response
            return response()->json(['message' => 'User Berhasil di Hapus'], 200);
        } else {
            return response()->json(['message' => 'Role tidak Valid'], 422);
        }
    }
}
