@extends('admin.layouts.admin_layout')

@section('title', 'Street Food Management - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Dashboard</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Street Food Management</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Street Food Management</h2>
        <p class="text-slate-500 text-sm mt-1">Manage non-packaged foods, nutritional data, and regional variants.</p>
    </div>
    <a href="{{ route('admin.street-foods.create') }}" class="flex items-center space-x-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-all">
        <span class="material-icons-round text-lg">add</span>
        <span class="text-sm font-bold">Add New Food</span>
    </a>
</div>

<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/30 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                    <th class="px-6 py-4">Food Name</th>
                    <th class="px-6 py-4">Category</th>
                    <th class="px-6 py-4">Typical Calories</th>
                    <th class="px-6 py-4">Halal Status</th>
                    <th class="px-6 py-4">Variants</th>
                    <th class="px-6 py-4">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($foods as $food)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden">
                                @if($food->image_url)
                                <img src="{{ asset($food->image_url) }}" alt="{{ $food->name }}" class="w-full h-full object-cover">
                                @else
                                <span class="material-icons-round text-slate-400 text-xl">restaurant</span>
                                @endif
                            </div>
                            <span class="text-sm font-bold text-slate-800 dark:text-white">{{ $food->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs font-medium px-2 py-1 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">{{ $food->category }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                        {{ $food->calories_typical }} kcal
                    </td>
                    <td class="px-6 py-4">
                        @if($food->halal_status == 'halal_umum')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600">HALAL</span>
                        @elseif($food->halal_status == 'syubhat' || $food->halal_status == 'tergantung_bahan')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 dark:bg-amber-900/30 text-amber-600">SYUBHAT</span>
                        @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 dark:bg-red-900/30 text-red-600">NON-HALAL</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.street-foods.variants', $food->id) }}" class="text-xs font-bold text-primary hover:underline">
                            {{ $food->variants_count }} Variants
                        </a>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.street-foods.edit', $food->id) }}" class="p-2 text-slate-400 hover:text-primary rounded-lg transition-all">
                                <span class="material-icons-round text-lg">edit</span>
                            </a>
                            <form action="{{ route('admin.street-foods.destroy', $food->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this food?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-500 rounded-lg transition-all">
                                    <span class="material-icons-round text-lg">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-400">No street foods found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $foods->links() }}
@endsection
