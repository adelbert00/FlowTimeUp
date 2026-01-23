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
        public ?float $hourlyRate = null,
        public ?string $currency = null,
        public ?bool $completed = false,
        public ?bool $isRecurring = false,
        public ?string $recurrenceType = null,
        public ?int $recurrenceInterval = null,
        public ?string $recurrenceEndsAt = null,
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
            hourlyRate: isset($data['hourly_rate']) ? (float) $data['hourly_rate'] : null,
            currency: $data['currency'] ?? null,
            completed: $data['completed'] ?? false,
            isRecurring: $data['is_recurring'] ?? false,
            recurrenceType: $data['recurrence_type'] ?? null,
            recurrenceInterval: $data['recurrence_interval'] ?? null,
            recurrenceEndsAt: $data['recurrence_ends_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        $result = [
            'title' => $this->title,
            'project_id' => $this->projectId,
            'user_id' => $this->userId,
            'description' => $this->description,
            'due_date' => $this->dueDate,
            'priority' => $this->priority,
            'completed' => $this->completed,
            'is_recurring' => $this->isRecurring,
            'recurrence_type' => $this->recurrenceType,
            'recurrence_interval' => $this->recurrenceInterval,
            'recurrence_ends_at' => $this->recurrenceEndsAt,
        ];

        if ($this->hourlyRate !== null) {
            $result['hourly_rate'] = $this->hourlyRate;
        }
        if ($this->currency !== null) {
            $result['currency'] = $this->currency;
        }

        return array_filter($result, fn($value) => $value !== null);
    }
}
