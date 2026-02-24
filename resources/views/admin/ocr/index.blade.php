@extends('admin.master')

@section('title', 'OCR Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">OCR Product Management</h1>
        <div>
            <button class="btn btn-sm btn-primary" onclick="refreshOCRData()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <a href="{{ route('admin.ocr.statistics') }}" class="btn btn-sm btn-info">
                <i class="fas fa-chart-bar"></i> Statistics
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Scans
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalScans">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-qrcode fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Review
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="pendingReview">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Approved Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="approvedToday">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Rejected Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="rejectedToday">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#" data-status="pending" onclick="filterOCRProducts('pending')">
                        <i class="fas fa-clock"></i> Pending Review
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-status="approved" onclick="filterOCRProducts('approved')">
                        <i class="fas fa-check"></i> Approved
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-status="rejected" onclick="filterOCRProducts('rejected')">
                        <i class="fas fa-times"></i> Rejected
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-status="all" onclick="filterOCRProducts('all')">
                        <i class="fas fa-list"></i> All
                    </a>
                </li>
            </ul>
        </div>

        <!-- OCR Products List -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="ocrProductsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Barcode</th>
                            <th>Images</th>
                            <th>Extracted Text</th>
                            <th>Ingredients</th>
                            <th>Confidence</th>
                            <th>Step</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Products will be loaded here -->
                    </tbody>
                </table>
            </div>
            
            <!-- Loading Spinner -->
            <div id="loadingSpinner" class="text-center py-4" style="display: none;">
                <div class="spinner-border" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            
            <!-- Empty State -->
            <div id="emptyState" class="text-center py-4" style="display: none;">
                <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                <p class="text-gray-500">No OCR products found</p>
            </div>
        </div>
    </div>
</div>

<!-- OCR Product Detail Modal -->
<div class="modal fade" id="ocrProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">OCR Product Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Front Image</h6>
                        <img id="frontImagePreview" class="img-fluid mb-3" style="max-height: 200px;">
                    </div>
                    <div class="col-md-6">
                        <h6>Back Image</h6>
                        <img id="backImagePreview" class="img-fluid mb-3" style="max-height: 200px;">
                    </div>
                </div>
                
                <div class="mt-3">
                    <h6>Extracted Text</h6>
                    <div id="extractedTextDisplay" class="border rounded p-2 bg-light" style="max-height: 150px; overflow-y: auto;"></div>
                </div>
                
                <div class="mt-3">
                    <h6>Detected Ingredients</h6>
                    <div id="ingredientsList" class="border rounded p-2 bg-light" style="max-height: 150px; overflow-y: auto;"></div>
                </div>
                
                <div class="mt-3">
                    <h6>Processing Information</h6>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Confidence Score:</strong></td>
                            <td id="confidenceScoreDisplay"></td>
                        </tr>
                        <tr>
                            <td><strong>Processing Step:</strong></td>
                            <td id="processingStepDisplay"></td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td id="statusDisplay"></td>
                        </tr>
                        <tr>
                            <td><strong>Created:</strong></td>
                            <td id="createdDisplay"></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="approveOCRProduct()">
                    <i class="fas fa-check"></i> Approve
                </button>
                <button type="button" class="btn btn-danger" onclick="rejectOCRProduct()">
                    <i class="fas fa-times"></i> Reject
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject OCR Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="rejectionReason" class="form-label">Rejection Reason</label>
                    <textarea class="form-control" id="rejectionReason" rows="3" placeholder="Please specify the reason for rejection..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="confirmReject()">Reject</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentOCRProduct = null;
let currentFilter = 'pending';

// Load initial data
$(document).ready(function() {
    loadOCRStatistics();
    loadOCRProducts();
});

// Load OCR statistics
function loadOCRStatistics() {
    $.get('/api/ocr/statistics')
        .done(function(data) {
            if (data.success) {
                $('#totalScans').text(data.data.total_scans);
                $('#pendingReview').text(data.data.pending_review);
                $('#approvedToday').text(data.data.approved_today);
                $('#rejectedToday').text(data.data.rejected_today);
            }
        })
        .fail(function() {
            console.error('Failed to load OCR statistics');
        });
}

// Load OCR products
function loadOCRProducts() {
    $('#loadingSpinner').show();
    $('#ocrProductsTable tbody').empty();
    
    let url = currentFilter === 'all' ? '/api/ocr/pending' : '/api/ocr/pending';
    
    $.get(url)
        .done(function(data) {
            $('#loadingSpinner').hide();
            
            if (data.success && data.data.data.length > 0) {
                renderOCRProducts(data.data.data);
                $('#emptyState').hide();
            } else {
                $('#emptyState').show();
            }
        })
        .fail(function() {
            $('#loadingSpinner').hide();
            $('#emptyState').show();
            console.error('Failed to load OCR products');
        });
}

// Render OCR products
function renderOCRProducts(products) {
    let tbody = $('#ocrProductsTable tbody');
    tbody.empty();
    
    products.forEach(function(product) {
        let statusBadge = getStatusBadge(product.status);
        let stepBadge = getStepBadge(product.processing_step);
        
        let row = `
            <tr>
                <td>${product.id}</td>
                <td>${product.user.name}</td>
                <td>${product.barcode || '-'}</td>
                <td>
                    ${product.front_image ? `<img src="${product.front_image_url}" width="30" class="me-1">` : ''}
                    ${product.back_image ? `<img src="${product.back_image_url}" width="30">` : ''}
                </td>
                <td>
                    <small class="text-muted">${product.extracted_text ? product.extracted_text.substring(0, 50) + '...' : '-'}</small>
                </td>
                <td>
                    <small class="text-muted">${product.ingredients ? product.ingredients.length + ' items' : '-'}</small>
                </td>
                <td>
                    <span class="badge bg-info">${(product.confidence_score * 100).toFixed(1)}%</span>
                </td>
                <td>${stepBadge}</td>
                <td>${statusBadge}</td>
                <td>${new Date(product.created_at).toLocaleDateString()}</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="viewOCRProduct(${product.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    ${product.status === 'pending' ? `
                        <button class="btn btn-sm btn-success" onclick="quickApprove(${product.id})">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="quickReject(${product.id})">
                            <i class="fas fa-times"></i>
                        </button>
                    ` : ''}
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

// Get status badge
function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="badge bg-warning">Pending</span>',
        'approved': '<span class="badge bg-success">Approved</span>',
        'rejected': '<span class="badge bg-danger">Rejected</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
}

// Get step badge
function getStepBadge(step) {
    const badges = {
        'front': '<span class="badge bg-info">Front</span>',
        'back': '<span class="badge bg-primary">Back</span>',
        'processing': '<span class="badge bg-warning">Processing</span>',
        'complete': '<span class="badge bg-success">Complete</span>'
    };
    return badges[step] || '<span class="badge bg-secondary">Unknown</span>';
}

// View OCR product details
function viewOCRProduct(productId) {
    $.get(`/api/ocr/product/${productId}`)
        .done(function(data) {
            if (data.success) {
                currentOCRProduct = data.data;
                displayOCRProductDetails(currentOCRProduct);
                $('#ocrProductModal').modal('show');
            }
        })
        .fail(function() {
            alert('Failed to load OCR product details');
        });
}

// Display OCR product details
function displayOCRProductDetails(product) {
    $('#frontImagePreview').attr('src', product.front_image_url || '/img/no-image.png');
    $('#backImagePreview').attr('src', product.back_image_url || '/img/no-image.png');
    $('#extractedTextDisplay').text(product.extracted_text || 'No text extracted');
    
    let ingredientsHtml = '';
    if (product.ingredients && product.ingredients.length > 0) {
        product.ingredients.forEach(function(ingredient) {
            ingredientsHtml += `<span class="badge bg-secondary me-1">${ingredient}</span>`;
        });
    } else {
        ingredientsHtml = 'No ingredients detected';
    }
    $('#ingredientsList').html(ingredientsHtml);
    
    $('#confidenceScoreDisplay').text((product.confidence_score * 100).toFixed(1) + '%');
    $('#processingStepDisplay').text(product.processing_step || 'Unknown');
    $('#statusDisplay').html(getStatusBadge(product.status));
    $('#createdDisplay').text(new Date(product.created_at).toLocaleString());
}

// Approve OCR product
function approveOCRProduct() {
    if (!currentOCRProduct) return;
    
    if (confirm('Are you sure you want to approve this OCR product?')) {
        $.post(`/api/ocr/approve/${currentOCRProduct.id}`, {
            notes: 'Approved by admin'
        })
        .done(function(data) {
            if (data.success) {
                $('#ocrProductModal').modal('hide');
                loadOCRProducts();
                loadOCRStatistics();
                alert('OCR product approved successfully!');
            } else {
                alert('Failed to approve OCR product: ' + data.message);
            }
        })
        .fail(function() {
            alert('Failed to approve OCR product');
        });
    }
}

// Reject OCR product
function rejectOCRProduct() {
    if (!currentOCRProduct) return;
    $('#rejectModal').modal('show');
}

// Confirm reject
function confirmReject() {
    if (!currentOCRProduct) return;
    
    let reason = $('#rejectionReason').val();
    if (!reason.trim()) {
        alert('Please provide a rejection reason');
        return;
    }
    
    $.post(`/api/ocr/reject/${currentOCRProduct.id}`, {
        reason: reason
    })
    .done(function(data) {
        if (data.success) {
            $('#rejectModal').modal('hide');
            $('#ocrProductModal').modal('hide');
            $('#rejectionReason').val('');
            loadOCRProducts();
            loadOCRStatistics();
            alert('OCR product rejected successfully!');
        } else {
            alert('Failed to reject OCR product: ' + data.message);
        }
    })
    .fail(function() {
        alert('Failed to reject OCR product');
    });
}

// Quick approve
function quickApprove(productId) {
    if (confirm('Are you sure you want to approve this OCR product?')) {
        $.post(`/api/ocr/approve/${productId}`, {
            notes: 'Quick approved by admin'
        })
        .done(function(data) {
            if (data.success) {
                loadOCRProducts();
                loadOCRStatistics();
            } else {
                alert('Failed to approve: ' + data.message);
            }
        })
        .fail(function() {
            alert('Failed to approve OCR product');
        });
    }
}

// Quick reject
function quickReject(productId) {
    let reason = prompt('Please provide rejection reason:');
    if (!reason) return;
    
    $.post(`/api/ocr/reject/${productId}`, {
        reason: reason
    })
    .done(function(data) {
        if (data.success) {
            loadOCRProducts();
            loadOCRStatistics();
        } else {
            alert('Failed to reject: ' + data.message);
        }
    })
    .fail(function() {
        alert('Failed to reject OCR product');
    });
}

// Filter OCR products
function filterOCRProducts(status) {
    currentFilter = status;
    
    // Update active tab
    $('.nav-link').removeClass('active');
    $(`.nav-link[data-status="${status}"]`).addClass('active');
    
    loadOCRProducts();
}

// Refresh OCR data
function refreshOCRData() {
    loadOCRStatistics();
    loadOCRProducts();
}
</script>
@endpush
