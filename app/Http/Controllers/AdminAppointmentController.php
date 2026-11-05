<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Dentist;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminAppointmentController extends Controller
{
    // READ: all appointments, with filters by status, dentist, and date
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'dentist_id', 'date']);

        $appointments = Appointment::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('dentist_id'), fn ($q) => $q->where('dentist_id', $request->input('dentist_id')))
            ->when($request->filled('date'), fn ($q) => $q->where('date', $request->input('date')))
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->paginate(15)
            ->withQueryString();

        $dentists = Dentist::orderBy('name')->get();

        return view('admin.appointments.index', compact('appointments', 'dentists', 'filters'));
    }

    // UPDATE: change the status (only allowed changes, example: pending -> confirmed)
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $allowed = Appointment::TRANSITIONS[$appointment->status] ?? [];

        $request->validate([
            'status' => ['required', Rule::in($allowed)],
        ], [
            'status.in' => 'That status change is not allowed.',
        ]);

        $appointment->update(['status' => $request->input('status')]);

        return back()->with('status', 'Appointment marked as ' . Appointment::STATUSES[$request->input('status')] . '.');
    }
}