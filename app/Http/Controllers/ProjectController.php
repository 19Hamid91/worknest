<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\ProjectService;
use Carbon\Carbon;
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
        return Inertia::render('Project/Index');
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

        $validated['start_date'] = Carbon::parse($validated['start_date'])->toDateString();
        $validated['end_date'] = Carbon::parse($validated['end_date'])->toDateString();

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

    public function update() {}

    public function show(Project $project)
    {
        return Inertia::render('Project/Show', [
            'project' => $project
        ]);
    }
}
