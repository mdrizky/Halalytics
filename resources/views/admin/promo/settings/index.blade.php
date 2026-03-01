@extends('admin.layouts.admin_layout')

@section('title', 'Promo Settings - Halalytics Admin')
@section('breadcrumb-parent', 'Content')
@section('breadcrumb-current', 'Promo Settings')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Pengaturan Website Promo</h2>
    <p class="text-slate-500 text-sm mt-1">Kelola identitas dan konten landing page Halalytics.</p>
</div>

@if(session('success'))
<div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">{{ session('success') }}</div>
@endif

<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
    <form action="{{ route('admin.promo.settings.update') }}" method="POST" class="space-y-5">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Nama Situs</label>
                <input type="text" name="site_name" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800" value="{{ $settings['site_name'] ?? 'HalalScan AI' }}">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Versi Aplikasi</label>
                <input type="text" name="app_version" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800" value="{{ $settings['app_version'] ?? '1.0.0' }}">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Deskripsi Singkat</label>
            <textarea name="site_description" rows="2" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800">{{ $settings['site_description'] ?? 'AI-powered halal & health product intelligence platform.' }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Headline Hero</label>
            <input type="text" name="hero_headline" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800" value="{{ $settings['hero_headline'] ?? 'Scan. Analyze. Stay Safe.' }}">
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Subheadline Hero</label>
            <textarea name="hero_subheadline" rows="2" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800">{{ $settings['hero_subheadline'] ?? 'AI-powered halal & health analyzer. Instantly detect ingredients, drug interactions, and health scores from any product barcode.' }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">URL Play Store</label>
                <input type="url" name="playstore_url" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800" value="{{ $settings['playstore_url'] ?? '#' }}">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Email Kontak</label>
                <input type="email" name="contact_email" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800" value="{{ $settings['contact_email'] ?? 'support@halalscanapp.com' }}">
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-white font-semibold hover:bg-primary-dark transition">Simpan Pengaturan</button>
        </div>
    </form>
</div>
@endsection
