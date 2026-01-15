<!DOCTYPE html>
@php $isRtl = app()->getLocale() === 'ar'; @endphp
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="{{asset('assets/img/ficon.png')}}" type="image/png">
    <meta name="Keywords" content=""/>
    @include('admin.layouts.head')

    <style type="text/css" media="print">
        @media print {
            .app-content, .content {
                margin-right: 0 !important;
            }

            body {
                -webkit-print-color-adjust: exact;
                -moz-print-color-adjust: exact;
                print-color-adjust: exact;
                -o-print-color-adjust: exact;
            }

            .no-print {
                display: none;
            }

            .printy {
                display: block !important;
            }
        }
    </style>
    <style>
        /* استخدام خط النظام الافتراضي لتجنب أخطاء الخط المفقود */
        label {
            font-size: 14px !important;
        }

        table {
            font-size: 14px !important;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: "Cairo", "Inter", system-ui, -apple-system, "Segoe UI", sans-serif !important;
        }

        body, html {
            font-family: "Cairo", "Inter", system-ui, -apple-system, "Segoe UI", sans-serif !important;
            font-size: 14px !important;
            direction: {{ $isRtl ? 'rtl' : 'ltr' }};
        }

        .navigation.navigation-main {
            padding-bottom: 200px !important;
        }

        .btn-md, .badge {
            font-family: "Cairo", "Inter", system-ui, -apple-system, "Segoe UI", sans-serif !important;
            font-size: 14px !important;
        }

        .btn.dropdown-toggle.bs-placeholder, .btn.dropdown-toggle {
            height: 40px !important;
        }
        .select2-selection__rendered {
            line-height: 40px !important; border-radius: 0!important;
        }
        .select2-container .select2-selection--single {
            height: 40px !important;border-radius: 0!important;
        }
        .select2-selection__arrow {
            height: 40px !important;border-radius: 0!important;
        }
        .select2-search__field{
            height: 40px!important;
            line-height: 40px!important;
            outline: 0!important;
        }
        /* LTR/RTL adjustments */
        body.ltr .app-sidebar {
            text-align: left;
        }
        body.ltr .side-menu__item,
        body.ltr .side-menu__label {
            text-align: left;
        }
        body.ltr .side-menu__item .angle {
            float: right;
        }
        body.ltr .main-header,
        body.ltr .main-content {
            direction: ltr;
        }
        body.rtl .main-header,
        body.rtl .main-content {
            direction: rtl;
        }
        .main-header-notification{
            position: relative;
        }
        .main-header-notification .nav-link{
            padding-inline: 0 15px;
            position: relative;
        }
        .main-header-notification .dropdown-menu{
            margin-top:0;
            min-width:320px;
            border:0;
            transform:none !important;
            top: calc(100% + 12px) !important;
        }
        .main-header-notification .notification-caret{
            position:absolute;
            top:-12px;
            width:0;
            height:0;
            border-style:solid;
            border-width:10px 8px 0 8px;
            border-color:#fff transparent transparent transparent;
        }
        body.rtl .main-header-notification .dropdown-menu{
            left:0 !important;
            right:auto !important;
        }
        body.rtl .main-header-notification .notification-caret{
            left:20px;
            right:auto;
        }
        body.ltr .main-header-notification .dropdown-menu{
            right:0 !important;
            left:auto !important;
        }
        body.ltr .main-header-notification .notification-caret{
            right:20px;
            left:auto;
        }
        body.rtl .main-header-notification .nav-link .nav-link-badge{
            left:-6px;
            right:auto;
        }
        body.ltr .main-header-notification .nav-link .nav-link-badge{
            right:-6px;
            left:auto;
        }
        .main-header-notification .nav-link .nav-link-badge{
            position:absolute;
            top:-6px;
        }
        .side-menu__icon {
            font-size: 14px !important;
        }
        ::-webkit-scrollbar {
            width: 10px;
        }

        /* Track */
        ::-webkit-scrollbar-track {
            background: #fff!important;
            border-radius: 5px;
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: #444!important;
            border-radius: 5px;
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
            background: #444!important;
        }
        body.rtl table.display.w-100.text-nowrap.table-bordered.dataTable.dtr-inline {
            direction: rtl;
            text-align:center;
        }
        body.ltr table.display.w-100.text-nowrap.table-bordered.dataTable.dtr-inline {
            direction: ltr;
            text-align:center;
        }
        body.rtl{
            direction: rtl; 
        }
        body.ltr{
            direction: ltr;
        }
        table#example1 tr td {
            padding: 5px !important;
            font-size:14px !important;
        }
        .btn-secondary {
            background-color: #0d6efd !important;
            border-color: #0d6efd; 
            border: 1px solid; 
        }
        .dt-buttons.btn-group.flex-wrap {
            margin-right: 3%;
            position: relative;
        }
        .input-group.wide-tip {
            border: 2px solid #ecf0fa;
            padding: 1%;
            border-radius: 10px;
            background: #ecf0fa;
        }
    </style>
</head> 
<body class="main-body app sidebar-mini tw-modern {{ $isRtl ? 'rtl' : 'ltr' }}">
@include('admin.layouts.main-sidebar')
<!-- main-content -->
<div class="main-content app-content">
@include('admin.layouts.main-header')
<!-- container -->
    <div class="container-fluid">
        @yield('page-header')
        @yield('content')
    </div>
</div>
@include('admin.layouts.footer')
@include('admin.layouts.footer-scripts')
</body>
</html>
