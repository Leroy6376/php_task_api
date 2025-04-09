<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\Api\TaskFactory> */
    use HasFactory;

    protected $guarded = false;
    protected $table = 'tasks';
}
