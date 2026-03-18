<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TaskBulkController extends Controller
{
    public function destroy(Request ): RedirectResponse
    {
        ->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:tasks,id,user_id,' . ->user()->id,
        ]);

        Task::whereIn('id', ->ids)->delete();

        return redirect()->back()->with('success', count(->ids) . ' tasks deleted successfully.');
    }

    public function update(Request ): RedirectResponse
    {
        ->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:tasks,id,user_id,' . ->user()->id,
            'project_id' => 'nullable|exists:projects,id,user_id,' . ->user()->id,
            'completed' => 'nullable|boolean',
        ]);

        $updateData = [];
        if ($request->has('project_id')) {
            $updateData['project_id'] = $request->project_id;
        }
        if ($request->has('completed')) {
            $updateData['completed'] = $request->completed;
        }

        if (!empty($updateData)) {
            Task::whereIn('id', $request->ids)->update($updateData);
        }

        return redirect()->back()->with('success', count($request->ids) . ' tasks updated successfully.');
    }
}
