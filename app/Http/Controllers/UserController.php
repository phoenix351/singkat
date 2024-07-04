<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::query()
            ->where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhere('username', 'like', "%{$search}%")
            ->paginate(20)
            ->appends(['search' => $search]);

        return Inertia::render('Users/KelolaUser', [
            'users' => $users,
            'search' => $search,
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users'],
            'username' => ['required', 'unique:users'],
            'password' => ['required'],
            "role" => "required"
        ], [
            'email.unique' => 'Email sudah digunakan',
            'username.unique' => 'Username sudah digunakan',
        ]);

        $validatedData['password'] = bcrypt($validatedData['password']);

        User::create($validatedData);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');
    }

    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email'],
            'username' => ['required'],
            'role' => ['required'],
        ]);

        if ($request->has('password')) {
            $validatedData['password'] = bcrypt($request['password']);
        }

        // Cek email
        if ($user->email != $validatedData['email']) {
            $request->validate([
                'email' => ['unique:users'],
            ], [
                'email.unique' => 'Email sudah digunakan',
            ]);
        }

        // Cek username
        if ($user->username != $validatedData['username']) {
            $request->validate([
                'username' => ['unique:users'],
            ], [
                'username.unique' => 'Username sudah digunakan',
            ]);
        }

        $user->update($validatedData);

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus');
    }
}
