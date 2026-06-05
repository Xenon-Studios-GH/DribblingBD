<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinanceProject;
use App\Services\Finance\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    protected NotificationService $notifications;

    public function __construct(NotificationService $notifications)
    {
        $this->notifications = $notifications;
    }

    public function index()
    {
        $projects = FinanceProject::withCount(['transactions as total_spent' => function ($q) {
            $q->select(\DB::raw('COALESCE(SUM(CASE WHEN type = "income" THEN amount ELSE -amount END), 0)'));
        }])->latest()->paginate(20);
        return view('finance.projects.index', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,completed,archived',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string|max:2000',
        ]);

        $validated['created_by'] = Auth::id();
        $project = FinanceProject::create($validated);

        $this->notifications->notifyAdmins(
            'project.created',
            'New Project',
            Auth::user()->name . ' created a new project: ' . $project->name,
            'project',
            $project->id
        );

        return redirect(admin_route('finance.projects'))->with('success', 'Project created.');
    }

    public function update(Request $request, FinanceProject $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,completed,archived',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string|max:2000',
        ]);

        $validated['updated_by'] = Auth::id();
        $project->update($validated);

        return redirect(admin_route('finance.projects'))->with('success', 'Project updated.');
    }

    public function destroy(FinanceProject $project)
    {
        $project->delete();
        return redirect(admin_route('finance.projects'))->with('success', 'Project deleted.');
    }
}
