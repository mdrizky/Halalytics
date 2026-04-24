@extends('admin.layouts.admin_layout')

@section('title', 'Forbidden Ingredients - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Dashboard</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Forbidden Ingredients</span>
@endsection

@section('content')
<!-- Page Title -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight flex items-center gap-3">
            <span class="material-icons-round text-rose-500 text-3xl">block</span>
            Forbidden Ingredients Database
        </h2>
        <p class="text-slate-500 text-sm mt-1">Manage halal-critical, health-hazard, and allergen ingredients.</p>
    </div>
    <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="flex items-center space-x-2 px-4 py-2.5 bg-rose-500 hover:bg-rose-600 text-white rounded-lg transition-all text-sm font-bold shadow-lg shadow-rose-500/25">
        <span class="material-icons-round text-lg">add</span>
        <span>Add Ingredient</span>
    </button>
</div>

@if(session('success'))
<div class="mb-6 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg text-emerald-700 dark:text-emerald-300 text-sm font-medium flex items-center gap-2">
    <span class="material-icons-round text-lg">check_circle</span>
    {{ session('success') }}
</div>
@endif

<!-- Search -->
<div class="mb-6">
    <form method="GET" class="flex gap-3">
        <div class="flex-1 relative">
            <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl">search</span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ingredients..." class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-rose-500">
        </div>
        <button type="submit" class="px-5 py-2.5 bg-slate-800 text-white rounded-lg text-sm font-bold hover:bg-slate-700 transition-all">Filter</button>
    </form>
</div>

<!-- Table -->
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-left pl-10">Ingredient</th>
                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-center">Code</th>
                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-center">Type</th>
                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-center">Risk</th>
                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-left">Reason</th>
                <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($ingredients as $ing)
            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-all">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 flex items-center justify-center flex-shrink-0">
                            <img src="{{ $ing->image }}" alt="{{ $ing->name }}" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name='+encodeURIComponent('{{ $ing->name }}')+'&background=random';">
                        </div>
                        <div>
                            <div class="font-bold text-sm text-slate-800 dark:text-white">{{ $ing->name }}</div>
                            @if($ing->aliases && count($ing->aliases) > 0)
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach(array_slice($ing->aliases, 0, 3) as $alias)
                                <span class="text-[10px] bg-slate-100 dark:bg-slate-800 text-slate-500 px-1.5 py-0.5 rounded font-medium">{{ $alias }}</span>
                                @endforeach
                                @if(count($ing->aliases) > 3)
                                <span class="text-[10px] text-slate-400">+{{ count($ing->aliases) - 3 }} more</span>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="text-xs font-mono font-bold text-primary">{{ $ing->code ?? '-' }}</span>
                </td>
                <td class="px-6 py-4 text-center">
                    @if($ing->type == 'halal_haram')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-400 border border-rose-200 dark:border-rose-800">Halal/Haram</span>
                    @elseif($ing->type == 'health_hazard')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-400 border border-amber-200 dark:border-amber-800">Health Hazard</span>
                    @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-primary/10 text-primary border border-primary/20">Allergen</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-center">
                    @if($ing->risk_level == 'high')
                    <span class="inline-flex items-center gap-0.5 text-xs font-bold text-rose-500"><span class="material-icons-round text-sm">error</span> HIGH</span>
                    @elseif($ing->risk_level == 'medium')
                    <span class="inline-flex items-center gap-0.5 text-xs font-bold text-amber-500"><span class="material-icons-round text-sm">warning</span> MED</span>
                    @else
                    <span class="inline-flex items-center gap-0.5 text-xs font-bold text-primary"><span class="material-icons-round text-sm">info</span> LOW</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 max-w-xs truncate">{{ $ing->reason }}</td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <button onclick="openEditModal({{ $ing->id }}, '{{ addslashes($ing->name) }}', '{{ $ing->code }}', '{{ $ing->type }}', '{{ $ing->risk_level }}', '{{ addslashes($ing->reason) }}', '{{ addslashes($ing->description) }}', '{{ $ing->aliases ? implode(', ', $ing->aliases) : '' }}')" class="p-1.5 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-all">
                            <span class="material-icons-round text-lg">edit</span>
                        </button>
                        <form action="{{ route('admin.forbidden.destroy', $ing->id) }}" method="POST" onsubmit="return confirm('Delete this ingredient?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-lg transition-all">
                                <span class="material-icons-round text-lg">delete</span>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                    <span class="material-icons-round text-4xl mb-2">science</span>
                    <p class="font-medium">No forbidden ingredients found</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">{{ $ingredients->links() }}</div>
</div>

<!-- Add Modal -->
<div id="addModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-2xl w-full max-w-lg shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Add Forbidden Ingredient</h3>
        </div>
        <form action="{{ route('admin.forbidden.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div><label class="text-xs font-bold text-slate-500 uppercase">Name *</label><input type="text" name="name" required class="w-full mt-1 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm" placeholder="e.g. Carmine"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase">Code (E-Number)</label><input type="text" name="code" class="w-full mt-1 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm" placeholder="e.g. E120"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase">Aliases (comma separated)</label><input type="text" name="aliases" class="w-full mt-1 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm" placeholder="e.g. Cochineal, CI 75470"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase">Type *</label>
                        <select name="type" required class="w-full mt-1 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm">
                            <option value="halal_haram">Halal/Haram</option>
                            <option value="health_hazard">Health Hazard</option>
                            <option value="allergen">Allergen</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase">Risk Level *</label>
                        <select name="risk_level" required class="w-full mt-1 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm">
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                </div>
                <div><label class="text-xs font-bold text-slate-500 uppercase">Reason *</label><input type="text" name="reason" required class="w-full mt-1 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm" placeholder="e.g. Haram"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase">Description</label><textarea name="description" rows="2" class="w-full mt-1 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm" placeholder="Optional description"></textarea></div>
            </div>
            <div class="p-6 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-rose-500 text-white rounded-lg text-sm font-bold hover:bg-rose-600 shadow-lg shadow-rose-500/25">Add Ingredient</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-2xl w-full max-w-lg shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Edit Ingredient</h3>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4">
                <div><label class="text-xs font-bold text-slate-500 uppercase">Name *</label><input type="text" name="name" id="edit_name" required class="w-full mt-1 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase">Code</label><input type="text" name="code" id="edit_code" class="w-full mt-1 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase">Aliases</label><input type="text" name="aliases" id="edit_aliases" class="w-full mt-1 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase">Type *</label>
                        <select name="type" id="edit_type" required class="w-full mt-1 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm">
                            <option value="halal_haram">Halal/Haram</option>
                            <option value="health_hazard">Health Hazard</option>
                            <option value="allergen">Allergen</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase">Risk Level *</label>
                        <select name="risk_level" id="edit_risk" required class="w-full mt-1 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm">
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                </div>
                <div><label class="text-xs font-bold text-slate-500 uppercase">Reason *</label><input type="text" name="reason" id="edit_reason" required class="w-full mt-1 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase">Description</label><textarea name="description" id="edit_description" rows="2" class="w-full mt-1 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm"></textarea></div>
            </div>
            <div class="p-6 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-bold hover:bg-primary-dark shadow-lg shadow-primary/20">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditModal(id, name, code, type, risk, reason, description, aliases) {
    document.getElementById('editForm').action = '/admin/forbidden/' + id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_code').value = code || '';
    document.getElementById('edit_type').value = type;
    document.getElementById('edit_risk').value = risk;
    document.getElementById('edit_reason').value = reason;
    document.getElementById('edit_description').value = description || '';
    document.getElementById('edit_aliases').value = aliases || '';
    document.getElementById('editModal').classList.remove('hidden');
}
</script>
@endpush
