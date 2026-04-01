@php
    $segment = old('target_mode')
        ? [
            'mode' => old('target_mode'),
            'user_ids' => preg_split('/[\s,]+/', old('user_ids', ''), -1, PREG_SPLIT_NO_EMPTY),
            'active_only' => old('active_only'),
            'data_type' => old('data_type', 'general'),
        ]
        : ($campaign->target_segment ?? []);

    $selectedMode = $segment['mode'] ?? 'all';
    $userIdsValue = old('user_ids', implode(', ', $segment['user_ids'] ?? []));
    $dataTypeValue = old('data_type', $segment['data_type'] ?? 'general');
@endphp

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2 space-y-6">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
            <h3 class="text-lg font-extrabold text-slate-800 dark:text-white">Konten Notifikasi</h3>
            <p class="text-sm text-slate-500 mt-1">Judul dan isi ini akan tampil di perangkat mobile user.</p>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-200 mb-2">Nama Campaign</label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $campaign->name ?? '') }}"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                        placeholder="Contoh: Promo Ramadan Pekan 1"
                        required
                    >
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-200 mb-2">Judul</label>
                    <input
                        type="text"
                        name="title"
                        value="{{ old('title', $campaign->title ?? '') }}"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                        placeholder="Judul notifikasi"
                        required
                    >
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-200 mb-2">Tipe Data</label>
                    <select
                        name="data_type"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                    >
                        @foreach(['general', 'promo', 'news', 'reminder', 'product'] as $type)
                            <option value="{{ $type }}" @selected($dataTypeValue === $type)>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-200 mb-2">Body</label>
                    <textarea
                        name="body"
                        rows="5"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                        placeholder="Isi notifikasi"
                        required
                    >{{ old('body', $campaign->body ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-200 mb-2">Image URL</label>
                    <input
                        type="url"
                        name="image_url"
                        value="{{ old('image_url', $campaign->image_url ?? '') }}"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                        placeholder="https://..."
                    >
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-200 mb-2">Action URL / Deep Link</label>
                    <input
                        type="text"
                        name="action_url"
                        value="{{ old('action_url', $campaign->action_url ?? '') }}"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                        placeholder="halalytics://promo/ramadan"
                    >
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
            <h3 class="text-lg font-extrabold text-slate-800 dark:text-white">Target Audiens</h3>
            <p class="text-sm text-slate-500 mt-1">Pilih semua user atau hanya user tertentu.</p>

            <div class="mt-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="flex items-start gap-3 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 cursor-pointer">
                        <input type="radio" name="target_mode" value="all" class="mt-1" @checked($selectedMode === 'all')>
                        <span>
                            <span class="block text-sm font-bold text-slate-800 dark:text-white">Semua User</span>
                            <span class="block text-xs text-slate-500 mt-1">Broadcast ke semua token yang aktif.</span>
                        </span>
                    </label>
                    <label class="flex items-start gap-3 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 cursor-pointer">
                        <input type="radio" name="target_mode" value="specific_users" class="mt-1" @checked($selectedMode === 'specific_users')>
                        <span>
                            <span class="block text-sm font-bold text-slate-800 dark:text-white">User Tertentu</span>
                            <span class="block text-xs text-slate-500 mt-1">Masukkan daftar `id_user`, pisahkan dengan koma atau baris baru.</span>
                        </span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-200 mb-2">Daftar User ID</label>
                    <textarea
                        name="user_ids"
                        rows="4"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                        placeholder="Contoh: 12, 15, 22"
                    >{{ $userIdsValue }}</textarea>
                </div>

                <label class="flex items-center gap-3">
                    <input type="checkbox" name="active_only" value="1" class="rounded border-slate-300" @checked(old('active_only', !empty($segment['active_only'])))>
                    <span class="text-sm text-slate-600 dark:text-slate-300">Hanya user yang punya aktivitas scan dalam 7 hari terakhir</span>
                </label>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6">
            <h3 class="text-lg font-extrabold text-slate-800 dark:text-white">Jadwal & Aksi</h3>

            <div class="mt-5 space-y-5">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-200 mb-2">Jadwalkan Kirim</label>
                    <input
                        type="datetime-local"
                        name="scheduled_at"
                        value="{{ old('scheduled_at', optional($campaign->scheduled_at ?? null)->format('Y-m-d\\TH:i')) }}"
                        class="w-full rounded-xl border-slate-200 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                    >
                </div>

                <label class="flex items-center gap-3">
                    <input type="checkbox" name="send_now" value="1" class="rounded border-slate-300" @checked(old('send_now'))>
                    <span class="text-sm text-slate-600 dark:text-slate-300">Kirim sekarang setelah disimpan</span>
                </label>
            </div>

            <div class="mt-6 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 p-4">
                <p class="text-xs uppercase tracking-wider text-slate-400 font-bold">Preview Payload</p>
                <div class="mt-3 space-y-2 text-sm">
                    <div>
                        <p class="text-xs text-slate-400">Type</p>
                        <p class="font-semibold text-slate-800 dark:text-white">{{ $dataTypeValue }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Action URL</p>
                        <p class="font-semibold text-slate-800 dark:text-white break-all">{{ old('action_url', $campaign->action_url ?? '—') }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-900 rounded-2xl p-5">
                <p class="text-sm font-bold text-red-600">Periksa input berikut:</p>
                <ul class="mt-3 text-sm text-red-500 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
