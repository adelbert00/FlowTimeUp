<?php

namespace App\Http\Controllers;

use App\Http\Requests\Projects\StoreProjectRequest;
use App\Http\Requests\Projects\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Policies\ProjectPolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(Request $request): Response
    {
        $filter = $request->input('status', 'active');

        $query = Project::where('user_id', $request->user()->id)
            ->withCount('tasks');

        if ($filter === 'active') {
            $query->where('is_archived', false);
        } elseif ($filter === 'archived') {
            $query->where('is_archived', true);
        }

        $projects = $query->get();

        return Inertia::render('Projects/Index', [
            'projects' => ProjectResource::collection($projects)->resolve(),
            'statusFilter' => $filter,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Projects/Create');
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        Project::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return redirect()->back()->with('success', 'Project created!');
    }

    public function show(Project $project, Request $request): Response
    {
        $this->authorize('view', $project);

        $project->load(['tasks.timeSessions', 'tasks.tags']);

        return Inertia::render('Projects/Show', [
            'project' => new ProjectResource($project),
        ]);
    }

    public function edit(Project $project): Response
    {
        $this->authorize('update', $project);

        return Inertia::render('Projects/Edit', [
            'project' => new ProjectResource($project),
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $project->update($request->validated());

        return redirect()->back()->with('success', 'Project updated!');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()->back()->with('success', 'Project deleted!');
    }

    public function archive(Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $project->update(['is_archived' => true]);

        return redirect()->back()->with('success', 'Project archived!');
    }

    public function restore(Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $project->update(['is_archived' => false]);

        return redirect()->back()->with('success', 'Project restored!');
    }
}
