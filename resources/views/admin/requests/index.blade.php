@extends('admin.layouts.admin_layout')

@section('title', 'Product Requests - Halalytics Admin')
@section('breadcrumb-parent', 'Crowdsourcing')
@section('breadcrumb-current', 'Product Requests')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Pending Product Requests</h4>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Barcode</th>
                                <th>Product Name</th>
                                <th>Images</th>
                                <th>OCR Text</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $request)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $request->user->username ?? 'Unknown' }}</td>
                                <td>{{ $request->barcode }}</td>
                                <td>{{ $request->product_name }}</td>
                                <td>
                                    @if($request->image_front)
                                        <a href="{{ asset('storage/' . $request->image_front) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $request->image_front) }}" width="50" class="img-thumbnail" alt="Front">
                                        </a>
                                    @endif
                                    @if($request->image_back)
                                        <a href="{{ asset('storage/' . $request->image_back) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $request->image_back) }}" width="50" class="img-thumbnail" alt="Back">
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#ocrModal{{ $request->id }}">
                                        View Text
                                    </button>

                                    <!-- OCR Modal -->
                                    <div class="modal fade" id="ocrModal{{ $request->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">OCR Text Result</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <pre class="bg-light p-3">{{ $request->ocr_text }}</pre>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $request->created_at->format('d M Y H:i') }}</td>
                                <td>
                                    <form action="{{ route('admin.requests.approve', $request->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this request?')">Approve</button>
                                    </form>
                                    
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}">
                                        Reject
                                    </button>

                                    <!-- Reject Modal -->
                                    <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form action="{{ route('admin.requests.reject', $request->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Reject Request</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>Reason for Rejection</label>
                                                            <textarea name="reason" class="form-control" rows="3" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Reject</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">No pending requests.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>
@endsection