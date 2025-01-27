<?php

namespace App\Http\Controllers;

use App\Models\TimeSession;
use Illuminate\Http\Request;

class TimeSessionController extends Controller
{
    public function index(Request $request)
    {
        $sessions = TimeSession::with('task')->get();

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
            'end_time'   => 'nullable|date|after_or_equal:start_time',
        ]);

        TimeSession::create($validated);

        return redirect()->back()->with('success', 'Time session created!');
    }

    public function show(TimeSession $timeSession, Request $request)
    {
        $timeSession->load('task');

        if ($request->wantsJson()) {
            return response()->json($timeSession);
        }

        return inertia('TimeSessions/Show', [
            'timeSession' => $timeSession,
        ]);
    }

    public function edit(TimeSession $timeSession)
    {
        $timeSession->load('task');

        return inertia('TimeSessions/Edit', [
            'timeSession' => $timeSession,
        ]);
    }

    public function update(Request $request, TimeSession $timeSession)
    {
        $validated = $request->validate([
            'start_time' => 'required|date',
            'end_time'   => 'nullable|date|after_or_equal:start_time',
        ]);

        $timeSession->update($validated);

        return redirect()->back()->with('success', 'Time session updated!');
    }

    public function destroy(TimeSession $timeSession)
    {
        $timeSession->delete();

        return redirect()->back()->with('success', 'Time session deleted!');
    }
}
