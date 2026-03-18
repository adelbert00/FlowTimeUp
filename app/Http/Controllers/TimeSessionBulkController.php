<?php

namespace App\Http\Controllers;

use App\Models\TimeSession;
use App\Services\TimeTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimeSessionBulkController extends Controller
{
    protected $timeTrackingService;

    public function __construct(TimeTrackingService $timeTrackingService)
    {
        $this->timeTrackingService = $timeTrackingService;
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:time_sessions,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'is_billable' => ['nullable', 'boolean'],
            'start_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:start_time'],
        ]);

        $userId = auth()->id();

        $count = TimeSession::whereIn('id', $validated['ids'])
            ->where('user_id', $userId)
            ->count();

        if ($count !== count(array_unique($validated['ids']))) {
            abort(403, 'Unauthorized access to some sessions.');
        }

        $updateData = collect($validated)->except(['ids'])->filter(fn($value) => $value !== null)->toArray();

        if (empty($updateData)) {
            return response()->json(['message' => 'No data provided for update.'], 422);
        }

        DB::transaction(function () use ($validated, $updateData, $userId) {
            TimeSession::whereIn('id', $validated['ids'])
                ->where('user_id', $userId)
                ->update($updateData);

            $this->timeTrackingService->invalidateCache($userId);
        });

        return response()->json(['message' => 'Sessions updated successfully.']);
    }

    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:time_sessions,id'],
        ]);

        $userId = auth()->id();

        $count = TimeSession::whereIn('id', $validated['ids'])
            ->where('user_id', $userId)
            ->count();

        if ($count !== count(array_unique($validated['ids']))) {
            abort(403, 'Unauthorized access to some sessions.');
        }

        DB::transaction(function () use ($validated, $userId) {
            TimeSession::whereIn('id', $validated['ids'])
                ->where('user_id', $userId)
                ->delete();

            $this->timeTrackingService->invalidateCache($userId);
        });

        return response()->json(['message' => 'Sessions deleted successfully.']);
    }
}
