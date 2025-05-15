<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function index()
    {
        $statuses = Status::values();
        return Inertia::render('Project/Index', [
            'statuses' => $statuses
        ]);
    }

    public function all(Request $request)
    {
        $query = Project::query();

        // Searching
        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhereDate('start_date', $request->search)
                ->orWhereDate('end_date', $request->search)
                ->orWhere('status', 'like', '%' . $request->search . '%');
        }

        // Sorting
        if ($request->has('sortField') && $request->has('sortOrder')) {
            $sortField = $request->sortField;
            $sortOrder = $request->sortOrder == 1 ? 'asc' : 'desc';
            $query->orderBy($sortField, $sortOrder);
        } else {
            $query->orderBy('id');
        }

        // Pagination
        $users = $query->paginate($request->get('rows', 10));

        return response()->json([
            'data' => $users->items(),
            'total' => $users->total()
        ]);
    }

    public function create()
    {
        return Inertia::render('Project/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'required|string|max:500',
        ]);

        $validated['start_date'] = toIndoDate($validated['start_date']);
        $validated['end_date'] = toIndoDate($validated['end_date']);

        try {
            $project = $this->projectService->createProject($validated);

            return redirect()->route('project.index')->with('
            success', 'Project created successfully');
        } catch (\Throwable $th) {
            return back()->withErrors('error', 'Failed to create project: ' . $th->getMessage());
        }
    }

    public function edit(Project $project)
    {
        return Inertia::render('Project/Edit', [
            'project' => $project
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'required|string|max:500',
        ]);

        $validated['start_date'] = toIndoDate($validated['start_date']);
        $validated['end_date'] = toIndoDate($validated['end_date']);

        try {
            $project = $this->projectService->updateProject($project, $validated);

            return redirect()->route('project.index')->with('success', 'Project updated successfully');
        } catch (\Throwable $th) {
            return back()->withErrors('error', 'Failed to update project: ' . $th->getMessage());
        }
    }

    public function show(Project $project)
    {
        $logs = $project->audits()->with('user')->orderByDesc('created_at')->get()->map(function ($log) {
            $log->old_values = is_array($log->old_values) ? json_encode($log->old_values) : $log->old_values;
            $log->new_values = is_array($log->new_values) ? json_encode($log->new_values) : $log->new_values;
            return $log;
        });

        return Inertia::render('Project/Show', [
            'project' => $project,
            'logs' => $logs,
        ]);
    }

    public function changeStatus(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:projects,id',
            'status' => 'required|in:Pending,On Progress,Done'
        ]);

        try {
            $project = Project::findOrFail($validated['id']);
            $project->status = $validated['status'];
            $project->save();

            return response()->json(['message' => 'Project status changed successfully']);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to change project status',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
