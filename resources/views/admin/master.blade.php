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
    /*
     * Legacy Bootstrap → Tailwind Bridge
     * These styles convert Bootstrap classes to match the Halalytics Tailwind design system.
     * Used by views still extending admin.master:
     *   - products/openfoodfacts/search|preview|index
     *   - medicine, cosmetic, produkt_edit
     *   - notifications/create
     */
    .legacy-admin-shell .row {
        display: flex;
        flex-wrap: wrap;
        margin: -0.5rem;
    }
    .legacy-admin-shell .row > [class*="col-"] {
        padding: 0.5rem;
        width: 100%;
    }
    .legacy-admin-shell .col-sm-6  { width: 50%; }
    .legacy-admin-shell .col-md-4  { width: 33.333%; }
    .legacy-admin-shell .col-md-6  { width: 50%; }
    .legacy-admin-shell .col-md-8  { width: 66.667%; }
    .legacy-admin-shell .col-md-12,
    .legacy-admin-shell .col-12    { width: 100%; }

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

    .legacy-admin-shell .table {
        width: 100%;
        border-collapse: collapse;
    }
    .legacy-admin-shell .table th,
    .legacy-admin-shell .table td {
        padding: 0.75rem 1rem;
        text-align: left;
        border-bottom: 1px solid rgba(148, 163, 184, 0.15);
    }
    .legacy-admin-shell .table thead th {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        background: transparent;
    }
    .legacy-admin-shell .table tbody tr:hover {
        background: rgba(0, 77, 64, 0.03);
    }
    .legacy-admin-shell .table-striped tbody tr:nth-of-type(odd) {
        background: rgba(244, 249, 248, 0.5);
    }
    .legacy-admin-shell .table-bordered th,
    .legacy-admin-shell .table-bordered td {
        border: 1px solid rgba(148, 163, 184, 0.15);
    }

    .legacy-admin-shell .btn-primary,
    .legacy-admin-shell .bg-primary {
        background: #004D40 !important;
        border-color: #004D40 !important;
        color: #ffffff !important;
    }
    .legacy-admin-shell .btn-success {
        background: #26A69A !important;
        border-color: #26A69A !important;
        color: #ffffff !important;
    }
    .legacy-admin-shell .btn-danger {
        background: #D32F2F !important;
        border-color: #D32F2F !important;
        color: #ffffff !important;
    }
    .legacy-admin-shell .btn-warning {
        background: #F59E0B !important;
        border-color: #F59E0B !important;
        color: #ffffff !important;
    }
    .legacy-admin-shell .btn-info {
        background: #0284C7 !important;
        border-color: #0284C7 !important;
        color: #ffffff !important;
    }
    .legacy-admin-shell .btn-secondary {
        background: #64748B !important;
        border-color: #64748B !important;
        color: #ffffff !important;
    }
    .legacy-admin-shell .btn-sm {
        padding: 0.25rem 0.75rem;
        font-size: 0.75rem;
        border-radius: 0.5rem;
    }
    .legacy-admin-shell .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 0.75rem;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .legacy-admin-shell .btn:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    .legacy-admin-shell .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.2em 0.6em;
        font-size: 0.75rem;
        font-weight: 700;
        border-radius: 9999px;
    }
    .legacy-admin-shell .badge.bg-success,
    .legacy-admin-shell .bg-success {
        background: #26A69A !important;
        color: #ffffff !important;
    }
    .legacy-admin-shell .badge.bg-warning,
    .legacy-admin-shell .bg-warning {
        background: #F59E0B !important;
        color: #ffffff !important;
    }
    .legacy-admin-shell .badge.bg-danger,
    .legacy-admin-shell .bg-danger {
        background: #D32F2F !important;
        color: #ffffff !important;
    }
    .legacy-admin-shell .badge.bg-info {
        background: #0284C7 !important;
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
        border-radius: 0.75rem !important;
        border: 1px solid #d4e7e4 !important;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        background: #ffffff;
        transition: border-color 0.15s, box-shadow 0.15s;
        box-shadow: none !important;
        width: 100%;
    }
    .legacy-admin-shell .form-control:focus,
    .legacy-admin-shell .form-select:focus {
        border-color: #004D40 !important;
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 77, 64, 0.1) !important;
    }
    .legacy-admin-shell .form-label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.375rem;
    }
    .legacy-admin-shell .form-group {
        margin-bottom: 1rem;
    }
    .legacy-admin-shell select.form-select {
        appearance: auto;
    }

    .legacy-admin-shell .alert {
        padding: 1rem;
        border-radius: 0.75rem;
        margin-bottom: 1rem;
        font-size: 0.875rem;
    }
    .legacy-admin-shell .alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    .legacy-admin-shell .alert-danger,
    .legacy-admin-shell .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
    .legacy-admin-shell .alert-warning {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }
    .legacy-admin-shell .alert-info {
        background: #e0f2fe;
        color: #075985;
        border: 1px solid #bae6fd;
    }

    .legacy-admin-shell .pagination {
        display: flex;
        list-style: none;
        padding: 0;
        gap: 0.25rem;
        flex-wrap: wrap;
    }
    .legacy-admin-shell .pagination .page-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.25rem;
        height: 2.25rem;
        border-radius: 9999px;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #004D40;
        background: transparent;
        border: 1px solid transparent;
        text-decoration: none;
        transition: all 0.15s;
    }
    .legacy-admin-shell .pagination .page-link:hover {
        background: #E0F2F1;
    }
    .legacy-admin-shell .pagination .active .page-link {
        background: #004D40;
        border-color: #004D40;
        color: #ffffff;
    }
    .legacy-admin-shell .pagination .disabled .page-link {
        opacity: 0.4;
        pointer-events: none;
    }

    .legacy-admin-shell h1, .legacy-admin-shell h2, .legacy-admin-shell h3,
    .legacy-admin-shell h4, .legacy-admin-shell h5 {
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        letter-spacing: -0.02em;
        color: #1e293b;
    }
    .legacy-admin-shell hr {
        border: none;
        border-top: 1px solid rgba(148, 163, 184, 0.2);
        margin: 1rem 0;
    }
    .legacy-admin-shell .float-right { float: right; }
    .legacy-admin-shell .float-left { float: left; }
    .legacy-admin-shell .clearfix::after {
        content: "";
        display: table;
        clear: both;
    }
    .legacy-admin-shell .text-right { text-align: right; }
    .legacy-admin-shell .text-center { text-align: center; }
    .legacy-admin-shell .mt-3 { margin-top: 1rem; }
    .legacy-admin-shell .mb-3 { margin-bottom: 1rem; }
    .legacy-admin-shell .mb-4 { margin-bottom: 1.5rem; }
    .legacy-admin-shell .p-3 { padding: 1rem; }
</style>
@endpush
