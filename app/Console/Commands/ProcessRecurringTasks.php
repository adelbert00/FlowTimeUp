<?php

namespace App\Console\Commands;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessRecurringTasks extends Command
{
    protected $signature = 'tasks:process-recurring';
    protected $description = 'Process recurring tasks and create new instances';

    public function handle(): int
    {
        $today = Carbon::today();

        $recurringTasks = Task::where('is_recurring', true)
            ->whereNotNull('next_occurrence')
            ->where('next_occurrence', '<=', $today)
            ->where(function ($query) use ($today) {
                $query->whereNull('recurrence_ends_at')
                    ->orWhere('recurrence_ends_at', '>=', $today);
            })
            ->get();

        $created = 0;

        foreach ($recurringTasks as $task) {
            $newTask = $task->replicate();
            $newTask->due_date = $task->next_occurrence;
            $newTask->completed = false;
            $newTask->parent_task_id = $task->id;
            $newTask->is_recurring = false;
            $newTask->recurrence_type = null;
            $newTask->recurrence_interval = null;
            $newTask->recurrence_ends_at = null;
            $newTask->next_occurrence = null;
            $newTask->save();

            if ($task->tags->isNotEmpty()) {
                $newTask->tags()->attach($task->tags->pluck('id'));
            }

            $nextOccurrence = $task->calculateNextOccurrence();
            
            if ($nextOccurrence) {
                $task->update(['next_occurrence' => $nextOccurrence]);
            } else {
                $task->update(['is_recurring' => false, 'next_occurrence' => null]);
            }

            $created++;
        }

        $this->info("Processed {$created} recurring tasks.");

        return Command::SUCCESS;
    }
}
