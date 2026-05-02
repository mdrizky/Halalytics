@extends('user.layouts.app')

@section('title', 'Compose Order - Halalytics')

@section('content')
<section class="page-hero mb-4">
    <div class="row g-4 align-items-end">
        <div class="col-lg-8">
            <span class="badge badge-soft rounded-pill px-3 py-2 mb-3">Compose / Buat Pesanan</span>
            <h1 class="display-6 fw-bold mb-2">Pilih produk, atur kuantitas, lalu lanjutkan ke checkout.</h1>
            <p class="mb-0 text-white-50">Halaman ini disiapkan khusus untuk demo presentasi agar alur user terlihat jelas sejak pemilihan produk.</p>
        </div>
        <div class="col-lg-4">
            <form method="GET" action="{{ route('user.compose') }}" class="surface-card p-3">
                <div class="row g-2">
                    <div class="col-12">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control rounded-4" placeholder="Cari produk">
                    </div>
                    <div class="col-8">
                        <select name="category" class="form-select rounded-4">
                            <option value="">Semua kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id_kategori }}" @selected((string) request('category') === (string) $category->id_kategori)>{{ $category->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-4 d-grid">
                        <button class="btn btn-light rounded-4 fw-bold" type="submit">Cari</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="row g-4">
    <div class="col-lg-8">
        <div class="row g-3">
            @forelse($products as $product)
                <div class="col-md-6">
                    <div class="product-card h-100 overflow-hidden">
                        <div class="row g-0 h-100">
                            <div class="col-4">
                                <img src="{{ $product->image }}" alt="{{ $product->nama_product }}" class="product-thumb h-100" onerror="this.onerror=null;this.src='{{ $product->image_fallback_url }}'">
                            </div>
                            <div class="col-8">
                                <div class="p-3 h-100 d-flex flex-column">
                                    <div class="d-flex justify-content-between gap-2 mb-2">
                                        <span class="badge badge-soft rounded-pill">{{ $product->kategori->nama_kategori ?? 'Umum' }}</span>
                                        <span class="status-pill status-{{ str_replace(' ', '_', strtolower($product->status ?? 'pending')) }}">{{ strtoupper($product->status ?? 'UNKNOWN') }}</span>
                                    </div>
                                    <h2 class="h6 fw-bold mb-2">{{ $product->nama_product }}</h2>
                                    <div class="small text-secondary mb-1">{{ $product->barcode ?: 'Tanpa barcode' }}</div>
                                    <div class="fw-bold mb-3">Rp{{ number_format((float) ($product->price ?? 0), 0, ',', '.') }}</div>
                                    <div class="mt-auto d-flex gap-2">
                                        <a href="{{ route('user.products.show', $product) }}" class="btn btn-sm btn-outline-dark rounded-pill">Detail</a>
                                        <form action="{{ route('user.cart.add') }}" method="POST" class="flex-grow-1">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id_product }}">
                                            <div class="input-group input-group-sm">
                                                <input type="number" class="form-control rounded-start-pill" min="1" max="20" name="quantity" value="1">
                                                <button class="btn btn-brand rounded-end-pill" type="submit">Tambah</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="surface-card p-5 text-center">
                        <div class="fw-bold h4 mb-2">Belum ada produk yang cocok</div>
                        <p class="text-secondary mb-0">Coba ganti filter pencarian untuk compose order.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </div>

    <div class="col-lg-4">
        <div class="surface-card p-4 summary-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="h4 fw-bold mb-1">Ringkasan Cart</h2>
                    <p class="text-secondary mb-0">{{ $cartSummary['item_count'] }} item siap checkout</p>
                </div>
                <a href="{{ route('user.cart.index') }}" class="btn btn-sm btn-ghost-brand rounded-pill">Kelola</a>
            </div>

            @forelse($cartItems as $item)
                <div class="d-flex align-items-center gap-3 py-3 border-top">
                    <img src="{{ $item['product']->image }}" alt="{{ $item['product']->nama_product }}" width="58" height="58" class="rounded-4 object-fit-cover" onerror="this.onerror=null;this.src='{{ $item['product']->image_fallback_url }}'">
                    <div class="flex-grow-1">
                        <div class="fw-semibold">{{ $item['product']->nama_product }}</div>
                        <div class="small text-secondary">{{ $item['quantity'] }} x Rp{{ number_format($item['unit_price'], 0, ',', '.') }}</div>
                    </div>
                    <div class="fw-bold small">Rp{{ number_format($item['line_total'], 0, ',', '.') }}</div>
                </div>
            @empty
                <div class="text-center py-4 text-secondary">Keranjang masih kosong.</div>
            @endforelse

            <div class="border-top pt-3 mt-3">
                <div class="d-flex justify-content-between mb-2"><span class="text-secondary">Subtotal</span><span class="fw-semibold">Rp{{ number_format($cartSummary['subtotal'], 0, ',', '.') }}</span></div>
                <div class="d-flex justify-content-between mb-2"><span class="text-secondary">Ongkir</span><span class="fw-semibold">Rp{{ number_format($cartSummary['shipping_fee'], 0, ',', '.') }}</span></div>
                <div class="d-flex justify-content-between fs-5 fw-bold"><span>Total</span><span>Rp{{ number_format($cartSummary['total'], 0, ',', '.') }}</span></div>
            </div>

            <div class="d-grid gap-2 mt-4">
                <a href="{{ route('user.cart.index') }}" class="btn btn-outline-dark rounded-pill">Buka Keranjang</a>
                <a href="{{ route('user.checkout') }}" class="btn btn-brand rounded-pill {{ $cartSummary['item_count'] === 0 ? 'disabled' : '' }}">Lanjut Checkout</a>
            </div>
        </div>
    </div>
</section>
@endsection
