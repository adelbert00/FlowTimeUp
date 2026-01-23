<?php

namespace App\Http\Controllers;

use App\DTO\TaskDTO;
use App\Http\Requests\Tasks\StoreTaskRequest;
use App\Http\Requests\Tasks\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Task::with(['project', 'tags', 'timeSessions'])
            ->where('user_id', $request->user()->id);

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('completed')) {
            $query->where('completed', $request->boolean('completed'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $tasks = $query->paginate($request->get('per_page', 15));

        return Inertia::render('Tasks/Index', [
            'tasks' => [
                'data' => array_map(fn($task) => (new TaskResource($task))->resolve(), $tasks->items()),
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
            ],
            'filters' => $request->only(['project_id', 'completed', 'priority', 'search', 'sort_by', 'sort_order']),
            'projects' => Project::where('user_id', $request->user()->id)->get(),
            'tags' => Tag::where('user_id', $request->user()->id)->where('is_archived', false)->get(),
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Tasks/Create', [
            'projects' => Project::where('user_id', $request->user()->id)->get(),
            'tags' => Tag::where('user_id', $request->user()->id)->where('is_archived', false)->get(),
        ]);
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $dto = TaskDTO::fromArray([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        $task = Task::create($dto->toArray());

        if ($dto->tagIds) {
            $task->tags()->attach($dto->tagIds);
        }

        if ($task->is_recurring && $task->due_date) {
            $task->update(['next_occurrence' => $task->calculateNextOccurrence()]);
        }

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function show(Task $task): Response
    {
        $this->authorize('view', $task);
        $task->load(['project', 'tags', 'timeSessions']);

        return Inertia::render('Tasks/Show', [
            'task' => new TaskResource($task),
        ]);
    }

    public function edit(Task $task, Request $request): Response
    {
        $this->authorize('update', $task);
        $task->load(['tags', 'timeSessions']);

        return Inertia::render('Tasks/Edit', [
            'task' => (new TaskResource($task))->resolve(),
            'projects' => Project::where('user_id', $request->user()->id)->get(),
            'tags' => Tag::where('user_id', $request->user()->id)->where('is_archived', false)->get(),
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $dto = TaskDTO::fromArray([
            ...$request->validated(),
            'user_id' => $task->user_id,
        ]);

        $task->update($dto->toArray());

        if ($request->has('tag_ids')) {
            $task->tags()->sync($request->tag_ids ?? []);
        }

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $this->authorize('delete', $task);
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    public function attachTags(Request $request, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $request->validate([
            'tags' => 'required|array',
            'tags.*' => 'exists:tags,id,user_id,' . $request->user()->id,
        ]);

        $task->tags()->syncWithoutDetaching($request->tags);

        return redirect()->back()->with('success', 'Tags attached successfully.');
    }

    public function detachTag(Task $task, Tag $tag, Request $request): RedirectResponse
    {
        $this->authorize('update', $task);

        if ($tag->user_id !== $request->user()->id) {
            abort(403);
        }

        $task->tags()->detach($tag->id);

        return redirect()->back()->with('success', 'Tag detached successfully.');
    }

    public function toggleComplete(Task $task): RedirectResponse
    {
        $this->authorize('update', $task);
        $task->update(['completed' => !$task->completed]);

        return redirect()->back()->with('success', 'Task status updated.');
    }
}
