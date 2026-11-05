<?php

namespace App\Http\Controllers;

use App\Models\Dentist;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DentistController extends Controller
{
    // READ: list of dentists (search by name or specialization)
    public function index(Request $request)
    {
        $search = $request->input('search');

        $dentists = Dentist::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('specialization', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.dentists.index', compact('dentists', 'search'));
    }

    // CREATE (step 1): show the empty form
    public function create()
    {
        return view('admin.dentists.create');
    }

    // CREATE (step 2): validate and save
    public function store(Request $request)
    {
        Dentist::create($this->validateDentist($request));

        return redirect()->route('admin.dentists.index')->with('status', 'Dentist added.');
    }

    // UPDATE (step 1): show the form with current values
    public function edit(Dentist $dentist)
    {
        return view('admin.dentists.edit', compact('dentist'));
    }

    // UPDATE (step 2): validate and save changes
    public function update(Request $request, Dentist $dentist)
    {
        $dentist->update($this->validateDentist($request));

        return redirect()->route('admin.dentists.index')->with('status', 'Dentist updated.');
    }

    // DELETE: remove the dentist
    public function destroy(Dentist $dentist)
    {
        $dentist->delete();

        return redirect()->route('admin.dentists.index')->with('status', 'Dentist deleted.');
    }

    // Shared validation rules
    private function validateDentist(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'specialization' => ['required', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:500'],
            'working_days' => ['required', 'array', 'min:1'],
            'working_days.*' => [Rule::in(array_keys(Dentist::DAYS))],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ], [
            'working_days.required' => 'Select at least one working day.',
            'end_time.after' => 'End time must be later than start time.',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}