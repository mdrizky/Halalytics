@extends('admin.layouts.admin_layout')

@section('title', 'Buat Kampanye Donasi - Halalytics Admin')

@section('content')
<div class="max-w-4xl mx-auto" x-data="campaignTemplate()">
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.donation-campaigns.index') }}" class="w-10 h-10 rounded-xl bg-white dark:bg-slate-800 flex items-center justify-center text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-all shadow-sm">
                <span class="material-icons-round">arrow_back</span>
            </a>
            <div>
                <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Buat Kampanye Donasi</h2>
                <p class="text-slate-500 text-sm mt-0.5">Lengkapi formulir di bawah untuk memulai penggalangan dana.</p>
            </div>
        </div>
    </div>

    {{-- Template Quick Select --}}
    <div class="mb-8">
        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-4">Pilih Template Cepat</label>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <template x-for="(tpl, key) in templates" :key="key">
                <button type="button" @click="applyTemplate(key)"
                        class="p-4 rounded-2xl border-2 transition-all text-left group hover:shadow-md"
                        :class="selectedTemplate === key ? 'border-primary bg-primary/5' : 'border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900'">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3 transition-all"
                         :class="selectedTemplate === key ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 group-hover:bg-primary group-hover:text-white'">
                        <span class="material-icons-round" x-text="tpl.icon"></span>
                    </div>
                    <p class="font-bold text-sm" :class="selectedTemplate === key ? 'text-primary' : 'text-slate-700 dark:text-slate-300'" x-text="tpl.name"></p>
                    <p class="text-[10px] text-slate-400 mt-1" x-text="tpl.category_label"></p>
                </button>
            </template>
        </div>
    </div>

    <form action="{{ route('admin.donation-campaigns.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column: Main Info --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="p-6 space-y-6">
                        {{-- Title --}}
                        <div>
                            <label for="title" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Judul Kampanye <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title" x-model="formData.title" required
                                   class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-sm font-medium"
                                   placeholder="Contoh: Bantu Saudara Palestina">
                            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Deskripsi Lengkap <span class="text-red-500">*</span></label>
                            <textarea name="description" id="description" rows="10" x-model="formData.description" required
                                      class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-sm"
                                      placeholder="Deskripsikan tujuan kampanye donasi secara mendalam..."></textarea>
                            @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Settings --}}
            <div class="space-y-6">
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-6">
                    {{-- Target Amount --}}
                    <div>
                        <label for="target_amount" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Target Dana (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-slate-400 font-bold text-sm">Rp</span>
                            <input type="number" name="target_amount" id="target_amount" x-model="formData.target_amount" required min="1000"
                                   class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-sm font-bold">
                        </div>
                        @error('target_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Category --}}
                    <div>
                        <label for="category" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kategori</label>
                            <select name="category" id="category" x-model="formData.category" required
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-sm">
                                <option value="kemanusiaan">Kemanusiaan</option>
                                <option value="kesehatan">Kesehatan</option>
                                <option value="pendidikan">Pendidikan</option>
                                <option value="bencana">Bencana Alam</option>
                                <option value="masjid">Masjid & Musholla</option>
                                <option value="zakat">Zakat & Infaq</option>
                                <option value="yatim">Yatim & Dhuafa</option>
                                <option value="donor_darah">Donor Darah</option>
                                <option value="pangan">Pangan</option>
                                <option value="stunting">Stunting</option>
                                <option value="platform">Platform</option>
                                <option value="darurat">Darurat</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                    </div>

                    {{-- Deadline --}}
                    <div>
                        <label for="deadline" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Batas Waktu (Opsional)</label>
                        <input type="date" name="deadline" id="deadline"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all text-sm">
                    </div>

                    {{-- Image Upload --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Cover Image</label>
                        <div class="relative group cursor-pointer">
                            <div class="w-full aspect-video rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-700 flex flex-col items-center justify-center bg-slate-50 dark:bg-slate-800/50 group-hover:bg-slate-100 transition-all overflow-hidden">
                                <template x-if="!imagePreview">
                                    <div class="text-center p-4">
                                        <span class="material-icons-round text-3xl text-slate-400 mb-2">add_photo_alternate</span>
                                        <p class="text-[10px] text-slate-500 font-medium">Klik untuk upload atau drag-drop</p>
                                    </div>
                                </template>
                                <template x-if="imagePreview">
                                    <img :src="imagePreview" class="w-full h-full object-cover">
                                </template>
                            </div>
                            <input type="file" name="image" id="image" accept="image/*" @change="previewImage"
                                   class="absolute inset-0 opacity-0 cursor-pointer">
                        </div>
                        @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Toggles --}}
                    <div class="pt-4 space-y-4 border-t border-slate-100 dark:border-slate-800">
                        <label class="flex items-center justify-between cursor-pointer group">
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Tampilkan ke Publik</span>
                            <div class="relative inline-flex items-center">
                                <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            </div>
                        </label>
                        <label class="flex items-center justify-between cursor-pointer group">
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Kategori Urgent</span>
                            <div class="relative inline-flex items-center">
                                <input type="checkbox" name="is_urgent" value="1" x-model="formData.is_urgent" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-500"></div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Action --}}
                <button type="submit" class="w-full py-4 rounded-2xl bg-primary hover:bg-primary-dark text-white font-black text-sm transition-all shadow-lg shadow-primary/25 flex items-center justify-center space-x-2 tracking-widest uppercase">
                    <span class="material-icons-round text-xl">rocket_launch</span>
                    <span>Publikasikan Kampanye</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function campaignTemplate() {
        return {
            selectedTemplate: null,
            imagePreview: null,
            formData: {
                title: '',
                description: '',
                target_amount: 10000000,
                category: 'kemanusiaan',
                is_urgent: false
            },
            templates: {
                palestina: {
                    name: 'Bantu Palestina',
                    icon: 'public',
                    category: 'kemanusiaan',
                    category_label: 'Kemanusiaan',
                    title: 'Bantuan Darurat Kemanusiaan untuk Palestina',
                    is_urgent: true,
                    description: 'Mari bersama ulurkan tangan untuk saudara-saudara kita di Palestina. Bantuan Anda akan disalurkan dalam bentuk pangan, obat-obatan, dan kebutuhan darurat lainnya.'
                },
                masjid: {
                    name: 'Bangun Masjid',
                    icon: 'mosque',
                    category: 'masjid',
                    category_label: 'Religius',
                    title: 'Wakaf Pembangunan Masjid Halalytics',
                    is_urgent: false,
                    description: 'Investasi akhirat dengan membantu pembangunan rumah Allah. Setiap sujud jamaah akan menjadi pahala jariyah bagi Anda.'
                },
                bencana: {
                    name: 'Siaga Bencana',
                    icon: 'warning',
                    category: 'bencana',
                    category_label: 'Darurat',
                    title: 'Bantuan Cepat Tanggap Bencana Alam',
                    is_urgent: true,
                    description: 'Bencana melanda tanpa diduga. Tim Halalytics Siaga siap menyalurkan bantuan Anda langsung ke lokasi terdampak untuk membantu pemulihan warga.'
                },
                yatim: {
                    name: 'Santunan Yatim',
                    icon: 'child_care',
                    category: 'yatim',
                    category_label: 'Sosial',
                    title: 'Beasiswa dan Santunan Anak Yatim',
                    is_urgent: false,
                    description: 'Berikan masa depan yang lebih cerah bagi mereka yang kehilangan orang tua. Donasi Anda digunakan untuk pendidikan dan kebutuhan hidup sehari-hari.'
                }
            },
            applyTemplate(key) {
                const tpl = this.templates[key];
                this.selectedTemplate = key;
                this.formData.title = tpl.title;
                this.formData.description = tpl.description;
                this.formData.category = tpl.category;
                this.formData.is_urgent = tpl.is_urgent;
            },
            previewImage(e) {
                const file = e.target.files[0];
                if (file) {
                    this.imagePreview = URL.createObjectURL(file);
                }
            }
        }
    }
</script>
@endsection
