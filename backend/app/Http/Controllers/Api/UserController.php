<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json(['status' => true, 'data' => $users]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,staff'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return response()->json(['status' => true, 'message' => 'User berhasil dibuat', 'data' => $user]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,'.$id,
            'role' => 'required|in:admin,staff'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return response()->json(['status' => true, 'message' => 'User berhasil diupdate', 'data' => $user]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->id !== auth()->id()) {
            $user->delete();
            return response()->json(['status' => true, 'message' => 'User berhasil dihapus']);
        }
        return response()->json(['status' => false, 'message' => 'Tidak bisa menghapus diri sendiri'], 400);
    }
}
