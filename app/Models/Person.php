<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $table = 'people';

    // Casts for your fields (optional but recommended for data integrity)
    protected $casts = [
        'name' => 'string',
        'status' => 'integer', // Assuming status is stored as an integer
        'description' => 'string',
    ];
}
