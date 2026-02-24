@extends('master')

@section('isi')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Pesan Masuk</h4>
            <p class="mb-0">Pesan dari pengguna atau partner via website promo</p>
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
                <h4 class="card-title">Daftar Pesan Masuk</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-sm text-center">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th class="text-left">Pengirim</th>
                                <th class="text-left">Subjek</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($messages as $msg)
                            <tr class="{{ !$msg->is_read ? 'font-weight-bold bg-light' : '' }}">
                                <td>{{ $msg->created_at->format('d M Y, H:i') }}</td>
                                <td class="text-left">
                                    {{ $msg->name }}<br>
                                    <small>{{ $msg->email }}</small>
                                </td>
                                <td class="text-left">{{ Str::limit($msg->subject, 30, '...') }}</td>
                                <td>
                                    @if($msg->is_read)
                                    <span class="badge badge-light">Sudah Dibaca</span>
                                    @else
                                    <span class="badge badge-danger">Baru</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <!-- Hapus button dan view button -->
                                        <div class="btn-group">
                                            <a href="{{ route('admin.promo.messages.show', $msg->id) }}" class="btn btn-info btn-xs mr-2"><i class="fa fa-envelope-open"></i> Buka</a>
                                            <form action="{{ route('admin.promo.messages.destroy', $msg->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">Belum ada pesan masuk.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $messages->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
