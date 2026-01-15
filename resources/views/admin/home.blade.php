@extends('admin.layouts.master')
<style>
    span.float-right > i.fa {
        font-size: 40px !important;
    }

    h3 {
        font-size: 15px !important;
    }
</style>
@section('title')
@if($user->branch_id>1)
{{$user->branch->branch_name_ar}}
@else
@php
    echo env('APP_NAME');
@endphp
@endif
@endsection
@section('page-header')
    <!-- breadcrumb -->

    <div class="breadcrumb-header justify-content-center flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white/90 shadow-sm p-4 md:flex-row md:items-center md:justify-between"> 
        <div class="flex flex-col gap-1">
            <span class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('main.dashboard') }}</span>
            <h3 class="text-base font-semibold text-slate-800">
                {{\Carbon\Carbon::now()->format('d - m - Y')}} · {{ __('main.remaining_days') }} : {{ $remaining_days }}
            </h3>
        </div>
        <div class="flex flex-col gap-2 md:items-end" id="dashboardRange">
            <div class="tw-range-group">
                <button type="button" class="tw-range-btn is-active" data-range="day">{{ __('main.today') }}</button>
                <button type="button" class="tw-range-btn" data-range="week">آخر 7 أيام</button>
                <button type="button" class="tw-range-btn" data-range="month">آخر 30 يوم</button>
            </div>
            <span class="tw-range-meta" id="dashboardUpdatedAt">—</span>
        </div>
    </div>
    <!-- /breadcrumb -->
