<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index()
    {
        $client = null;
        if (Auth::check()) {
            $client = Auth::user()->client;
        }
        return view('shop.checkout.index', compact('client'));
    }

    public function saveAddress(Request $request)
    {
        $client = Client::where('user_id', Auth::id())->firstOrFail();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:1000'],
            'city' => ['required', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:100'],
            'postal' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $shipping = [
            'area' => $data['area'] ?? '',
            'postal' => $data['postal'] ?? '',
            'notes' => $data['notes'] ?? '',
        ];

        $client->update([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'city' => $data['city'],
            'shipping_address' => json_encode($shipping),
        ]);

        return response()->json(['success' => true]);
    }
}
