<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Dentist;
use App\Models\Service;
use Illuminate\Http\Request;    
use Illuminate\Validation\ValidationException;

class AppointmentController extends Controller
{
    // READ: the logged-in patient's own appointments
    public function index(Request $request)
    {
        $appointments = Appointment::where('patient_id', (string) $request->user()->getKey())
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->paginate(10);

        return view('appointments.index', compact('appointments'));
    }

    // CREATE (step 1): show the booking form
    public function create()
    {
        $services = Service::where('is_active', true)->orderBy('name')->get();
        $dentists = Dentist::where('is_active', true)->orderBy('name')->get();

        return view('appointments.create', compact('services', 'dentists'));
    }

    // CREATE (step 2): validate, check availability, then save
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => ['required', 'string'],
            'dentist_id' => ['required', 'string'],
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'time' => ['required', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'service_id.required' => 'Choose a service.',
            'dentist_id.required' => 'Choose a dentist.',
            'date.after_or_equal' => 'Choose today or a future date.',
        ]);

        // The service must exist and be active
        $service = Service::where('is_active', true)->find($validated['service_id']);
        if (! $service) {
            throw ValidationException::withMessages(['service_id' => 'Choose a valid service.']);
        }

        // The time must not have passed already
        if (strtotime("{$validated['date']} {$validated['time']}") <= time()) {
            throw ValidationException::withMessages(['time' => 'Choose a time that has not passed yet.']);
        }

        // Find the chosen dentist (or any free dentist) and check availability
        $dentist = $this->findAvailableDentist($validated['dentist_id'], $service, $validated['date'], $validated['time']);

        Appointment::create([
            'patient_id' => (string) $request->user()->getKey(),
            'patient_name' => $request->user()->name,
            'service_id' => (string) $service->getKey(),
            'service_name' => $service->name,
            'service_price' => $service->price,
            'duration_minutes' => $service->duration_minutes,
            'dentist_id' => (string) $dentist->getKey(),
            'dentist_name' => $dentist->name,
            'date' => $validated['date'],
            'time' => $validated['time'],
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('appointments.index')
            ->with('status', "Appointment booked with {$dentist->name}. Please wait for the clinic to confirm it.");
    }

    // Patient cancels their own appointment
    public function cancel(Request $request, Appointment $appointment)
    {
        // Patients can only touch their OWN appointments
        abort_unless($appointment->patient_id === (string) $request->user()->getKey(), 403);

        if (! in_array($appointment->status, Appointment::ACTIVE_STATUSES) || ! $appointment->isUpcoming()) {
            return back()->with('error', 'This appointment can no longer be cancelled.');
        }

        $appointment->update(['status' => 'cancelled']);

        return back()->with('status', 'Appointment cancelled.');
    }

    // Returns an available dentist, or stops with an error message
    private function findAvailableDentist(string $dentistId, Service $service, string $date, string $time): Dentist
    {
        // "Any available dentist": try each active dentist until one is free
        if ($dentistId === 'any') {
            foreach (Dentist::where('is_active', true)->orderBy('name')->get() as $dentist) {
                if ($dentist->availabilityProblem($service, $date, $time) === null) {
                    return $dentist;
                }
            }

            throw ValidationException::withMessages(['time' => 'No dentist is available at that date and time. Please try another time.']);
        }

        $dentist = Dentist::find($dentistId);
        if (! $dentist) {
            throw ValidationException::withMessages(['dentist_id' => 'Choose a valid dentist.']);
        }

        $problem = $dentist->availabilityProblem($service, $date, $time);
        if ($problem) {
            throw ValidationException::withMessages(['time' => $problem]);
        }

        return $dentist;
    }
}