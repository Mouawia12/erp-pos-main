@extends('admin.layouts.master')
@section('title') لوحة المالك @endsection
@section('content')
<div class="row mt-3 mb-3">
    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
        <div class="card overflow-hidden sales-card bg-primary-gradient">
            <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                <h3 class="mb-3 text-white">إجمالي المشتركين</h3>
                <div class="d-flex">
                    <h1 class="tx-30 font-weight-bold mb-1 text-white">{{ $stats['total'] ?? 0 }}</h1>
                    <span class="float-right my-auto mr-auto"><i class="fa fa-building fa-2x text-white"></i></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
        <div class="card overflow-hidden sales-card bg-success-gradient">
            <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                <h3 class="mb-3 text-white">مشتركين نشطين</h3>
                <div class="d-flex">
                    <h1 class="tx-30 font-weight-bold mb-1 text-white">{{ $stats['active'] ?? 0 }}</h1>
                    <span class="float-right my-auto mr-auto"><i class="fa fa-check-circle fa-2x text-white"></i></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
        <div class="card overflow-hidden sales-card bg-warning-gradient">
            <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                <h3 class="mb-3 text-white">قرب الانتهاء</h3>
                <div class="d-flex">
                    <h1 class="tx-30 font-weight-bold mb-1 text-white">{{ $stats['near_expiry'] ?? 0 }}</h1>
                    <span class="float-right my-auto mr-auto"><i class="fa fa-hourglass-half fa-2x text-white"></i></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
        <div class="card overflow-hidden sales-card bg-danger-gradient">
            <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                <h3 class="mb-3 text-white">منتهي</h3>
                <div class="d-flex">
                    <h1 class="tx-30 font-weight-bold mb-1 text-white">{{ $stats['expired'] ?? 0 }}</h1>
                    <span class="float-right my-auto mr-auto"><i class="fa fa-ban fa-2x text-white"></i></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header pb-0">
                <h5 class="mb-0">مشتركين قرب الانتهاء</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead>
                        <tr>
                            <th>الشركة</th>
                            <th>انتهاء الاشتراك</th>
                            <th>الحالة</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($nearExpiryList as $sub)
                            <tr>
                                <td>{{ $sub->company_name }}</td>
                                <td>{{ optional($sub->subscription_end)->format('Y-m-d') ?? '-' }}</td>
                                <td><span class="badge badge-warning">قرب الانتهاء</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="3">لا يوجد</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
