@extends('master')
@section('isi')
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Halalytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2E8B57;
            --primary-light: #3A9D66;
            --primary-dark: #1A5632;
            --bg-dark: #121212;
            --bg-card: #1E1E1E;
            --bg-hover: #2A2A2A;
            --text-light: #E0E0E0;
            --text-muted: #A0A0A0;
            --border-color: #333333;
            --card-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            --glow-effect: 0 0 10px rgba(46, 139, 87, 0.3);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-light);
            min-height: 100vh;
        }
        
        .page-container {
            padding: 20px;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            background-color: var(--bg-card);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow), var(--glow-effect);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 1.5rem 2rem;
            border: none;
            border-radius: 15px 15px 0 0 !important;
            position: relative;
            overflow: hidden;
        }
        
        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .card-header:hover::before {
            left: 100%;
        }
        
        .card-header h5 {
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
            font-size: 1.5rem;
        }
        
        .product-badge {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-weight: 600;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(5px);
            font-size: 0.9rem;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--text-light);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-label i {
            color: var(--primary-color);
            font-size: 1.1rem;
        }
        
        .form-control, .form-select {
            background-color: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border-color);
            color: var(--text-light);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            background-color: rgba(0, 0, 0, 0.4);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(46, 139, 87, 0.2);
            color: var(--text-light);
        }
        
        .form-control::placeholder {
            color: var(--text-muted);
        }
        
        .form-select option {
            background-color: var(--bg-card);
            color: var(--text-light);
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }
        
        .btn {
            border-radius: 10px;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            box-shadow: 0 4px 15px rgba(46, 139, 87, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46, 139, 87, 0.4);
            background: linear-gradient(135deg, var(--primary-light), var(--primary-color));
        }
        
        .btn-secondary {
            background-color: var(--bg-hover);
            color: var(--text-light);
            border: 1px solid var(--border-color);
        }
        
        .btn-secondary:hover {
            background-color: var(--border-color);
            color: white;
            transform: translateY(-2px);
        }
        
        .form-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            border: 1px solid var(--border-color);
        }
        
        .form-section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-light);
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--primary-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-section-title i {
            font-size: 1.3rem;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .alert-danger {
            background-color: rgba(230, 57, 70, 0.15);
            color: #e63946;
            border-left: 4px solid #e63946;
        }
        
        .invalid-feedback {
            color: #e63946;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .btn-group-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
        }
        
        .header-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        @media (max-width: 768px) {
            .page-container {
                padding: 15px;
            }
            
            .card-body {
                padding: 1.5rem;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .btn-group-actions {
                flex-direction: column;
            }
            
            .btn-group-actions .btn {
                width: 100%;
            }
            
            .header-info {
                flex-direction: column;
                align-items: flex-start;
            }
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .card {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="card">
            <div class="card-header">
                <div class="header-info">
                    <h5><i class="fas fa-edit"></i> Edit Produk</h5>
                    <span class="product-badge">ID: #{{ $product->id_product }}</span>
                </div>
            </div>
        <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong><i class="fas fa-exclamation-triangle me-2"></i>Terjadi Kesalahan!</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            <form action="{{ route('admin_product.update', $product->id_product) }}" method="POST">
                @csrf
                    @method('POST')
                    
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-info-circle"></i>
                            Informasi Produk
                        </div>
                        
                        <div class="form-row">
                <div class="mb-3">
                                <label for="nama_product" class="form-label">
                                    <i class="fas fa-tag"></i>
                                    Nama Produk
                                </label>
                                <input type="text" 
                                       name="nama_product" 
                                       id="nama_product"
                                       class="form-control @error('nama_product') is-invalid @enderror" 
                                       value="{{ old('nama_product', $product->nama_product) }}"
                                       placeholder="Masukkan nama produk"
                                       required>
                                @error('nama_product')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                </div>

                <div class="mb-3">
                                <label for="barcode" class="form-label">
                                    <i class="fas fa-barcode"></i>
                                    Barcode
                                </label>
                                <input type="text" 
                                       name="barcode" 
                                       id="barcode"
                                       class="form-control @error('barcode') is-invalid @enderror" 
                                       value="{{ old('barcode', $product->barcode) }}"
                                       placeholder="Masukkan barcode produk"
                                       required>
                                @error('barcode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                </div>

                <div class="mb-3">
                            <label for="kategori_id" class="form-label">
                                <i class="fas fa-layer-group"></i>
                                Kategori
                            </label>
                            <select name="kategori_id" 
                                    id="kategori_id"
                                    class="form-select @error('kategori_id') is-invalid @enderror">
                                <option value="">Pilih Kategori</option>
                                @if(isset($kategoris))
                                    @foreach($kategoris as $kat)
                                        <option value="{{ $kat->id_kategori }}" {{ old('kategori_id', $product->kategori_id) == $kat->id_kategori ? 'selected' : '' }}>
                                            {{ $kat->nama_kategori }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="{{ $product->kategori_id }}" selected>Kategori ID: {{ $product->kategori_id }}</option>
                                @endif
                            </select>
                            @error('kategori_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-list"></i>
                            Detail Produk
                </div>

                <div class="mb-3">
                            <label for="komposisi" class="form-label">
                                <i class="fas fa-flask"></i>
                                Komposisi
                            </label>
                            <textarea name="komposisi" 
                                      id="komposisi"
                                      class="form-control @error('komposisi') is-invalid @enderror" 
                                      rows="4"
                                      placeholder="Masukkan komposisi produk">{{ old('komposisi', $product->komposisi) }}</textarea>
                            @error('komposisi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                </div>

                <div class="mb-3">
                            <label for="info_gizi" class="form-label">
                                <i class="fas fa-chart-pie"></i>
                                Info Gizi
                            </label>
                            <textarea name="info_gizi" 
                                      id="info_gizi"
                                      class="form-control @error('info_gizi') is-invalid @enderror" 
                                      rows="4"
                                      placeholder="Masukkan informasi gizi produk">{{ old('info_gizi', $product->info_gizi) }}</textarea>
                            @error('info_gizi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-shield-check"></i>
                            Status Halal
                </div>

                <div class="mb-3">
                            <label for="status" class="form-label">
                                <i class="fas fa-certificate"></i>
                                Status Halal
                            </label>
                            <select name="status" 
                                    id="status"
                                    class="form-select @error('status') is-invalid @enderror" 
                                    required>
                                <option value="halal" {{ old('status', $product->status) == 'halal' ? 'selected' : '' }}>Halal</option>
                                <option value="haram" {{ old('status', $product->status) == 'haram' ? 'selected' : '' }}>Haram</option>
                                <option value="syubhat" {{ old('status', $product->status) == 'syubhat' ? 'selected' : '' }}>Syubhat</option>
                    </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                </div>

                    <div class="btn-group-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Produk
                        </button>
                        <a href="{{ route('admin_product') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                    </div>
            </form>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-focus on first input
        document.addEventListener('DOMContentLoaded', function() {
            const firstInput = document.getElementById('nama_product');
            if (firstInput) {
                firstInput.focus();
                firstInput.select();
            }
        });
        
        // Form validation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const status = document.getElementById('status').value;
            if (!status) {
                e.preventDefault();
                alert('Silakan pilih status halal produk!');
                return false;
            }
        });
    </script>
</body>
</html>
@endsection