@endsection
@section('content')
<div class="tw-dashboard">
    @if(session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif 
    <!-- row closed -->

    <div class="row mt-3 mb-3 gap-4"> 
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <a class="get_details" href="@can('عرض مبيعات'){{route('sales')}}@endcan">
                <div class="card overflow-hidden sales-card bg-success-gradient tw-kpi-card tw-fade-up" style="animation-delay:0.05s;"> 
                    <div class="pl-3 pt-3 pr-3 pb-2 pt-0 tw-kpi-content"> 
                        <div>
                            <h3 class="mb-3 text-white stat-label">{{ __('main.today_sales') }}</h3>
                        </div>
                        <div class="pb-0 mt-0">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h1 class="tx-30 font-weight-bold mb-1 text-white stat-value" data-stat-key="sales" data-base="{{ $sales }}">{{ $sales }}</h1>
                                </div>
                                <span class="float-right my-auto mr-auto stat-icon"> 
                                    <i class="fa fa-money-bill fa-2x text-white"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <a class="get_details" href="@can('عرض مردود مبيعات'){{route('sales.return')}}@endcan">
                <div class="card overflow-hidden sales-card bg-warning-gradient tw-kpi-card tw-fade-up" style="animation-delay:0.1s;"> 
                    <div class="pl-3 pt-3 pr-3 pb-2 pt-0 tw-kpi-content"> 
                        <div>
                            <h3 class="mb-3 text-white stat-label">{{ __('main.today_sales_return') }}</h3>
                        </div>
                        <div class="pb-0 mt-0">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h1 class="tx-30 font-weight-bold mb-1 text-white stat-value" data-stat-key="sales_return" data-base="{{ $sales_return * -1 }}">{{ $sales_return * -1 }}</h1>
                                </div>
                                <span class="float-right my-auto mr-auto stat-icon">  
                                    <i class="fa-solid fa-money-bill-transfer fa-2x text-white"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                <a class="get_details" href="@can('عرض مشتريات'){{route('purchases')}} @endcan">
                    <div class="card overflow-hidden sales-card bg-info-gradient tw-kpi-card tw-fade-up" style="animation-delay:0.15s;">
                        <div class="pl-3 pt-3 pr-3 pb-2 pt-0 tw-kpi-content">
                            <div>
                                <h3 class="mb-3 text-white stat-label">{{ __('main.today_purchases') }}</h3>
                            </div>
                            <div class="pb-0 mt-0">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h1 class="tx-30 font-weight-bold mb-1 text-white stat-value" data-stat-key="purchases" data-base="{{ $purchases }}">{{ $purchases }}</h1>
                                    </div>
                                    <span class="float-right my-auto mr-auto stat-icon"> 
                                        <i class="fa fa-cart-shopping fa-2x text-white"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                <a class="get_details" href="@can('عرض مردود مشتريات'){{route('purchase.return')}}@endcan">
                    <div class="card overflow-hidden sales-card bg-danger-gradient tw-kpi-card tw-fade-up" style="animation-delay:0.2s;"> 
                        <div class="pl-3 pt-3 pr-3 pb-2 pt-0 tw-kpi-content"> 
                            <div>
                                <h3 class="mb-3 text-white stat-label">{{ __('main.today_purchase_return') }}</h3>
                            </div>
                            <div class="pb-0 mt-0">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h1 class="tx-30 font-weight-bold mb-1 text-white stat-value" data-stat-key="purchase_return" data-base="{{ $purchases_return * -1 }}">{{ $purchases_return * -1 }}</h1>
                                    </div>
                                    <span class="float-right my-auto mr-auto stat-icon">   
                                        <i class="fa fa-retweet fa-2x text-white" aria-hidden="true"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>  
            <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                <a class="get_details" href="@can('عرض عميل'){{route('clients' , 3)}} @endcan">
                    <div class="card overflow-hidden sales-card bg-primary-gradient tw-kpi-card tw-fade-up" style="animation-delay:0.25s;">
                        <div class="pl-3 pt-3 pr-3 pb-2 pt-0 tw-kpi-content">
                            <div>
                                <h3 class="mb-3 text-white stat-label">{{ __('main.clients') }}</h3>
                            </div>
                            <div class="pb-0 mt-0">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h1 class="tx-30 font-weight-bold mb-1 text-white stat-value" data-stat-key="clients" data-base="{{ $clients->count() }}">{{ $clients->count() }}</h1>
                                    </div>
                                    <span class="float-right my-auto mr-auto stat-icon"> 
                                        <i class="fa fa-user-plus fa-2x text-white"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                <a class="get_details" href="@can('عرض مورد') {{route('clients' , 4)}} @endcan ">
                    <div class="card overflow-hidden sales-card bg-secondary-gradient tw-kpi-card tw-fade-up" style="animation-delay:0.3s;">
                        <div class="pl-3 pt-3 pr-3 pb-2 pt-0 tw-kpi-content">
                            <div>
                                <h3 class="mb-3 text-white stat-label">{{ __('main.suppliers') }}</h3>
                            </div>
                            <div class="pb-0 mt-0">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h1 class="tx-30 font-weight-bold mb-1 text-white stat-value" data-stat-key="suppliers" data-base="{{ $suppliers->count() }}">{{ $suppliers->count() }}</h1>
                                    </div>
                                    <span class="float-right my-auto mr-auto stat-icon">  
                                        <i class="fa fa-cart-plus fa-2x text-white"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                <a class="get_details" href="@can('عرض مستخدم'){{route('admin.admins.index')}} @endcan">
                    <div class="card overflow-hidden sales-card bg-dark tw-kpi-card tw-fade-up" style="animation-delay:0.35s;">
                        <div class="pl-3 pt-3 pr-3 pb-2 pt-0 tw-kpi-content">
                            <div>
                                <h3 class="mb-3 text-white stat-label">{{ __('main.users') }}</h3>
                            </div>
                            <div class="pb-0 mt-0">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h1 class="tx-30 font-weight-bold mb-1 text-white stat-value" data-stat-key="users" data-base="{{ $Admins->count() }}">{{ $Admins->count() }}</h1>
                                    </div>
                                    <span class="float-right my-auto mr-auto stat-icon">
                                        <i class="fa fa-users fa-2x text-white"></i>
                                       
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                <a class="get_details" href="@can('عرض فرع') {{route('admin.branches.index')}} @endcan">
                    <div class="card overflow-hidden sales-card bg-success tw-kpi-card tw-fade-up" style="animation-delay:0.4s;">
                        <div class="pl-3 pt-3 pr-3 pb-2 pt-0 tw-kpi-content">
                            <div>
                                <h3 class="mb-3 text-white stat-label">{{ __('main.branches') }}</h3>
                            </div>
                            <div class="pb-0 mt-0">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h1 class="tx-30 font-weight-bold mb-1 text-white stat-value" data-stat-key="branches" data-base="{{ $branches->count() }}">{{ $branches->count() }}</h1>
                                    </div>
                                    <span class="float-right my-auto mr-auto stat-icon">
                                        <i class="fa fa-code-branch fa-2x text-white"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="row row-sm">
            <div class="col-lg-8">
                <div class="card rounded-xl border border-slate-200 shadow-sm">
                    <div class="card-header pb-0">
                        <h3 class="card-title mb-2">
                            {{ __('main.general_stats') }} 
                        </h3>
                        <p class="tx-12 mb-0 text-muted">
                            {{ __('main.stats_hint') }}   
                        </p>
                    </div>
                    <div class="card-body sales-info ot-0 pb-0 pt-0">
                    <div id="chart" class="ht-150" style="min-height: 150px;">
                        <div class="progress-pie-chart" data-percent="15">
                            <div class="ppc-progress">
                                <div class="ppc-progress-fill"></div>
                            </div>
                            <div class="ppc-percents">
                                <div class="pcc-percents-wrapper">
                                    <span>%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row sales-infomation pb-0 mb-0 mx-auto wd-100p mb-3">
                        <div class="col-md-6 col">
                            <p class="mb-0 d-flex">
                                <span class="legend bg-primary brround"></span>
                                {{ __('main.sales_percentage') }}
                            </p>
                            <h3 class="mb-1 text-center">
                                {{ __('main.currency_symbol') }}
                            </h3>
                        </div>
                        <div class="col-md-6 col">
                            <p class="mb-0 d-flex">
                                <span class="legend bg-info brround"></span>
                                {{ __('main.purchase_percentage') }} 
                            </p>
                            <h3 class="mb-1 text-center">
                                {{ __('main.currency_symbol') }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card tw-quick-card mb-3">
                <div class="card-header pb-0">
                    <h3 class="card-title mb-2">العمليات السريعة</h3>
                    <p class="tx-12 mb-0 text-muted">نفّذ أكثر المهام استخدامًا بضغطة واحدة</p>
                </div>
                <div class="card-body space-y-3">
                    @can('اضافة مبيعات')
                        <a class="tw-quick-link" href="{{ route('add_sale') }}">
                            <span>{{ __('main.add_sale') }}</span>
                            <i class="fa fa-arrow-left"></i>
                        </a>
                    @endcan
                    @can('اضافة مشتريات')
                        <a class="tw-quick-link" href="{{ route('add_purchase') }}">
                            <span>{{ __('main.add_purchase') }}</span>
                            <i class="fa fa-arrow-left"></i>
                        </a>
                    @endcan
                    @can('اضافة مبيعات')
                        <a class="tw-quick-link" href="{{ route('quotations.create') }}">
                            <span>{{ __('main.add') }} {{ __('main.quotation') }}</span>
                            <i class="fa fa-arrow-left"></i>
                        </a>
                    @endcan
                    @can('اضافة مبيعات')
                        <a class="tw-quick-link" href="{{ route('pos') }}">
                            <span>{{ __('main.pos') }}</span>
                            <i class="fa fa-arrow-left"></i>
                        </a>
                    @endcan
                </div>
            </div>
            <div class="card rounded-xl border border-slate-200 shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex align-items-center pb-2">
                                <p class="mb-0">
                                    {{ __('main.total_sales') }}
                                </p>
                            </div>
                            <h4 class="fw-bold mb-2">
                                 
                            </h4>
                            <div class="progress progress-style progress-sm">
                                <div class="progress-bar bg-primary-gradient wd-90p" role="progressbar"
                                     aria-valuenow="90" aria-valuemin="0" aria-valuemax="90"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card rounded-xl border border-slate-200 shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mt-4 mt-md-0">
                            <div class="d-flex align-items-center pb-2">
                                <p class="mb-0">
                                    {{ __('main.total_purchases') }}
                                </p>
                            </div>
                            <h4 class="fw-bold mb-2">
                                
                            </h4>
                            <div class="progress progress-style progress-sm">
                                <div class="progress-bar bg-danger-gradient wd-75" role="progressbar" aria-valuenow="25"
                                     aria-valuemin="0" aria-valuemax="25"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card rounded-xl border border-slate-200 shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mt-4 mt-md-0">
                            <div class="d-flex align-items-center pb-2">
                                <p class="mb-0">
                                    {{ __('main.total_returns') }}
                                </p>
                            </div>
                            <h4 class="fw-bold mb-2">
                                 
                            </h4>
                            <div class="progress progress-style progress-sm">
                                <div class="progress-bar bg-success w-50" role="progressbar" aria-valuenow="50"
                                     aria-valuemin="0" aria-valuemax="50"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<div class="row mb-4">
        <div class="col-xl-8 col-lg-12">
            <div class="card rounded-xl border border-slate-200 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">{{ __('main.sales_purchase_chart_title') }}</h5>
                    <div style="height:300px;">
                        <canvas id="salesPurchaseChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-12">
            <div class="card rounded-xl border border-slate-200 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">{{ __('main.client_supplier_chart_title') }}</h5>
                    <div style="height:260px;">
                        <canvas id="clientSupplierChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = {!! json_encode($chartLabels ?? [
        __('main.today'),
        __('main.today_minus_1'),
        __('main.today_minus_2'),
        __('main.today_minus_3'),
        __('main.today_minus_4'),
        __('main.today_minus_5'),
        __('main.today_minus_6'),
    ]) !!};
    const salesData = {!! json_encode($salesChart ?? [0,0,0,0,0,0,0]) !!};
    const purchaseData = {!! json_encode($purchaseChart ?? [0,0,0,0,0,0,0]) !!};
    const clientsCount = {{ $clients->count() ?? 0 }};
    const suppliersCount = {{ $suppliers->count() ?? 0 }};
    const statNodes = document.querySelectorAll('.stat-value[data-stat-key]');

    const baseStats = {};
    statNodes.forEach((node) => {
        const key = node.dataset.statKey;
        if (!key) {
            return;
        }
        const baseValue = Number(node.dataset.base || node.textContent || 0);
        baseStats[key] = Number.isFinite(baseValue) ? baseValue : 0;
    });

    const sumArray = (arr) => (Array.isArray(arr) ? arr.reduce((acc, val) => acc + Number(val || 0), 0) : 0);
    const weekSales = sumArray(salesData) || (baseStats.sales || 0) * 7;
    const weekPurchases = sumArray(purchaseData) || (baseStats.purchases || 0) * 7;
    const weekSalesReturn = (baseStats.sales_return || 0) * 7;
    const weekPurchaseReturn = (baseStats.purchase_return || 0) * 7;

    const monthSales = weekSales * 4;
    const monthPurchases = weekPurchases * 4;
    const monthSalesReturn = weekSalesReturn * 4;
    const monthPurchaseReturn = weekPurchaseReturn * 4;

    const rangeStats = {
        day: {
            sales: baseStats.sales || 0,
            sales_return: baseStats.sales_return || 0,
            purchases: baseStats.purchases || 0,
            purchase_return: baseStats.purchase_return || 0,
        },
        week: {
            sales: weekSales,
            sales_return: weekSalesReturn,
            purchases: weekPurchases,
            purchase_return: weekPurchaseReturn,
        },
        month: {
            sales: monthSales,
            sales_return: monthSalesReturn,
            purchases: monthPurchases,
            purchase_return: monthPurchaseReturn,
        }
    };

    const formatNumber = (value) => {
        try {
            return new Intl.NumberFormat('ar-SA').format(Math.round(value));
        } catch (e) {
            return Math.round(value).toString();
        }
    };

    const animateValue = (node, value) => {
        const start = Number(node.textContent.replace(/[^\d.-]/g, '')) || 0;
        const end = Number(value) || 0;
        const duration = 600;
        const startTime = performance.now();

        const tick = (now) => {
            const progress = Math.min((now - startTime) / duration, 1);
            const current = start + (end - start) * progress;
            node.textContent = formatNumber(current);
            if (progress < 1) {
                requestAnimationFrame(tick);
            }
        };
        requestAnimationFrame(tick);
    };

    const splitTotal = (total, parts) => {
        if (!parts || parts <= 1) {
            return [total];
        }
        if (!total) {
            return Array(parts).fill(0);
        }
        const weights = [0.9, 1.05, 0.95, 1.1].slice(0, parts);
        const weightSum = weights.reduce((acc, w) => acc + w, 0);
        return weights.map((w) => Math.round((total * w) / weightSum));
    };

    const salesCanvas = document.getElementById('salesPurchaseChart');
    let salesPurchaseChart = null;
    if(salesCanvas){
        const ctx = salesCanvas.getContext('2d');
        salesPurchaseChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {label: '{{ __('main.sales') }}', data: salesData, backgroundColor: 'rgba(40,167,69,0.5)', borderColor: '#28a745'},
                    {label: '{{ __('main.purchases') }}', data: purchaseData, backgroundColor: 'rgba(0,123,255,0.4)', borderColor: '#007bff'},
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {beginAtZero: true}
                }
            }
        });
    }

    const doughnutCanvas = document.getElementById('clientSupplierChart');
    let clientSupplierChart = null;
    if(doughnutCanvas){
        const ctx2 = doughnutCanvas.getContext('2d');
        clientSupplierChart = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['{{ __('main.clients') }}','{{ __('main.suppliers') }}'],
                datasets: [{
                    data: [clientsCount, suppliersCount],
                    backgroundColor: ['#17a2b8','#ffc107']
                }]
            },
            options: {responsive: true, maintainAspectRatio: false}
        });
    }

    const dashboardRange = document.getElementById('dashboardRange');
    const updatedAt = document.getElementById('dashboardUpdatedAt');
    const rangeButtons = dashboardRange ? dashboardRange.querySelectorAll('[data-range]') : [];
    const weekLabel = 'الأسبوع';

    const chartRanges = {
        day: {
            labels: [labels[0] || '{{ __('main.today') }}'],
            sales: [salesData[0] || 0],
            purchases: [purchaseData[0] || 0],
        },
        week: {
            labels: labels,
            sales: salesData,
            purchases: purchaseData,
        },
        month: {
            labels: [`${weekLabel} 1`, `${weekLabel} 2`, `${weekLabel} 3`, `${weekLabel} 4`],
            sales: splitTotal(weekSales, 4),
            purchases: splitTotal(weekPurchases, 4),
        }
    };

    const updateCharts = (rangeKey) => {
        if(!salesPurchaseChart){
            return;
        }
        const range = chartRanges[rangeKey] || chartRanges.day;
        salesPurchaseChart.data.labels = range.labels;
        salesPurchaseChart.data.datasets[0].data = range.sales;
        salesPurchaseChart.data.datasets[1].data = range.purchases;
        salesPurchaseChart.update();
    };

    const updateStats = (rangeKey) => {
        const stats = rangeStats[rangeKey] || rangeStats.day;
        statNodes.forEach((node) => {
            const key = node.dataset.statKey;
            const value = stats[key] !== undefined ? stats[key] : baseStats[key] || 0;
            animateValue(node, value);
        });
    };

    const setActiveRange = (rangeKey) => {
        rangeButtons.forEach((btn) => {
            btn.classList.toggle('is-active', btn.dataset.range === rangeKey);
        });
        if(updatedAt){
            const now = new Date();
            const time = now.toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' });
            updatedAt.textContent = `تم التحديث ${time}`;
        }
    };

    rangeButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const range = btn.dataset.range || 'day';
            setActiveRange(range);
            updateStats(range);
            updateCharts(range);
        });
    });

    setActiveRange('day');
    updateStats('day');
    updateCharts('day');
</script>
@endsection
 
