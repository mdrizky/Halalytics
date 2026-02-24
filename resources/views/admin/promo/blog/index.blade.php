@extends('master')

@section('isi')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Manajemen Blog Promo</h4>
            <p class="mb-0">Kelola artikel edukasi dan promosi aplikasi</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Blog</a></li>
        </ol>
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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Daftar Artikel</h4>
                <a href="{{ route('admin.promo.blog.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Artikel Baru</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-sm text-center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Sampul</th>
                                <th class="text-left">Judul</th>
                                <th>Kategori</th>
                                <th>Views</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($blogs as $key => $blog)
                            <tr>
                                <th>{{ $blogs->firstItem() + $key }}</th>
                                <td>
                                    @if($blog->image)
                                    <img src="{{ $blog->image_url }}" alt="..." class="rounded-lg" width="50" height="50" style="object-fit: cover;">
                                    @else
                                    <div class="bg-light rounded-lg d-flex align-items-center justify-content-center" style="width:50px; height:50px; margin: 0 auto;">
                                        <i class="fa fa-image text-muted"></i>
                                    </div>
                                    @endif
                                </td>
                                <td class="text-left">
                                    <strong>{{ Str::limit($blog->title, 40) }}</strong><br>
                                    <small class="text-muted">{{ $blog->formatted_date }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-light">{{ $blog->category ?? '-' }}</span>
                                </td>
                                <td>{{ $blog->views }}</td>
                                <td>
                                    @if($blog->status == 'published')
                                    <span class="badge badge-success">Terbit</span>
                                    @else
                                    <span class="badge badge-warning">Draft</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center">
                                        <a href="{{ route('blog.show', $blog->slug) }}" target="_blank" class="btn btn-info shadow btn-xs sharp mr-1" title="Lihat"><i class="fa fa-eye"></i></a>
                                        <a href="{{ route('admin.promo.blog.edit', $blog->id) }}" class="btn btn-primary shadow btn-xs sharp mr-1" title="Edit"><i class="fa fa-pencil"></i></a>
                                        
                                        <form action="{{ route('admin.promo.blog.toggle', $blog->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-warning shadow btn-xs sharp mr-1" title="Toggle Status">
                                                <i class="fa fa-refresh"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.promo.blog.destroy', $blog->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus artikel ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger shadow btn-xs sharp" title="Hapus"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7">Belum ada artikel edukasi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $blogs->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
