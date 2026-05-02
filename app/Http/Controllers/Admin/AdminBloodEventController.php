<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BloodEvent;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AdminBloodEventController extends Controller
{
    public function index()
    {
        $events = BloodEvent::orderBy('event_date', 'desc')->paginate(15);
        return view('admin.blood_events', compact('events'));
    }

    public function create()
    {
        return view('admin.donor.events.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'address' => 'required|string',
            'event_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'quota' => 'required|integer|min:1',
            'organizer' => 'required|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
        ]);

        $validated['created_by'] = auth()->user()->id_user ?? null;
        $validated['status'] = 'active';

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/events');
            $validated['image_url'] = asset(str_replace('public/', 'storage/', $path));
        }

        BloodEvent::create($validated);

        return redirect()->route('admin.blood-events.index')->with('success', 'Event created successfully.');
    }

    public function edit($id)
    {
        $event = BloodEvent::findOrFail($id);
        return view('admin.donor.events.form', compact('event'));
    }

    public function update(Request $request, $id)
    {
        $event = BloodEvent::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'address' => 'required|string',
            'event_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'quota' => 'required|integer|min:1',
            'organizer' => 'required|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'status' => 'required|in:draft,active,ongoing,completed,cancelled',
            'image' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($event->image_url && str_contains($event->image_url, '/storage/events/')) {
                $oldPath = str_replace(asset('storage/'), 'public/', $event->image_url);
                Storage::delete($oldPath);
            }
            $path = $request->file('image')->store('public/events');
            $validated['image_url'] = asset(str_replace('public/', 'storage/', $path));
        }

        $event->update($validated);

        return redirect()->route('admin.blood-events.index')->with('success', 'Event updated successfully.');
    }

    public function destroy($id)
    {
        $event = BloodEvent::findOrFail($id);
        $event->delete();

        return redirect()->route('admin.blood-events.index')->with('success', 'Event deleted successfully.');
    }
}
