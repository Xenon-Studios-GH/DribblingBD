<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Services\WorkLogService;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    protected WorkLogService $workLogService;

    public function __construct(WorkLogService $workLogService)
    {
        $this->workLogService = $workLogService;
    }

    public function index()
    {
        $inquiries = Inquiry::latest()->paginate(20);
        return view('inquiries.index', compact('inquiries'));
    }

    public function show(Inquiry $inquiry)
    {
        if (! $inquiry->read_at) {
            $inquiry->update(['read_at' => now(), 'is_read' => true]);
            $this->workLogService->log('Inquiry Read', 'inquiry', $inquiry->id, "Inquiry from {$inquiry->name} marked as read");
        }
        return view('inquiries.show', compact('inquiry'));
    }

    public function destroy(Inquiry $inquiry)
    {
        $name = $inquiry->name;
        $inquiry->delete();
        $this->workLogService->log('Inquiry Deleted', 'inquiry', $inquiry->id, "Inquiry from {$name} deleted");
        return redirect(admin_route('inquiries.index'))->with('success', 'Inquiry deleted successfully.');
    }
}
