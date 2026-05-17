@extends('admin.layouts.admin_layout')

@section('title', 'Kelola Ahli Gizi - Halalytics Admin')

@section('breadcrumb-current', 'Ahli Gizi')

@section('content')
<div class="space-y-8">
    <!-- Header Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="metric-card metric-card--primary flex items-center justify-between">
            <div>
                <p class="text-white/60 text-xs font-bold uppercase tracking-wider">Total Ahli Gizi</p>
                <h3 class="text-3xl font-extrabold mt-1">{{ number_format($stats['total_nutritionists']) }}</h3>
            </div>
            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center">
                <span class="material-icons-round text-3xl">medical_services</span>
            </div>
        </div>
        <div class="metric-card metric-card--accent flex items-center justify-between">
            <div>
                <p class="text-white/60 text-xs font-bold uppercase tracking-wider">Ahli Gizi Aktif</p>
                <h3 class="text-3xl font-extrabold mt-1">{{ number_format($stats['active_nutritionists']) }}</h3>
            </div>
            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center">
                <span class="material-icons-round text-3xl">check_circle</span>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="surface-card rounded-3xl overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Daftar Ahli Gizi</h3>
                <p class="text-slate-500 text-xs mt-0.5">Kelola data dan status akun tenaga ahli profesional.</p>
            </div>
            
            <form action="{{ route('admin.nutritionists.index') }}" method="GET" class="flex items-center gap-3">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <span class="material-icons-round text-sm">search</span>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/email..." class="pl-9 pr-4 py-2 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-primary w-full md:w-64 transition-all">
                </div>
                <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary-dark transition-all">
                    Cari
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 text-[10px] font-bold uppercase tracking-widest border-b border-slate-100">
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Nama Lengkap</th>
                        <th class="px-6 py-4">Email / Username</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($nutritionists as $expert)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <span class="text-xs font-mono text-slate-400">#{{ $expert->id_user }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary font-bold text-sm">
                                    {{ strtoupper(substr($expert->full_name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-700">{{ $expert->full_name }}</p>
                                    <p class="text-[10px] text-slate-400 uppercase tracking-wider font-bold">Expert</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-slate-600">{{ $expert->email }}</p>
                            <p class="text-[11px] text-slate-400">@ {{ $expert->username }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-center">
                                <form action="{{ route('admin.nutritionists.toggle', $expert->id_user) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="badge-{{ $expert->active ? 'active' : 'blocked' }} px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider transition-all hover:opacity-80">
                                        {{ $expert->active ? 'Aktif' : 'Nonaktif' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.nutritionists.show', $expert->id_user) }}" class="p-2 text-slate-400 hover:text-primary transition-colors">
                                    <span class="material-icons-round text-lg">visibility</span>
                                </a>
                                <form action="{{ route('admin.nutritionists.destroy', $expert->id_user) }}" method="POST" onsubmit="return confirm('Hapus akun ahli gizi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-red-500 transition-colors">
                                        <span class="material-icons-round text-lg">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <span class="material-icons-round text-4xl text-slate-200">medical_services</span>
                                <p class="text-slate-400 text-sm mt-2">Belum ada ahli gizi yang terdaftar.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-6 border-t border-slate-100">
            {{ $nutritionists->links() }}
        </div>
    </div>
</div>
@endsection
