<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Service extends Model
{
    // Use the MongoDB connection and the "services" collection
    protected $connection = 'mongodb';
    protected $table = 'services';

    // Fields that are allowed to be saved from the form
    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_minutes',
        'is_active',
    ];

    // Make sure each field is stored with the correct type
    protected $casts = [
        'price' => 'float',
        'duration_minutes' => 'integer',
        'is_active' => 'boolean',
    ];
}