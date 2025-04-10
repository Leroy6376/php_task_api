<?php

namespace app\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[
    OA\Schema(
        title: 'TaskRequest',
        properties: [
            new OA\Property(
                property: 'title',
                description: 'Название задачи',
                type: 'string',
                example: 'Задача1',
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
        xml: new OA\Xml(name: 'TaskRequest'),
    )
]

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'create_date' => 'required|date',
            'status' => ['required', Rule::in(['выполнена', 'не выполнена'])],
            'priority' => ['required', Rule::in(['низкий', 'средний', 'высокий'])],
            'category' => 'required|string',
        ];
    }
}
