<?php

namespace App\DTO;

readonly class TaskDTO
{
    public function __construct(
        public string $title,
        public ?int $projectId = null,
        public ?int $userId = null,
        public ?array $tagIds = null,
        public ?string $description = null,
        public ?string $dueDate = null,
        public ?string $priority = null,
        public ?bool $completed = false,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            projectId: $data['project_id'] ?? null,
            userId: $data['user_id'] ?? null,
            tagIds: $data['tag_ids'] ?? null,
            description: $data['description'] ?? null,
            dueDate: $data['due_date'] ?? null,
            priority: $data['priority'] ?? null,
            completed: $data['completed'] ?? false,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'title' => $this->title,
            'project_id' => $this->projectId,
            'user_id' => $this->userId,
            'description' => $this->description,
            'due_date' => $this->dueDate,
            'priority' => $this->priority,
            'completed' => $this->completed,
        ], fn($value) => $value !== null);
    }
}
