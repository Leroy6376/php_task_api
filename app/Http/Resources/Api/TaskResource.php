<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[
    OA\Schema(
        title: 'TaskResource',
        properties: [
            new OA\Property(
                property: 'id',
                description: 'Уникальный идентификатор задачи',
                type: 'integer',
                example: 1
            ),
            new OA\Property(
                property: 'title',
                description: 'Название задачи',
                type: 'string',
                example: 'Задача1'
            ),
            new OA\Property(
                property: 'description',
                description: 'Описание задачи',
                type: 'string',
                example: 'Задача1 описание'
            ),
            new OA\Property(
                property: 'due_date',
                description: 'Срок выполнения',
                type: 'date-time',
                example: '2025-01-20T15:00:00'
            ),
            new OA\Property(
                property: 'create_date',
                description: 'Дата создания',
                type: 'date-time',
                example: '2025-01-20T15:00:00'
            ),
            new OA\Property(
                property: 'status',
                description: 'Статус задачи',
                type: 'string',
                example: 'не выполнена'
            ),
            new OA\Property(
                property: 'priority',
                description: 'Приоритет задачи',
                type: 'string',
                example: 'высокий'
            ),
            new OA\Property(
                property: 'category',
                description: 'Категория задачи',
                type: 'string',
                example: 'Работа'
            )
        ],
        xml: new OA\Xml(name: 'TaskResource'),
    )
]

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date,
            'create_date' => $this->create_date,
            'status' => $this->status,
            'priority' => $this->priority,
            'category' => $this->category,
        ];
    }
}
