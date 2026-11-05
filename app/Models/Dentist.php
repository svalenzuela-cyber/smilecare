<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Dentist extends Model
{
    // Use the MongoDB connection and the "dentists" collection
    protected $connection = 'mongodb';
    protected $table = 'dentists';

    // Days of the week: short code saved in the database => label shown on screen
    public const DAYS = [
        'mon' => 'Monday',
        'tue' => 'Tuesday',
        'wed' => 'Wednesday',
        'thu' => 'Thursday',
        'fri' => 'Friday',
        'sat' => 'Saturday',
        'sun' => 'Sunday',
    ];

    // Fields that are allowed to be saved from the form
    protected $fillable = [
        'name',
        'specialization',
        'bio',
        'working_days',   // example: ['mon', 'wed', 'fri']
        'start_time',     // example: '09:00'
        'end_time',       // example: '17:00'
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Readable list of working days, example: "Mon, Wed, Fri"
    public function workingDaysLabel(): string
    {
        $days = $this->working_days ?? [];

        return collect(array_keys(self::DAYS))
            ->filter(fn ($day) => in_array($day, $days))
            ->map(fn ($day) => ucfirst($day))
            ->implode(', ');
    }

    // Readable working hours, example: "9:00 AM - 5:00 PM"
    public function workingHoursLabel(): string
    {
        $format = fn ($time) => date('g:i A', strtotime($time));

        return $format($this->start_time) . ' - ' . $format($this->end_time);
    }
}