@extends('admin.layouts.admin_layout')

@section('title', 'Manage Variants - ' . $streetFood->name)

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.street-foods.index') }}" class="p-2 bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 hover:text-primary transition-all">
                <span class="material-icons-round text-lg">arrow_back</span>
            </a>
            <div>
                <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Variants of {{ $streetFood->name }}</h2>
                <p class="text-slate-500 text-sm mt-1">Manage different toppings or styles of this food.</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- New Variant Form -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 sticky top-8">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Add New Variant</h3>
                <form action="{{ route('admin.street-foods.variants.store', $streetFood->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Variant Name</label>
                        <input type="text" name="name" required class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g. Spesial Telur">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Calories Modifier</label>
                        <input type="number" name="calories_modifier" required class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="e.g. +150">
                        <p class="text-[10px] text-slate-400">Additional calories added to base food.</p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Halal Modifier (Optional)</label>
                        <select name="halal_modifier" class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary">
                            <option value="">No change</option>
                            <option value="halal">Force Halal</option>
                            <option value="syubhat">Mark as Syubhat</option>
                            <option value="haram">Mark as Haram</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full py-2 bg-primary text-white rounded-lg text-sm font-bold hover:bg-primary-dark transition-all shadow-md shadow-primary/20">Add Variant</button>
                </form>
            </div>
        </div>

        <!-- Variants List -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/30 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="px-6 py-4">Variant Name</th>
                            <th class="px-6 py-4">Calorie Mod</th>
                            <th class="px-6 py-4">Halal Status</th>
                            <th class="px-6 py-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($variants as $variant)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-800 dark:text-white text-sm">{{ $variant->name }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                @if($variant->calories_modifier >= 0)
                                    <span class="text-emerald-500">+{{ $variant->calories_modifier }}</span>
                                @else
                                    <span class="text-red-500">{{ $variant->calories_modifier }}</span>
                                @endif
                                kcal
                            </td>
                            <td class="px-6 py-4">
                                @if($variant->halal_modifier)
                                    <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500">
                                        Overrides to {{ $variant->halal_modifier }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400 italic">Inherited</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.street-foods.variants.destroy', $variant->id) }}" method="POST" onsubmit="return confirm('Delete this variant?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-red-500 rounded-lg transition-all">
                                        <span class="material-icons-round text-lg">delete</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400">No variants added yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
