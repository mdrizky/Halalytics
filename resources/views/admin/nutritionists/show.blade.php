@extends('admin.layouts.admin_layout')

@section('title', 'Detail Ahli Gizi - Halalytics Admin')

@section('breadcrumb-current', 'Detail Expert')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.nutritionists.index') }}" class="flex items-center text-slate-500 hover:text-primary transition-all gap-2 group text-sm font-bold">
            <span class="material-icons-round text-lg group-hover:-translate-x-1 transition-transform">arrow_back</span>
            Kembali ke Daftar
        </a>
        
        <div class="flex gap-3">
            <form action="{{ route('admin.nutritionists.toggle', $nutritionist->id_user) }}" method="POST">
                @csrf
                <button type="submit" class="px-6 py-2.5 rounded-2xl {{ $nutritionist->active ? 'bg-red-50 text-red-600 border-red-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100' }} border font-bold text-sm transition-all hover:shadow-sm">
                    {{ $nutritionist->active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Sidebar Profile -->
        <div class="md:col-span-1 space-y-6">
            <div class="surface-card rounded-[2.5rem] p-8 text-center">
                <div class="w-24 h-24 rounded-3xl bg-primary flex items-center justify-center text-white text-3xl font-bold mx-auto shadow-lg shadow-primary/20">
                    {{ strtoupper(substr($nutritionist->full_name, 0, 1)) }}
                </div>
                <h2 class="mt-6 text-xl font-bold text-slate-800">{{ $nutritionist->full_name }}</h2>
                <p class="text-slate-500 text-sm">@ {{ $nutritionist->username }}</p>
                
                <div class="mt-6 pt-6 border-t border-slate-100 flex justify-center">
                    <span class="badge-{{ $nutritionist->active ? 'active' : 'blocked' }} px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest">
                        {{ $nutritionist->active ? 'Verified Expert' : 'Account Suspended' }}
                    </span>
                </div>
            </div>

            <div class="surface-card rounded-[2.5rem] p-6 space-y-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Quick Actions</h3>
                <button class="w-full text-left p-4 rounded-2xl bg-slate-50 hover:bg-primary/5 hover:text-primary transition-all flex items-center gap-3">
                    <span class="material-icons-round text-lg">chat_bubble</span>
                    <span class="text-sm font-bold">Riwayat Konsultasi</span>
                </button>
                <button class="w-full text-left p-4 rounded-2xl bg-slate-50 hover:bg-primary/5 hover:text-primary transition-all flex items-center gap-3">
                    <span class="material-icons-round text-lg">star</span>
                    <span class="text-sm font-bold">Review Pasien</span>
                </button>
            </div>
        </div>

        <!-- Main Info -->
        <div class="md:col-span-2 space-y-8">
            <div class="surface-card rounded-[2.5rem] p-8">
                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <span class="material-icons-round text-primary">person_outline</span>
                    Informasi Akun
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Email Address</p>
                        <p class="text-sm font-semibold text-slate-700">{{ $nutritionist->email }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Phone Number</p>
                        <p class="text-sm font-semibold text-slate-700">{{ $nutritionist->phone ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Bergabung Sejak</p>
                        <p class="text-sm font-semibold text-slate-700">{{ $nutritionist->created_at->format('d M Y') }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Last Login</p>
                        <p class="text-sm font-semibold text-slate-700">Recent</p>
                    </div>
                </div>
            </div>

            <div class="surface-card rounded-[2.5rem] p-8">
                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <span class="material-icons-round text-primary">analytics</span>
                    Statistik Performa
                </h3>
                
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                    <div class="p-4 bg-slate-50 rounded-2xl">
                        <p class="text-xl font-black text-primary">0</p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">Pasien</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-2xl">
                        <p class="text-xl font-black text-primary">0</p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">Sesi</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-2xl">
                        <p class="text-xl font-black text-primary">5.0</p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">Rating</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-2xl">
                        <p class="text-xl font-black text-primary">100%</p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">Respon</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
