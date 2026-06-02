@extends('expert.layouts.expert_layout')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-xl font-black text-slate-800">Meal Plan</h3>
            <p class="text-sm text-slate-500 mt-1">Buat dan kelola rencana makan untuk pasien.</p>
        </div>
        <a href="#" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary-dark transition-all">
            <span class="material-icons-round text-sm">add</span>
            Buat Meal Plan Baru
        </a>
    </div>

    <div class="surface-card rounded-3xl p-12 text-center">
        <span class="material-icons-round text-5xl text-slate-200">restaurant_menu</span>
        <h4 class="text-lg font-bold text-slate-800 mt-4">Fitur Meal Plan</h4>
        <p class="text-sm text-slate-500 mt-2 max-w-md mx-auto">Fitur ini sedang dalam pengembangan. Segera hadir untuk membantu Anda membuat rencana makan yang sesuai dengan kebutuhan gizi pasien.</p>
        <div class="mt-6 flex justify-center gap-3">
            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-full">
                <span class="material-icons-round text-sm">check_circle</span> AI Recommendations
            </span>
            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-700 text-xs font-bold rounded-full">
                <span class="material-icons-round text-sm">check_circle</span> Calorie Tracking
            </span>
            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-50 text-amber-700 text-xs font-bold rounded-full">
                <span class="material-icons-round text-sm">pending</span> Meal Scheduling
            </span>
        </div>
    </div>
</div>
@endsection
