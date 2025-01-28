<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Tag;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $tasks = Task::with(['project','tags','timeSessions'])->get();
    
        if($request->wantsJson()) {
            return response()->json($tasks);
        }
    
        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks
        ]);
    }
    public function create()
    {
        $projects = Project::all();
        return Inertia::render('Tasks/Create', [
            'projects' => $projects,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $task = Task::create($validated);
        return response()->json($task, 201);
    }

    public function show(Task $task, Request $request)
    {
        $task = Task::with(['project', 'tags', 'timeSessions'])
        ->orderBy('id', 'desc')
        ->paginate(10);

        if ($request->wantsJson()) {
            return response()->json($task);
        }

        return Inertia::render('Tasks/Show', [
            'task' => $task,
        ]);
    }

    public function edit(Task $task)
    {
        $projects = Project::all();

        return Inertia::render('Tasks/Edit', [
            'task' => $task,
            'projects' => $projects,
        ]);
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'project_id' => 'nullable|exists:projects,id', 
        ]);

        $task->update($validated);
        return response()->json($task, 200);
    }

        public function destroy(Task $task)
        {
            $task->delete();
            return response()->json(null, 204);
        }
        public function attachTags(Request $request, Task $task)
    {
        $tagIds = $request->input('tags', []);
        $task->tags()->syncWithoutDetaching($tagIds);

        return response()->json(['status' => 'ok']);
    }
        public function detachTag(Task $task, Tag $tag)
        {
            $task->tags()->detach($tag->id);

            return response()->json(['status' => 'ok']);
        }
}
