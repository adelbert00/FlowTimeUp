<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $totalTimeSeconds = $this->relationLoaded('timeSessions') ? $this->calculateTotalTimeSeconds() : 0;
        $billableSeconds = $this->relationLoaded('timeSessions') ? $this->calculateBillableSeconds() : 0;
        $earnings = $this->calculateEarnings();
        
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'project_id' => $this->project_id,
            'user_id' => $this->user_id,
            'due_date' => $this->due_date?->format('Y-m-d'),
            'priority' => $this->priority,
            'hourly_rate' => $this->hourly_rate ? (float) $this->hourly_rate : null,
            'currency' => $this->currency ?? 'USD',
            'completed' => $this->completed ?? false,
            'is_recurring' => $this->is_recurring ?? false,
            'recurrence_type' => $this->recurrence_type,
            'recurrence_interval' => $this->recurrence_interval ?? 1,
            'recurrence_ends_at' => $this->recurrence_ends_at?->format('Y-m-d'),
            'next_occurrence' => $this->next_occurrence?->format('Y-m-d'),
            'parent_task_id' => $this->parent_task_id,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'project' => $this->whenLoaded('project', fn() => new ProjectResource($this->project)),
            'tags' => $this->relationLoaded('tags') ? TagResource::collection($this->tags)->resolve() : [],
            'time_sessions' => $this->whenLoaded('timeSessions', fn() => TimeSessionResource::collection($this->timeSessions)),
            'total_time' => $this->when(
                $this->relationLoaded('timeSessions'),
                fn() => $this->formatTime($totalTimeSeconds)
            ),
            'total_time_seconds' => $totalTimeSeconds,
            'billable_time' => $this->when(
                $this->relationLoaded('timeSessions'),
                fn() => $this->formatTime($billableSeconds)
            ),
            'billable_seconds' => $billableSeconds,
            'earnings' => $earnings ? round($earnings, 2) : null,
        ];
    }

    private function calculateTotalTimeSeconds(): int
    {
        if (!$this->relationLoaded('timeSessions')) {
            return 0;
        }

        return $this->timeSessions->sum(function ($session) {
            if (!$session->end_time) {
                return 0;
            }
            return $session->start_time->diffInSeconds($session->end_time);
        });
    }

    private function calculateBillableSeconds(): int
    {
        if (!$this->relationLoaded('timeSessions')) {
            return 0;
        }

        return $this->timeSessions->filter(fn($s) => $s->is_billable)->sum(function ($session) {
            if (!$session->end_time) {
                return 0;
            }
            return $session->start_time->diffInSeconds($session->end_time);
        });
    }

    private function calculateEarnings(): ?float
    {
        if (!$this->relationLoaded('timeSessions')) {
            return null;
        }

        $total = 0;
        foreach ($this->timeSessions as $session) {
            if (!$session->is_billable || !$session->end_time) {
                continue;
            }
            $rate = $session->billable_rate ?? $this->hourly_rate;
            if ($rate) {
                $hours = $session->start_time->diffInSeconds($session->end_time) / 3600;
                $total += $hours * $rate;
            }
        }

        return $total > 0 ? $total : null;
    }

    private function formatTime(int $totalSeconds): string
    {
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }
}
