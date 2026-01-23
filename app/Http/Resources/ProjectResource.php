<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'color' => $this->color,
            'user_id' => $this->user_id,
            'hourly_rate' => $this->hourly_rate,
            'budget' => $this->budget,
            'currency' => $this->currency ?? 'USD',
            'access' => $this->access ?? 'public',
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'tasks_count' => $this->whenCounted('tasks'),
            'tasks' => $this->whenLoaded('tasks', fn() => TaskResource::collection($this->tasks)),
            'tracked_time' => $this->formatted_tracked_time,
            'tracked_seconds' => $this->total_tracked_seconds,
            'total_amount' => $this->total_amount,
        ];
    }
}
