@extends('admin.layouts.admin_layout')

@section('title', isset($event) ? 'Edit Blood Event' : 'Create Blood Event')

@section('breadcrumb')
<span class="text-slate-400">Dashboard</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<a href="{{ route('admin.blood-events.index') }}" class="text-slate-400 hover:text-primary">Blood Events</a>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">{{ isset($event) ? 'Edit Event' : 'Create Event' }}</span>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">{{ isset($event) ? 'Edit Event' : 'Create New Event' }}</h2>
            <p class="text-slate-500 text-sm mt-1">Fill out the details for the blood donation campaign.</p>
        </div>
        <a href="{{ route('admin.blood-events.index') }}" class="flex items-center space-x-2 px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
            <span class="material-icons-round text-lg">arrow_back</span>
            <span class="text-sm font-medium">Back</span>
        </a>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <form action="{{ isset($event) ? route('admin.blood-events.update', $event->id) : route('admin.blood-events.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($event)) @method('PUT') @endif
            
            <div class="p-6 space-y-6">
                <!-- Title & Organizer -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Event Title</label>
                        <input type="text" name="title" value="{{ old('title', $event->title ?? '') }}" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Organizer</label>
                        <input type="text" name="organizer" value="{{ old('organizer', $event->organizer ?? '') }}" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                </div>

                <!-- Location & Address -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Location Name</label>
                        <input type="text" name="location" value="{{ old('location', $event->location ?? '') }}" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Full Address</label>
                        <input type="text" name="address" value="{{ old('address', $event->address ?? '') }}" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                </div>

                <!-- Date, Time, Quota -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Date</label>
                        <input type="date" name="event_date" value="{{ old('event_date', isset($event) ? \Carbon\Carbon::parse($event->event_date)->format('Y-m-d') : '') }}" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Time (Start - End)</label>
                        <div class="flex items-center space-x-2">
                            <input type="time" name="start_time" value="{{ old('start_time', $event->start_time ?? '') }}" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary">
                            <span class="text-slate-400">-</span>
                            <input type="time" name="end_time" value="{{ old('end_time', $event->end_time ?? '') }}" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Participant Quota</label>
                        <input type="number" name="quota" value="{{ old('quota', $event->quota ?? 100) }}" min="1" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                </div>

                <!-- Phone & Status -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Contact Phone</label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone', $event->contact_phone ?? '') }}" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    @if(isset($event))
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Status</label>
                        <select name="status" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary">
                            <option value="draft" {{ $event->status == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="active" {{ $event->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="ongoing" {{ $event->status == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="completed" {{ $event->status == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $event->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    @endif
                </div>

                <!-- Image Upload -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Event Image / Poster (Optional)</label>
                    <div class="flex items-center space-x-4">
                        @if(isset($event) && $event->image_url)
                            <div class="h-20 w-20 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700 flex-shrink-0">
                                <img src="{{ $event->image_url }}" alt="Current image" class="h-full w-full object-cover">
                            </div>
                        @endif
                        <div class="flex-1">
                            <input type="file" name="image" accept="image/*" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary">
                            <p class="mt-1 text-xs text-slate-500">Recommended: Landscape or Square, Max 5MB</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.blood-events.index') }}" class="px-6 py-2.5 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 rounded-lg hover:bg-white dark:hover:bg-slate-800 transition-all text-sm font-medium">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-primary text-white rounded-lg hover:bg-primary-dark transition-all text-sm font-bold flex items-center space-x-2">
                    <span class="material-icons-round text-lg">save</span>
                    <span>{{ isset($event) ? 'Update Event' : 'Create Event' }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
