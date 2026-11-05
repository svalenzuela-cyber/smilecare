<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Appointment extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'appointments';

    // All possible statuses: code saved in the database => label shown on screen
    public const STATUSES = [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'no_show' => 'No-show',
    ];

    // Statuses that still take up the dentist's time slot
    public const ACTIVE_STATUSES = ['pending', 'confirmed'];

    // Status changes the admin is allowed to make
    public const TRANSITIONS = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['completed', 'no_show', 'cancelled'],
        'completed' => [],
        'cancelled' => [],
        'no_show' => [],
    ];

    protected $fillable = [
        // IDs link to the other collections
        'patient_id',
        'service_id',
        'dentist_id',
        // Copies of names and price at the time of booking
        'patient_name',
        'service_name',
        'service_price',
        'duration_minutes',
        'dentist_name',
        // Booking details
        'date',     // example: '2026-11-10'
        'time',     // example: '14:00'
        'status',
        'notes',
    ];

    protected $casts = [
        'service_price' => 'float',
        'duration_minutes' => 'integer',
    ];

    // Example: "Tue, Nov 10, 2026"
    public function dateLabel(): string
    {
        return date('D, M j, Y', strtotime($this->date));
    }

    // Example: "2:00 PM - 2:30 PM" (end time is based on the service duration)
    public function timeLabel(): string
    {
        $start = strtotime("{$this->date} {$this->time}");
        $end = $start + ($this->duration_minutes * 60);

        return date('g:i A', $start) . ' - ' . date('g:i A', $end);
    }

    // True if the appointment has not happened yet
    public function isUpcoming(): bool
    {
        return strtotime("{$this->date} {$this->time}") > time();
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }
}