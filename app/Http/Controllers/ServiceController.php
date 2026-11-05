<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    // READ: show the list of services (with optional search)
    public function index(Request $request)
    {
        $search = $request->input('search');

        $services = Service::query()
            ->when($search, fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.services.index', compact('services', 'search'));
    }

    // CREATE (step 1): show the empty "Add service" form
    public function create()
    {
        return view('admin.services.create');
    }

    // CREATE (step 2): validate the form and save the new service
    public function store(Request $request)
    {
        Service::create($this->validateService($request));

        return redirect()->route('admin.services.index')->with('status', 'Service added.');
    }

    // UPDATE (step 1): show the form filled with the current values
    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    // UPDATE (step 2): validate the form and save the changes
    public function update(Request $request, Service $service)
    {
        $service->update($this->validateService($request));

        return redirect()->route('admin.services.index')->with('status', 'Service updated.');
    }

    // DELETE: remove the service
    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('admin.services.index')->with('status', 'Service deleted.');
    }

    // Shared validation rules for creating and updating
    private function validateService(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0', 'max:100000'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
        ]);

        // Checkbox: checked = true, unchecked = false
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}