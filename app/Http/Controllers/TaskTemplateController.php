<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Tag;
use App\Models\Task;
use App\Models\TaskTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TaskTemplateController extends Controller
{
    public function index(Request $request): Response
    {
        $userId = $request->user()->id;

        $templates = TaskTemplate::where('user_id', $userId)
            ->with('project')
            ->orderBy('name')
            ->get();

        return Inertia::render('TaskTemplates/Index', [
            'templates' => $templates->map(function ($template) {
                return [
                    'id' => $template->id,
                    'name' => $template->name,
                    'title' => $template->title,
                    'description' => $template->description,
                    'project_id' => $template->project_id,
                    'project' => $template->project ? [
                        'name' => $template->project->name,
                        'color' => $template->project->color,
                    ] : null,
                    'priority' => $template->priority,
                    'tag_ids' => $template->tag_ids ?? [],
                ];
            }),
            'projects' => Project::where('user_id', $userId)->get(),
            'tags' => Tag::where('user_id', $userId)->get(),
        ]);
    }

    public function create(Request $request): Response
    {
        $userId = $request->user()->id;

        return Inertia::render('TaskTemplates/Create', [
            'projects' => Project::where('user_id', $userId)->get(),
            'tags' => Tag::where('user_id', $userId)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'project_id' => 'nullable|integer|exists:projects,id',
            'priority' => 'required|in:low,medium,high',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'integer|exists:tags,id,user_id,' . $request->user()->id,
        ]);

        TaskTemplate::create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        return redirect()->route('task-templates.index')
            ->with('success', 'Template created successfully.');
    }

    public function edit(TaskTemplate $taskTemplate, Request $request): Response
    {
        if ($taskTemplate->user_id !== $request->user()->id) {
            abort(403);
        }

        $userId = $request->user()->id;

        return Inertia::render('TaskTemplates/Edit', [
            'template' => [
                'id' => $taskTemplate->id,
                'name' => $taskTemplate->name,
                'title' => $taskTemplate->title,
                'description' => $taskTemplate->description,
                'project_id' => $taskTemplate->project_id,
                'priority' => $taskTemplate->priority,
                'tag_ids' => $taskTemplate->tag_ids ?? [],
            ],
            'projects' => Project::where('user_id', $userId)->get(),
            'tags' => Tag::where('user_id', $userId)->get(),
        ]);
    }

    public function update(Request $request, TaskTemplate $taskTemplate): RedirectResponse
    {
        if ($taskTemplate->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'project_id' => 'nullable|integer|exists:projects,id',
            'priority' => 'required|in:low,medium,high',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'integer|exists:tags,id,user_id,' . $request->user()->id,
        ]);

        $taskTemplate->update($validated);

        return redirect()->route('task-templates.index')
            ->with('success', 'Template updated successfully.');
    }

    public function destroy(TaskTemplate $taskTemplate, Request $request): RedirectResponse
    {
        if ($taskTemplate->user_id !== $request->user()->id) {
            abort(403);
        }

        $taskTemplate->delete();

        return redirect()->route('task-templates.index')
            ->with('success', 'Template deleted successfully.');
    }

    public function useTemplate(TaskTemplate $taskTemplate, Request $request): RedirectResponse
    {
        if ($taskTemplate->user_id !== $request->user()->id) {
            abort(403);
        }

        // Create task from template
        $task = Task::create([
            'user_id' => $request->user()->id,
            'title' => $taskTemplate->title,
            'description' => $taskTemplate->description,
            'project_id' => $taskTemplate->project_id,
            'priority' => $taskTemplate->priority,
            'completed' => false,
        ]);

        // Attach tags if any
        if ($taskTemplate->tag_ids && count($taskTemplate->tag_ids) > 0) {
            $task->tags()->attach($taskTemplate->tag_ids);
        }

        return redirect()->route('tasks.edit', $task->id)
            ->with('success', 'Task created from template.');
    }
}
