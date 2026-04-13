@extends('admin.layouts.admin_layout')

@section('content')
    @hasSection('header')
        <div class="mb-6">
            @yield('header')
        </div>
    @endif

    <div class="legacy-admin-shell">
        @yield('content')
    </div>
@endsection

@push('styles')
<style>
    .legacy-admin-shell .container-fluid {
        max-width: 100%;
        margin: 0 auto;
    }

    .legacy-admin-shell .card {
        border-radius: 1.25rem;
        border: 1px solid rgba(148, 163, 184, 0.18);
        background: #ffffff;
        box-shadow: 0 18px 50px rgba(0, 77, 64, 0.08);
    }

    .legacy-admin-shell .card-header,
    .legacy-admin-shell .card-body,
    .legacy-admin-shell .card-footer {
        background: transparent;
    }

    .legacy-admin-shell .btn-primary,
    .legacy-admin-shell .bg-primary {
        background: #004D40 !important;
        border-color: #004D40 !important;
        color: #ffffff !important;
    }

    .legacy-admin-shell .btn-outline-success,
    .legacy-admin-shell .text-primary {
        color: #004D40 !important;
    }

    .legacy-admin-shell .badge.bg-success,
    .legacy-admin-shell .bg-success {
        background: #26A69A !important;
        color: #ffffff !important;
    }

    .legacy-admin-shell .badge.bg-warning,
    .legacy-admin-shell .bg-warning {
        background: #B8E6DE !important;
        color: #004D40 !important;
    }

    .legacy-admin-shell .badge.bg-danger,
    .legacy-admin-shell .bg-danger {
        background: #D32F2F !important;
        color: #ffffff !important;
    }

    .legacy-admin-shell .badge.bg-light,
    .legacy-admin-shell .bg-light {
        background: #E0F2F1 !important;
        color: #004D40 !important;
    }

    .legacy-admin-shell .form-control,
    .legacy-admin-shell .form-select,
    .legacy-admin-shell .input-group-text {
        border-radius: 1rem !important;
        border-color: #d4e7e4 !important;
        box-shadow: none !important;
    }

    .legacy-admin-shell .pagination .page-link {
        border-radius: 9999px;
        margin-inline: 0.125rem;
        color: #004D40;
    }

    .legacy-admin-shell .pagination .active .page-link {
        background: #004D40;
        border-color: #004D40;
        color: #ffffff;
    }
</style>
@endpush
