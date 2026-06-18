<?php

namespace App\Http\Controllers\Shop\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', Password::min(8)->letters()->mixedCase()->numbers(), 'confirmed'],
        ]);

        $user = DB::transaction(function () use ($data) {
            $hashedPassword = Hash::make($data['password']);
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => $hashedPassword,
                'role' => 'customer',
                'status' => true,
            ]);

            Client::create([
                'user_id' => $user->id,
                'usercode' => Client::generateUsercode(),
                'name' => $data['name'],
                'username' => null,
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
            ]);

            return $user;
        });

        Auth::login($user);

        return redirect()->route('shop.home');
    }
}
