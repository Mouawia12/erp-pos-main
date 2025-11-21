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

    <div class="breadcrumb-header justify-content-center"> 
        <div> 
            <h3 class="text-center">
                [  {{\Carbon\Carbon::now() -> format('d - m - Y')}} / {{__('main.remaining_days')}} : {{$remaining_days}}  ]
            </h3>  
        </div> 
    </div>
    <!-- /breadcrumb -->
@endsection
@section('content')
    @if(session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif 
    <!-- row closed -->
 
    <div class="row mt-3 mb-3"> 
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
            <a href="@can('عرض مبيعات'){{route('sales')}}@endcan">
                <a class="get_details" 
                   href="@can('عرض مبيعات'){{route('sales')}}@endcan">
                    <div class="card overflow-hidden sales-card bg-success-gradient"> 
                        <div class="pl-3 pt-3 pr-3 pb-2 pt-0"> 
                            <div class="">
                                <h3 class="mb-3 text-white">مبيعات اليوم</h3>
                            </div>
                            <div class="pb-0 mt-0">
                                <div class="d-flex">
                                    <div class="">
                                        <h1 class="tx-30 font-weight-bold mb-1 text-white">{{$sales}}</h1>
                                    </div>
                                    <span class="float-right my-auto mr-auto"> 
                                        <i class="fa fa-money-bill fa-2x text-white"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a> 
            </a>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                <a href="@can('عرض مردود مبيعات'){{route('sales.return')}}@endcan">
                    <a class="get_details" 
                       href="@can('عرض مردود مبيعات'){{route('sales.return')}}@endcan">
                        <div class="card overflow-hidden sales-card bg-warning-gradient"> 
                            <div class="pl-3 pt-3 pr-3 pb-2 pt-0"> 
                                <div class="">
                                    <h3 class="mb-3 text-white">مرتجع مبيعات اليوم</h3>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h1 class="tx-30 font-weight-bold mb-1 text-white">{{$sales_return * -1}}</h1>
                                        </div>
                                        <span class="float-right my-auto mr-auto">  
                                            <i class="fa-solid fa-money-bill-transfer  fa-2x text-white"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a> 
                </a>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                <a  href="@can('عرض مشتريات'){{route('purchases')}} @endcan">
                    <a class="get_details" 
                    href=" @can('عرض مشتريات'){{route('purchases')}} @endcan">
                        <div class="card overflow-hidden sales-card bg-info-gradient">

                            <div class="pl-3 pt-3 pr-3 pb-2 pt-0">

                                <div class="">
                                    <h3 class="mb-3 text-white">مشتريات اليوم</h3>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h1 class="tx-30 font-weight-bold mb-1 text-white">{{$purchases}}</h1>
                                        </div>
                                        <span class="float-right my-auto mr-auto"> 
                                            <i class="fa fa-cart-shopping fa-2x text-white"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a> 
                </a>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                <a href="@can('عرض مردود مشتريات'){{route('purchase.return')}}@endcan">
                    <a class="get_details" 
                       href="@can('عرض مردود مشتريات'){{route('purchase.return')}}@endcan">
                        <div class="card overflow-hidden sales-card bg-danger-gradient"> 
                            <div class="pl-3 pt-3 pr-3 pb-2 pt-0"> 
                                <div class="">
                                    <h3 class="mb-3 text-white"> مردود مشتريات اليوم</h3>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h1 class="tx-30 font-weight-bold mb-1 text-white">{{$purchases_return * -1}}</h1>
                                        </div>
                                        <span class="float-right my-auto mr-auto">   
                                            <i class="fa fa-retweet fa-2x text-white" aria-hidden="true"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a> 
                </a>
            </div>  
            <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                <a href="@can('عرض عميل'){{route('clients' , 3)}} @endcan">
                    <a class="get_details" 
                       href="@can('عرض عميل'){{route('clients' , 3)}} @endcan">
                        <div class="card overflow-hidden sales-card bg-primary-gradient">

                            <div class="pl-3 pt-3 pr-3 pb-2 pt-0">

                                <div class="">
                                    <h3 class="mb-3 text-white">العملاء</h3>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h1 class="tx-30 font-weight-bold mb-1 text-white">{{$clients->count()}}</h1>
                                        </div>
                                        <span class="float-right my-auto mr-auto"> 
                                            <i class="fa fa-user-plus fa-2x text-white"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a> 
                </a>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                <a href="@can('عرض مورد') {{route('clients' , 4)}} @endcan ">
                    <a class="get_details" 
                       href="@can('عرض مورد') {{route('clients' , 4)}} @endcan ">
                        <div class="card overflow-hidden sales-card bg-secondary-gradient">

                            <div class="pl-3 pt-3 pr-3 pb-2 pt-0">

                                <div class="">
                                    <h3 class="mb-3 text-white">الموردين</h3>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h1 class="tx-30 font-weight-bold mb-1 text-white">{{$suppliers->count()}}</h1>
                                        </div>
                                        <span class="float-right my-auto mr-auto">  
                                            <i class="fa fa-cart-plus fa-2x text-white"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a> 
                </a>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                <a href="@can('عرض مستخدم'){{route('admin.admins.index')}} @endcan">
                    <a class="get_details" 
                       href="@can('عرض مستخدم'){{route('admin.admins.index')}}@endcan">
                        <div class="card overflow-hidden sales-card bg-dark">

                            <div class="pl-3 pt-3 pr-3 pb-2 pt-0">

                                <div class="">
                                    <h3 class="mb-3 text-white">مستخدمين</h3>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h1 class="tx-30 font-weight-bold mb-1 text-white">{{$Admins->count()}}</h1>
                                        </div>
                                        <span class="float-right my-auto mr-auto">
                                            <i class="fa fa-users fa-2x text-white"></i>
                                           
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a> 
                </a>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                <a href="@can('عرض فرع') {{route('admin.branches.index')}} @endcan">
                    <a class="get_details" 
                       href="@can('عرض فرع') {{route('admin.branches.index')}} @endcan">
                        <div class="card overflow-hidden sales-card bg-success">

                            <div class="pl-3 pt-3 pr-3 pb-2 pt-0">

                                <div class="">
                                    <h3 class="mb-3 text-white">فروع</h3>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h1 class="tx-30 font-weight-bold mb-1 text-white">{{$branches->count();}}</h1>
                                        </div>
                                        <span class="float-right my-auto mr-auto">
                                            <i class="fa fa-code-branch fa-2x text-white"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a> 
                </a>
            </div>
        </div>
        <div class="row row-sm">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header pb-0">
                        <h3 class="card-title mb-2">
                            احصائيات عامة 
                        </h3>
                        <p class="tx-12 mb-0 text-muted">
                            فى العداد التالى يوضح النسبة المئوية   
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
                                نسبة المبيعات
                            </p>
                            <h3 class="mb-1 text-center">
                                 
                                ريال
                            </h3>
                        </div>
                        <div class="col-md-6 col">
                            <p class="mb-0 d-flex">
                                <span class="legend bg-info brround"></span>
                                نسبة المشتريات 
                            </p>
                            <h3 class="mb-1 text-center">
                                
                                ريال
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card ">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex align-items-center pb-2">
                                <p class="mb-0">
                                    اجمالى المبيعات
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
            <div class="card ">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mt-4 mt-md-0">
                            <div class="d-flex align-items-center pb-2">
                                <p class="mb-0">
                                    اجمالى المشتريات
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
            <div class="card ">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mt-4 mt-md-0">
                            <div class="d-flex align-items-center pb-2">
                                <p class="mb-0">
                                    اجمالى المردودات
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
@endsection
 
