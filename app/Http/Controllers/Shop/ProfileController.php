<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index($usercode)
    {
        $client = Client::where('usercode', $usercode)->firstOrFail();
        return view('shop.profile.index', compact('client'));
    }

    public function update(Request $request, $usercode)
    {
        $client = Client::where('usercode', $usercode)->firstOrFail();

        if ($client->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'username' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'max:10'],
            'date_of_birth' => ['nullable', 'date'],
            'address' => ['nullable', 'string', 'max:1000'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'shipping_address' => ['nullable', 'string', 'max:1000'],
            'preferred_size' => ['nullable', 'string', 'max:10'],
            'favorite_team' => ['nullable', 'string', 'max:255'],
            'preferred_payment' => ['nullable', 'string', 'max:50'],
            'newsletter' => ['nullable', 'boolean'],
        ]);

        $client->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }
}
