<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    // Specify the fields that can be mass-assigned
    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
        'user_id'
    ];
}
