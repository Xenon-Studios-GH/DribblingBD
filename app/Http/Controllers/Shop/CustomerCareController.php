<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;

class CustomerCareController extends Controller
{
    public function index()
    {
        return view('shop.customer-care.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'details' => 'required|string|max:5000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('inquiries', 'uploads');
        }

        Inquiry::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'details' => $validated['details'],
            'image' => $imagePath,
        ]);

        return back()->with('success', 'Your inquiry has been submitted. We will get back to you shortly.');
    }
}
