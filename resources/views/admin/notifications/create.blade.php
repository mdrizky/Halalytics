@extends('admin.master')

@section('title', 'Compose Broadcast - Halalytics Admin')

@section('breadcrumb')
<span class="text-slate-400">Communication</span>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<a href="{{ route('admin.notifications.index') }}" class="text-slate-400 hover:text-primary transition-colors">Broadcast Center</a>
<span class="material-icons-round text-slate-300 text-sm">chevron_right</span>
<span class="font-semibold text-slate-700 dark:text-slate-200">Compose</span>
@endsection

@section('content')
<div class="max-w-4xl">
    <div class="mb-8">
        <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Compose Broadcast</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Send real-time alerts or promotional messages to mobile users via Firebase FCM.</p>
    </div>
    
    <form action="{{ route('admin.notifications.store') }}" method="POST" class="space-y-8">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-1 gap-8">
            <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-sm p-8 relative overflow-hidden">
                <!-- Visual Accent -->
                <div class="absolute top-0 right-0 h-32 w-32 bg-primary/5 blur-3xl rounded-full"></div>

                <div class="flex items-center gap-4 mb-8">
                    <div class="h-12 w-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                        <span class="material-icons-round text-2xl">edit_note</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">Broadcast Content</h3>
                        <p class="text-xs text-slate-500">Draft your message and select targeting options.</p>
                    </div>
                </div>
                
                <div class="space-y-6 relative z-10">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Message Title</label>
                        <input type="text" name="title" required class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="e.g. New Safety Alert: Harmful Ingredients Detected">
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Message Body</label>
                        <textarea name="body" required rows="5" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all" placeholder="Provide detailed information about this notification..."></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Campaign Type</label>
                            <div class="relative">
                                <select name="type" required class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-2 focus:ring-primary appearance-none">
                                    <option value="general">Standard (News/Update)</option>
                                    <option value="ingredient_alert">Critical Alert (Ingredients)</option>
                                    <option value="product_reminder">Engagement (Reminder)</option>
                                    <option value="product">Promotional (New Product)</option>
                                    <option value="poster">Media (Poster/Promo)</option>
                                    <option value="news">Editorial (Article)</option>
                                </select>
                                <span class="material-icons-round absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">User Selection</label>
                            <div class="relative">
                                <select id="targetType" name="target_type" required class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-2 focus:ring-primary appearance-none">
                                    <option value="all">Universal (All Registered Users)</option>
                                    <option value="specific_users">Segmented (Specific User IDs)</option>
                                </select>
                                <span class="material-icons-round absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">people</span>
                            </div>
                        </div>
                    </div>

                    <div id="specificUsersField" class="space-y-2 hidden p-6 rounded-3xl bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/30">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-amber-700 dark:text-amber-400 mb-2">Target User IDs</label>
                        <input
                            type="text"
                            name="user_ids"
                            value="{{ old('user_ids') }}"
                            class="w-full px-5 py-4 bg-white dark:bg-slate-900 border border-amber-200 dark:border-amber-800/50 rounded-2xl text-sm focus:ring-2 focus:ring-amber-500 transition-all"
                            placeholder="e.g. 102, 105, 209"
                        >
                        <p class="text-[9px] font-bold text-amber-600/70 uppercase tracking-tighter mt-2">Comma-separated list of numeric IDs.</p>
                        @error('user_ids')
                            <p class="text-xs text-rose-500 mt-2 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Scheduling (Optional)</label>
                        <div class="relative">
                            <input type="datetime-local" name="scheduled_at" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl text-sm focus:ring-2 focus:ring-primary transition-all">
                            <span class="material-icons-round absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">calendar_today</span>
                        </div>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter mt-2 ml-1">Leave blank to dispatch immediately.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4 pt-4">
            <a href="{{ route('admin.notifications.index') }}" class="px-8 py-3.5 text-sm font-bold text-slate-500 hover:text-slate-900 dark:hover:text-white transition-all">DISCARD</a>
            <button type="submit" class="px-10 py-3.5 bg-primary text-white rounded-2xl text-sm font-bold hover:bg-primary-dark transition-all shadow-xl shadow-primary/20 flex items-center gap-3">
                <span class="material-icons-round text-lg">rocket_launch</span>
                DISPATCH BROADCAST
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const targetType = document.getElementById('targetType');
    const specificUsersField = document.getElementById('specificUsersField');

    function toggleSpecificUsersField() {
        if (!targetType || !specificUsersField) return;
        const isSpecific = targetType.value === 'specific_users';
        if (isSpecific) {
            specificUsersField.classList.remove('hidden');
            specificUsersField.classList.add('animate-in', 'fade-in', 'slide-in-from-top-2');
        } else {
            specificUsersField.classList.add('hidden');
        }
    }

    targetType?.addEventListener('change', toggleSpecificUsersField);
    toggleSpecificUsersField();
</script>
@endpush

