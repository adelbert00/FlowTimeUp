<?php

namespace App\Services;

use Illuminate\Support\Collection;

class ReportService
{
    public function formatDuration(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    public function calculateSummary(Collection $sessions): array
    {
        $totalSeconds = 0;
        $billableSeconds = 0;
        $nonBillableSeconds = 0;
        $totalEarnings = 0;
        $byProject = [];
        $byTask = [];
        $byDate = [];

        foreach ($sessions as $session) {
            $duration = $session->start_time->diffInSeconds($session->end_time);
            $totalSeconds += $duration;

            $hourlyRate = $session->task->hourly_rate ?? $session->task->project?->hourly_rate ?? 0;
            $hours = $duration / 3600;
            $currency = $session->task->currency ?? $session->task->project?->currency ?? 'USD';

            if ($session->is_billable) {
                $billableSeconds += $duration;
                $totalEarnings += $hours * $hourlyRate;
            } else {
                $nonBillableSeconds += $duration;
            }

            $projectName = $session->task->project?->name ?? 'No project';
            $taskName = $session->task->title;
            $date = $session->start_time->format('Y-m-d');

            if (!isset($byProject[$projectName])) {
                $byProject[$projectName] = ['seconds' => 0, 'earnings' => 0, 'currency' => $currency];
            }
            $byProject[$projectName]['seconds'] += $duration;
            if ($session->is_billable) {
                $byProject[$projectName]['earnings'] += $hours * $hourlyRate;
            }

            $byTask[$taskName] = ($byTask[$taskName] ?? 0) + $duration;
            $byDate[$date] = ($byDate[$date] ?? 0) + $duration;
        }

        return [
            'total_time' => $this->formatDuration($totalSeconds),
            'total_seconds' => $totalSeconds,
            'total_sessions' => $sessions->count(),
            'billable_time' => $this->formatDuration($billableSeconds),
            'billable_seconds' => $billableSeconds,
            'non_billable_time' => $this->formatDuration($nonBillableSeconds),
            'non_billable_seconds' => $nonBillableSeconds,
            'total_earnings' => round($totalEarnings, 2),
            'by_project' => collect($byProject)->map(function ($data, $name) {
                return [
                    'name' => $name,
                    'duration' => $this->formatDuration($data['seconds']),
                    'seconds' => $data['seconds'],
                    'earnings' => round($data['earnings'], 2),
                    'currency' => $data['currency'],
                ];
            })->values(),
            'by_task' => collect($byTask)->map(function ($seconds, $name) {
                return [
                    'name' => $name,
                    'duration' => $this->formatDuration($seconds),
                    'seconds' => $seconds,
                ];
            })->sortByDesc('seconds')->take(10)->values(),
            'by_date' => collect($byDate)->map(function ($seconds, $date) {
                return [
                    'date' => $date,
                    'duration' => $this->formatDuration($seconds),
                    'seconds' => $seconds,
                ];
            })->sortByDesc('date')->values(),
        ];
    }

    public function formatSessionForExport($session): array
    {
        $duration = $session->start_time->diffInSeconds($session->end_time);
        $hourlyRate = $session->task->hourly_rate ?? $session->task->project?->hourly_rate ?? 0;
        $hours = $duration / 3600;
        $earnings = $session->is_billable ? round($hours * $hourlyRate, 2) : 0;

        return [
            'id' => $session->id,
            'task' => $session->task->title,
            'project' => $session->task->project?->name ?? 'No project',
            'tags' => $session->task->tags->pluck('name')->join(', ') ?: 'No tags',
            'start_time' => $session->start_time->format('Y-m-d H:i:s'),
            'end_time' => $session->end_time->format('Y-m-d H:i:s'),
            'duration' => $this->formatDuration($duration),
            'duration_seconds' => $duration,
            'is_billable' => $session->is_billable,
            'hourly_rate' => $hourlyRate,
            'currency' => $session->task->currency ?? $session->task->project?->currency ?? 'USD',
            'earnings' => $earnings,
            'notes' => $session->notes ?? '',
        ];
    }
}
