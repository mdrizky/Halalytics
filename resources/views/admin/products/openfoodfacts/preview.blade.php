@extends('admin.master')

@section('header')
<div class="header-body">
    <div class="row align-items-end">
        <div class="col">
            <h6 class="header-pretitle">OpenFoodFacts</h6>
            <h1 class="header-title">Preview & Import Produk</h1>
        </div>
        <div class="col-auto">
            <a href="javascript:history.back()" class="btn btn-white px-4 rounded-pill">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Left Column: Product Info -->
        <div class="col-lg-7">
            <!-- Basic Info Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="rounded-3 overflow-hidden bg-light p-2 border" style="height: 200px;">
                                <img src="{{ $offProduct['image'] ?? 'https://via.placeholder.com/400?text=No+Image' }}" 
                                     class="w-100 h-100" 
                                     style="object-fit: contain;"
                                     alt="{{ $offProduct['nama_product'] }}">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h2 class="mb-2 fw-bold text-dark">{{ $offProduct['nama_product'] }}</h2>
                            <p class="text-muted mb-3"><i class="fas fa-tag me-2"></i> {{ $offProduct['brand'] ?: 'Brand tidak tersedia' }}</p>
                            
                            <div class="row g-2 mb-4">
                                <div class="col-auto">
                                    <span class="badge bg-light text-primary border px-3 py-2">
                                        <i class="fas fa-barcode me-1"></i> {{ $offProduct['barcode'] }}
                                    </span>
                                </div>
                                <div class="col-auto">
                                    <span class="badge bg-light text-dark border px-3 py-2">
                                        <i class="fas fa-info-circle me-1"></i> {{ $offProduct['off_product_id'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="alert alert-info border-0 p-3 mb-0" style="background-color: rgba(44, 123, 229, 0.05);">
                                <div class="d-flex">
                                    <i class="fas fa-database mt-1 me-3"></i>
                                    <div>
                                        <small class="d-block fw-bold mb-1">Sumber Data Global</small>
                                        <p class="mb-0 small">Data ini ditarik langsung dari OpenFoodFacts. Beberapa informasi mungkin memerlukan validasi lebih lanjut.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ingredients Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h4 class="card-header-title"><i class="fas fa-mortar-pestle me-2 text-primary"></i> Komposisi Bahan</h4>
                </div>
                <div class="card-body p-4">
                    @if($offProduct['ingredients_text'])
                        <div class="p-3 bg-light rounded-3 text-dark small" style="line-height: 1.6;">
                            {{ $offProduct['ingredients_text'] }}
                        </div>
                    @elseif(!empty($offProduct['ingredients_list']))
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($offProduct['ingredients_list'] as $ingredient)
                                <span class="badge bg-light text-dark border fw-normal">{{ $ingredient }}</span>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-times-circle fa-2x mb-2 d-block"></i>
                            <p class="mb-0">Data komposisi tidak tersedia.</p>
                        </div>
                    @endif

                    @if(!empty($offProduct['additives']))
                        <hr class="my-4">
                        <h5 class="mb-3">Bahan Tambahan (E-Numbers)</h5>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($offProduct['additives'] as $additive)
                                <span class="badge bg-warning text-dark border px-3 py-2 fw-bold">
                                    <i class="fas fa-flask me-1"></i> {{ strtoupper(str_replace('en:', '', $additive)) }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Nutrition Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h4 class="card-header-title"><i class="fas fa-chart-pie me-2 text-primary"></i> Informasi Gizi (per 100g)</h4>
                </div>
                <div class="card-body p-0">
                    <table class="table table-nowrap table-edge table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4">Nutrisi</th>
                                <th class="text-end px-4">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-4">Energi (Kcal)</td>
                                <td class="text-end px-4 fw-bold text-dark">{{ $offProduct['nutriments']['energy_kcal'] ?? 'N/A' }} kcal</td>
                            </tr>
                            <tr>
                                <td class="px-4">Protein</td>
                                <td class="text-end px-4 fw-bold text-dark">{{ $offProduct['nutriments']['proteins'] ?? 'N/A' }} g</td>
                            </tr>
                            <tr>
                                <td class="px-4">Karbohidrat</td>
                                <td class="text-end px-4 fw-bold text-dark">{{ $offProduct['nutriments']['carbohydrates'] ?? 'N/A' }} g</td>
                            </tr>
                            <tr>
                                <td class="px-4">Lemak</td>
                                <td class="text-end px-4 fw-bold text-dark">{{ $offProduct['nutriments']['fat'] ?? 'N/A' }} g</td>
                            </tr>
                            <tr>
                                <td class="px-4">Gula</td>
                                <td class="text-end px-4 fw-bold text-dark">{{ $offProduct['nutriments']['sugars'] ?? 'N/A' }} g</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Import & Verification -->
        <div class="col-lg-5">
            <!-- Halal Issues Warning -->
            @if(!empty($halalIssues))
            <div class="card border-0 shadow-sm mb-4" style="border-left: 5px solid #e63757 !important;">
                <div class="card-body p-4">
                    <h4 class="text-danger mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i> Peringatan Deteksi Bahan
                    </h4>
                    <p class="small text-muted mb-3">Sistem mendeteksi bahan-bahan yang berpotensi bermasalah berdasarkan database haram/syubhat:</p>
                    <ul class="list-group list-group-flush">
                        @foreach($halalIssues as $issue)
                            <li class="list-group-item bg-transparent px-0 py-2 text-danger fw-bold small">
                                <i class="fas fa-times-circle me-2"></i> {{ $issue }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <!-- Existing Product Notice -->
            @if($existingProduct)
            <div class="card border-0 shadow-sm mb-4" style="border-left: 5px solid #2c7be5 !important;">
                <div class="card-body p-4 text-center">
                    <div class="icon-circle bg-primary-soft text-primary mb-3 mx-auto" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background-color: rgba(44, 123, 229, 0.1);">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                    <h4>Produk Sudah Ada</h4>
                    <p class="text-muted small mb-4">Produk ini sudah terdaftar di database lokal. Anda tidak dapat mengimportnya kembali.</p>
                    <a href="{{ route('admin.product.edit', $existingProduct->id_product) }}" class="btn btn-primary w-100 rounded-pill">
                        Edit Produk Lokal
                    </a>
                </div>
            </div>
            @else
            <!-- Import Action Card -->
            <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                <div class="card-header bg-primary text-white py-4 px-4">
                    <h3 class="mb-0">Import ke Katalog Lokal</h3>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.products.off.import', $offProduct['off_product_id']) }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Status Halal Saat Ini:</label>
                            <div class="d-flex flex-column gap-2">
                                <div class="form-check custom-option border rounded p-3">
                                    <input class="form-check-input" type="radio" name="halal_status" id="status_syubhat" value="syubhat" checked>
                                    <label class="form-check-label ps-2" for="status_syubhat">
                                        <span class="d-block fw-bold text-warning">SYUBHAT / PERLU REVIEW</span>
                                        <small class="text-muted">Pilih jika Anda belum yakin 100% dan butuh audit lebih lanjut.</small>
                                    </label>
                                </div>
                                <div class="form-check custom-option border rounded p-3">
                                    <input class="form-check-input" type="radio" name="halal_status" id="status_halal" value="halal">
                                    <label class="form-check-label ps-2" for="status_halal">
                                        <span class="d-block fw-bold text-success">HALAL</span>
                                        <small class="text-muted">Gunakan jika komposisi sudah diperiksa dan memiliki sertifikat/label halal yang valid.</small>
                                    </label>
                                </div>
                                <div class="form-check custom-option border rounded p-3">
                                    <input class="form-check-input" type="radio" name="halal_status" id="status_haram" value="haram">
                                    <label class="form-check-label ps-2" for="status_haram">
                                        <span class="d-block fw-bold text-danger">HARAM</span>
                                        <small class="text-muted">Gunakan jika produk mengandung bahan yang jelas dilarang.</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="bg-light p-3 rounded-3 mb-4">
                            <div class="d-flex small text-muted">
                                <i class="fas fa-info-circle mt-1 me-2"></i>
                                <span>Setelah diimport, status verifikasi akan otomatis menjadi <strong>VERIFIED</strong> karena diinput oleh Admin.</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm py-3 fw-bold">
                            <i class="fas fa-download me-2"></i> Import & Verifikasi Produk
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <!-- Categories Guidance -->
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Panduan Kategori Global</h5>
                    <p class="small text-muted mb-0">Kategori dari OpenFoodFacts akan dipetakan ke kategori sistem lokal jika memungkinkan. Anda dapat mengubahnya setelah proses import selesai di halaman edit produk.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-option:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
    .form-check-input:checked + .form-check-label {
        color: inherit !important;
    }
</style>
@endsection
