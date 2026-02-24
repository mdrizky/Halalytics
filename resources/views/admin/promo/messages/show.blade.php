@extends('master')

@section('isi')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Pesan Masuk</h4>
            <p class="mb-0">Pesan dari pengguna atau partner via website promo</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.promo.messages.index') }}">Pesan</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Baca</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Detail Pesan</h4>
                <a href="{{ route('admin.promo.messages.index') }}" class="btn btn-secondary btn-sm"><i class="fa fa-arrow-left"></i> Kembali</a>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h5 class="text-primary">{{ $message->subject ?? '(Tanpa Subjek)' }}</h5>
                    <p class="mb-0 text-muted">Dari: <strong>{{ $message->name }}</strong> ({{ $message->email }})</p>
                    <p class="text-muted"><small>Tanggal: {{ $message->created_at->format('d M Y, H:i') }}</small></p>
                </div>
                <hr>
                <div class="message-content mt-4" style="white-space: pre-wrap; font-size: 1.1em; line-height: 1.8;">{{ $message->message }}</div>
                
                <hr class="mt-5">
                <div class="d-flex justify-content-end">
                    <a href="mailto:{{ $message->email }}?subject=RE: {{ $message->subject ?? 'Balasan dari HalalScan AI' }}" class="btn btn-success mr-2">
                        <i class="fa fa-reply"></i> Balas ke Email
                    </a>
                    <form action="{{ route('admin.promo.messages.destroy', $message->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesan ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Hapus Pesan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
