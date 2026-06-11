<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function index()
    {
        $inquiries = Inquiry::latest()->paginate(20);
        return view('inquiries.index', compact('inquiries'));
    }

    public function show(Inquiry $inquiry)
    {
        if (! $inquiry->is_read) {
            $inquiry->update(['is_read' => true]);
        }
        return view('inquiries.show', compact('inquiry'));
    }

    public function destroy(Inquiry $inquiry)
    {
        $inquiry->delete();
        return redirect(admin_route('inquiries.index'))->with('success', 'Inquiry deleted successfully.');
    }
}
