@extends('master')

@section('isi')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Pengaturan Website Promo</h4>
            <p class="mb-0">Kelola identitas dan konten utama website pendaratan (landing page)</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        @if(session('success'))
        <div class="alert alert-success solid alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span><i class="mdi mdi-close"></i></span></button>
            <strong>Sukses!</strong> {{ session('success') }}
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Konfigurasi Landing Page</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.promo.settings.update') }}" method="POST">
                    @csrf
                    
                    <h5 class="mb-3 text-primary border-bottom pb-2">Identitas Dasar</h5>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Nama Situs</label>
                            <input type="text" name="site_name" class="form-control" value="{{ $settings['site_name'] ?? 'HalalScan AI' }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Versi Aplikasi Saat Ini</label>
                            <input type="text" name="app_version" class="form-control" value="{{ $settings['app_version'] ?? '1.0.0' }}">
                        </div>
                        <div class="col-md-12 form-group">
                            <label>Deskripsi Singkat (SEO & Meta)</label>
                            <textarea name="site_description" class="form-control" rows="2">{{ $settings['site_description'] ?? 'AI-powered halal & health product intelligence platform.' }}</textarea>
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4 text-primary border-bottom pb-2">Konten Hero (Beranda Atas)</h5>
                    <div class="form-group">
                        <label>Headline Utama (H1)</label>
                        <input type="text" name="hero_headline" class="form-control" value="{{ $settings['hero_headline'] ?? 'Scan. Analyze. Stay Safe.' }}">
                    </div>
                    <div class="form-group">
                        <label>Sub-Headline</label>
                        <textarea name="hero_subheadline" class="form-control" rows="2">{{ $settings['hero_subheadline'] ?? 'AI-powered halal & health analyzer. Instantly detect ingredients, drug interactions, and health scores from any product barcode.' }}</textarea>
                    </div>

                    <h5 class="mb-3 mt-4 text-primary border-bottom pb-2">Tautan Eksternal</h5>
                    <div class="form-group">
                        <label>URL Google Play Store</label>
                        <input type="url" name="playstore_url" class="form-control" value="{{ $settings['playstore_url'] ?? '#' }}" placeholder="https://play.google.com/store/apps/details?id=...">
                    </div>
                    
                    <div class="form-group">
                        <label>Email Kontak Utama</label>
                        <input type="email" name="contact_email" class="form-control" value="{{ $settings['contact_email'] ?? 'support@halalscanapp.com' }}">
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="btn btn-primary btn-lg"><i class="fa fa-save"></i> Simpan Pengaturan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
