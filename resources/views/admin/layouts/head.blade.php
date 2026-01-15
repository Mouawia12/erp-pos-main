<!-- Title -->
<title>@yield('title')</title> 
<!-- Favicon -->
<link rel="icon" href="{{URL::asset('assets/img/logo.png')}}" type="image/x-icon"/>
<!-- Icons css -->
<link href="{{URL::asset('assets/css/icons.css')}}" rel="stylesheet">
<!--  Custom Scroll bar-->
<link href="{{URL::asset('assets/plugins/mscrollbar/jquery.mCustomScrollbar.css')}}" rel="stylesheet"/>
<!--  Sidebar css -->
<link href="{{URL::asset('assets/plugins/sidebar/sidebar.css')}}" rel="stylesheet">
<!-- Sidemenu css -->
@php $isRtl = app()->getLocale() === 'ar'; @endphp
@if($isRtl)
<link rel="stylesheet" href="{{URL::asset('assets/css-rtl/sidemenu.css')}}">
@else
<link rel="stylesheet" href="{{URL::asset('assets/css/sidemenu.css')}}">
@endif
<!-- datatables css -->
<link href="{{asset('assets/plugins/select2/css/select2.min.css')}}"  rel="stylesheet" > 
@yield('css')
<!--- Style css -->
@if($isRtl)
<link href="{{URL::asset('assets/css-rtl/style.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/css-rtl/style-dark.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/css-rtl/skin-modes.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('assets/css-rtl/progress-chart.css')}}">
@else
<link href="{{URL::asset('assets/css/style.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/css/style-dark.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/css/skin-modes.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('assets/css-rtl/progress-chart.css')}}">
@endif
<link href="{{asset('assets/plugins/fontawesome-free/css/all.min.css')}}" rel="stylesheet" />
@vite(['resources/css/app.css', 'resources/js/app.js'])
<!-- حذف أي @font-face مخصص لتجنب أخطاء الخط -->
