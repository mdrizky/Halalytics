@extends('admin.layouts.admin_layout')

@section('title', 'Edit Kampanye Donasi - Halalytics Admin')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center space-x-4 mb-8">
        <a href="{{ route('admin.donation-campaigns.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 hover:text-slate-800 hover:bg-slate-200 transition-all">
            <span class="material-icons-round">arrow_back</span>
        </a>
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Edit Kampanye</h2>
            <p class="text-slate-500 text-sm mt-0.5">{{ $donationCampaign->title }}</p>
        </div>
    </div>

    <form action="{{ route('admin.donation-campaigns.update', $donationCampaign) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-6 space-y-6">
                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Judul Kampanye <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title', $donationCampaign->title) }}" required
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-sm">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Deskripsi <span class="text-red-500">*</span></label>
                    <textarea name="description" id="description" rows="5" required
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-sm">{{ old('description', $donationCampaign->description) }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Target Amount + Category --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="target_amount" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Target Dana (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="target_amount" id="target_amount" value="{{ old('target_amount', $donationCampaign->target_amount) }}" required min="1000"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-sm">
                        @error('target_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="category" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kategori <span class="text-red-500">*</span></label>
                        <select name="category" id="category" required
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-sm">
                            @foreach(['kemanusiaan', 'pendidikan', 'kesehatan', 'bencana', 'masjid', 'zakat', 'yatim', 'lainnya'] as $cat)
                            <option value="{{ $cat }}" {{ old('category', $donationCampaign->category) == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                            @endforeach
                        </select>
                        @error('category') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Deadline + Image --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="deadline" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Deadline</label>
                        <input type="date" name="deadline" id="deadline" value="{{ old('deadline', $donationCampaign->deadline?->format('Y-m-d')) }}"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-sm">
                        @error('deadline') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="image" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Gambar Kampanye</label>
                        @if($donationCampaign->image)
                        <div class="mb-2">
                            <img src="{{ $donationCampaign->image }}" class="w-20 h-20 rounded-lg object-cover border border-slate-200 dark:border-slate-700">
                        </div>
                        @endif
                        <input type="file" name="image" id="image" accept="image/*"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-sm file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-primary/10 file:text-primary">
                        @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Toggles --}}
                <div class="flex items-center space-x-8">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $donationCampaign->is_active) ? 'checked' : '' }}
                               class="w-5 h-5 rounded border-slate-300 text-primary focus:ring-primary/30">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Aktifkan Kampanye</span>
                    </label>
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" name="is_urgent" value="1" {{ old('is_urgent', $donationCampaign->is_urgent) ? 'checked' : '' }}
                               class="w-5 h-5 rounded border-slate-300 text-red-500 focus:ring-red-300">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Tandai Urgent</span>
                    </label>
                </div>
            </div>

            {{-- Actions --}}
            <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/30 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.donation-campaigns.index') }}" class="px-5 py-2.5 rounded-lg text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">Batal</a>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary hover:bg-primary-dark text-white font-bold text-sm transition-all shadow-sm shadow-primary/20 flex items-center space-x-2">
                    <span class="material-icons-round text-lg">save</span>
                    <span>Update Kampanye</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
