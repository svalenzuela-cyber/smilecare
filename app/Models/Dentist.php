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

    /**
     * AVAILABILITY CHECK
     * Returns null if the dentist can take this booking,
     * or a message explaining why not.
     */
    public function availabilityProblem(Service $service, string $date, string $time): ?string
    {
        // 1. The dentist must be active
        if (! $this->is_active) {
            return "{$this->name} is not accepting bookings right now.";
        }

        // 2. The dentist must work on that day of the week
        $day = strtolower(date('D', strtotime($date)));   // "mon", "tue", ...
        if (! in_array($day, $this->working_days ?? [])) {
            return "{$this->name} does not work on " . self::DAYS[$day] . 's. Works on: ' . $this->workingDaysLabel() . '.';
        }

        // 3. The whole service must fit inside the dentist's working hours
        $start = self::toMinutes($time);
        $end = $start + (int) $service->duration_minutes;

        if ($start < self::toMinutes($this->start_time) || $end > self::toMinutes($this->end_time)) {
            return "{$this->name} works from {$this->workingHoursLabel()}. "
                . "The {$service->duration_minutes}-minute {$service->name} must start and finish within those hours.";
        }

        // 4. The time must not overlap with another pending/confirmed appointment (no double booking)
        $bookedAppointments = Appointment::where('dentist_id', (string) $this->getKey())
            ->where('date', $date)
            ->whereIn('status', Appointment::ACTIVE_STATUSES)
            ->get();

        foreach ($bookedAppointments as $booked) {
            $bookedStart = self::toMinutes($booked->time);
            $bookedEnd = $bookedStart + (int) $booked->duration_minutes;

            // Two time ranges overlap if each one starts before the other ends
            if ($start < $bookedEnd && $bookedStart < $end) {
                return "{$this->name} is already booked from {$booked->timeLabel()} on that day. Please choose another time.";
            }
        }

        return null;   // available
    }

    // Converts "14:30" into minutes since midnight (870)
    public static function toMinutes(string $time): int
    {
        [$hours, $minutes] = array_map('intval', explode(':', $time));

        return $hours * 60 + $minutes;
    }
}