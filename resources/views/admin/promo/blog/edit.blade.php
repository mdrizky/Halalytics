@extends('master')

@section('isi')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Edit Artikel</h4>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.promo.blog.index') }}">Blog</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Edit</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Form Artikel Edukasi</h4>
            </div>
            <div class="card-body">
                @if ($errors->any())
                <div class="alert alert-danger solid alert-dismissible fade show">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span><i class="mdi mdi-close"></i></span></button>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('admin.promo.blog.update', $blog->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="form-group">
                                <label>Judul Artikel <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" value="{{ old('title', $blog->title) }}" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Konten <span class="text-danger">*</span></label>
                                <textarea name="content" class="form-control" rows="15" required>{{ old('content', $blog->content) }}</textarea>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Kategori</label>
                                <input type="text" name="category" class="form-control" value="{{ old('category', $blog->category) }}">
                            </div>
                            
                            <div class="form-group">
                                <label>Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-control">
                                    <option value="draft" {{ old('status', $blog->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status', $blog->status) == 'published' ? 'selected' : '' }}>Published</option>
                                </select>
                            </div>
                            
                            @if($blog->image)
                            <div class="form-group">
                                <label>Gambar Saat Ini</label><br>
                                <img src="{{ $blog->image_url }}" alt="Current Image" class="img-fluid rounded mb-2" style="max-height: 150px;">
                            </div>
                            @endif
                            
                            <div class="form-group">
                                <label>Ganti Gambar Sampul (Kosongkan bila tidak ingin diganti)</label>
                                <input type="file" name="image" class="form-control-file" accept="image/*">
                            </div>
                            
                            <div class="form-group mt-5">
                                <button type="submit" class="btn btn-primary btn-block">Simpan Perubahan</button>
                                <a href="{{ route('admin.promo.blog.index') }}" class="btn btn-light btn-block mt-2">Batal</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
