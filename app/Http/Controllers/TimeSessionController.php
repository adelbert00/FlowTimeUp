<?php

namespace App\Http\Controllers;

use App\Models\TimeSession;
use App\Models\Task;
use Illuminate\Http\Request;

class TimeSessionController extends Controller
{
    public function index(Request $request)
    {
        $sessions = TimeSession::whereHas('task', function ($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->with('task')->orderBy('start_time', 'desc')->get();

        if ($request->wantsJson()) {
            return response()->json($sessions);
        }

        return inertia('TimeSessions/Index', [
            'sessions' => $sessions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'is_billable' => 'boolean',
            'billable_rate' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        $task = Task::findOrFail($validated['task_id']);
        $this->authorize('update', $task);

        $validated['is_billable'] = $validated['is_billable'] ?? true;

        TimeSession::create($validated);

        return redirect()->back()->with('success', 'Time session created!');
    }

    public function show(TimeSession $timeSession, Request $request)
    {
        $timeSession->load('task');
        $this->authorize('view', $timeSession);

        if ($request->wantsJson()) {
            return response()->json($timeSession);
        }

        return inertia('TimeSessions/Show', [
            'timeSession' => $timeSession,
        ]);
    }

    public function update(Request $request, TimeSession $timeSession)
    {
        $timeSession->load('task');
        $this->authorize('update', $timeSession);

        $validated = $request->validate([
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'is_billable' => 'boolean',
            'billable_rate' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        $timeSession->update($validated);

        return redirect()->back()->with('success', 'Time session updated!');
    }

    public function destroy(TimeSession $timeSession)
    {
        $timeSession->load('task');
        $this->authorize('delete', $timeSession);
        $timeSession->delete();

        return redirect()->back()->with('success', 'Time session deleted!');
    }
}
