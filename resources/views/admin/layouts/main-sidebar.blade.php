<!-- main-sidebar -->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
@php
    $user = Auth::user();
    $isRtl = app()->getLocale() === 'ar';
    $subscriberId = optional($user)->subscriber_id;
    $isSubscriberContext = (bool) $subscriberId;
    $isSystemOwner = $user && ! $subscriberId && $user->hasRole('system_owner');
@endphp
<aside class="app-sidebar sidebar-scroll" style="{{ $isRtl ? '' : 'direction:ltr;text-align:left;' }}">
    <style type="text/css">
        ::-webkit-scrollbar {width: 7px !important;}
        ::-webkit-scrollbar-track {background: #eee !important;}
        ::-webkit-scrollbar-thumb {background: #1989da !important;}
	    ::-webkit-scrollbar {width: 7px !important;}
        ::-webkit-scrollbar-track {background: #eee !important;}
        ::-webkit-scrollbar-thumb {background: #1989da !important;}
	    .main-sidemenu{margin-top:10px !important; height:98% !important;} 
	    .side-menu__label{color:#666;font-size:13px;font-weight:600;padding-top:5%;}
        .main-header {height: 50px !important;}
        .main-profile-menu.show .dropdown-menu {top: 50px !important;}
    </style> 
    <div class="main-sidemenu" style="overflow: auto!important;" id="right">
        <div class="app-sidebar__user clearfix">
            <div class="dropdown user-pro-body">
                <a href="{{route('admin.home')}}">
                    <div class="">
                        <img alt="user-img" class="avatar avatar-xl brround"
                             src="{{URL::asset('assets/img/logo.png')}}"> 
                    </div>
                    <div class="user-info">
                        <h4  class="mt-3 pt-0 pb-0 pr-4 pl-4 mb-0">
                            @php
                                echo env('APP_NAME');
                            @endphp 
                        </h4> 
                        <br>
                        <h5>
                            @if(!empty(Auth::user()->branch_id) && optional(Auth::user()->branch)->branch_name)
                                [ {{ optional(Auth::user()->branch)->branch_name }} ]
                            @else
                                [ {{ __('main.all_branches') }} ]
                            @endif
                        <h5>
                    </div>
                </a>

            </div>
        </div>
		<hr>
        <ul class="side-menu" style="padding-bottom: 50px !important;" id="main-menu-navigation"
            data-menu="menu-navigation">
            <li class="slide {{ Request::is('home*') ? 'active' : '' }}">
                <a class="side-menu__item" href="{{ url('/admin/' . $page='home') }}"> 
                    <i class="fa fa-home side-menu__icon"></i>
                    <span class="side-menu__label">{{ __('main.dashboard') }}</span>
                </a>
            </li>  
            <li class="slide {{ Request::is('admin/alerts*') ? 'active' : '' }}">
                <a class="side-menu__item" href="{{ route('alerts.index') }}">
                    <i class="fa fa-bell side-menu__icon"></i>
                    <span class="side-menu__label">{{ __('main.alerts_center') }}</span>
                </a>
            </li>
            @if($isSystemOwner)
            <li class="slide {{ Request::is('admin/owner/subscribers*') ? 'active' : '' }}">
                <a class="side-menu__item" href="{{ route('owner.subscribers.index') }}">
                    <i class="fa fa-building-user side-menu__icon"></i>
                    <span class="side-menu__label">{{ __('main.subscribers_panel') }}</span>
                </a>
                </li>
            @endif
            @if($isSubscriberContext)
            @can(['عرض صنف','اضافة صنف'])                
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#">
                        <i class="fas fa-barcode side-menu__icon"></i>
                        <span class="side-menu__label">{{__('main.products')}}</span><i class="angle fe fe-chevron-down"></i>
                    </a> 
                    <ul class="slide-menu">  
                        <li>
                            <a class="slide-item" href="{{route('products')}}">
                            {{__('main.products_list')}}
                            </a>
                        </li>  
                        <!--
                        <li><a class="slide-item" href="{{route('createProduct')}}">{{__('main.add_product')}}</a></li> 
                        @can('تعديل كمية','اضافة كمية') 
                        <li><a class="slide-item" href="{{route('update_qnt')}}">{{__('main.update_qnt')}}</a></li>
                        @endcan
                        @can('تعديل كمية') 
                        <li><a class="slide-item" href="{{route('add_update_qnt')}}">{{__('main.add_update_qnt')}}</a></li>
                        @endcan
                        -->
                        <li><a class="slide-item" href="{{route('print_barcode')}}">{{__('main.print_barcode')}}</a></li>
                        <li><a class="slide-item" href="{{route('print_qr')}}">{{__('main.print_qr')}}</a></li>
                    </ul>
                </li>  
            @endcan 
            <li class="slide {{ Request::is('admin/promotions*') ? 'active' : '' }}">
                <a class="side-menu__item" href="{{ route('promotions.index') }}">
                    <i class="fa fa-gift side-menu__icon"></i>
                    <span class="side-menu__label">{{ __('main.promotions') }}</span>
                </a>
            </li>
            <li class="slide {{ Request::is('admin/transfers*') ? 'active' : '' }}">
                <a class="side-menu__item" href="{{ route('transfers.index') }}">
                    <i class="fa fa-exchange-alt side-menu__icon"></i>
                    <span class="side-menu__label">{{ __('main.transfer_requests') }}</span>
                </a>
            </li>
            <li class="slide {{ Request::is('admin/stock_counts*') ? 'active' : '' }}">
                <a class="side-menu__item" href="{{ route('stock_counts.index') }}">
                    <i class="fa fa-clipboard-list side-menu__icon"></i>
                    <span class="side-menu__label">{{ __('main.inventory') }}</span>
                </a>
            </li>
            @can('عرض مبيعات')                  
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#"> 
                        <i class="fa fa-money-bill-1 side-menu__icon"></i>
                        <span class="side-menu__label">
                        {{__('main.sales')}}
                        </span>
                        <i class="angle fe fe-chevron-down"></i>
                    </a> 
                    <ul class="slide-menu">   
                        @can('اضافة مبيعات') 
                        <li><a class="slide-item" href="{{route('add_sale')}}">{{__('main.add_sale')}}</a></li>
                        <li><a class="slide-item" href="{{route('pos')}}">{{__('main.pos')}}</a></li> 
                        @endcan 
                        <li><a class="slide-item" href="{{route('sales')}}">{{__('main.sales_bill')}}</a></li>
                        <li><a class="slide-item" href="{{route('sales.return')}}">{{__('main.sales.return')}}</a></li>
                    </ul>
                </li>  
            @endcan  
            @can('عرض مشتريات')                  
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#"> 
                        <i class="fas fa-cart-shopping side-menu__icon"></i>
                        <span class="side-menu__label">
                            {{__('main.purchases')}}
                        </span>
                        <i class="angle fe fe-chevron-down"></i>
                    </a> 
                    <ul class="slide-menu">  
                        @can('اضافة مشتريات') 
                        <li><a class="slide-item" href="{{route('add_purchase')}}">{{__('main.add_purchase')}}</a></li>
                        @endcan  
                        <li><a class="slide-item" href="{{route('purchases')}}">{{__('main.purchases')}}</a></li>
                        <li><a class="slide-item" href="{{route('purchase.return')}}">{{__('main.purchase.return')}}</a></li>
                    </ul>
                </li>  
            @endcan   
            @can('عرض مورد') 
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#">
                        <i class="fas fa-user-plus side-menu__icon"></i>
                        <span class="side-menu__label">
                        {{__('main.supplier')}}
                    </span><i class="angle fe fe-chevron-down"></i>
                    </a> 
                    <ul class="slide-menu">  
                        <li>
                            <a class="slide-item" href="{{route('clients' , 4)}}">
                            {{__('main.supplier')}}
                            </a>
                        </li>  
                    </ul>
                </li>
            @endcan  
            @can('عرض عميل')                  
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#"> 
                        <i class="fas fa-user-check side-menu__icon"></i>
                        <span class="side-menu__label">
                        {{__('main.clients')}} 
                    </span><i class="angle fe fe-chevron-down"></i>
                    </a> 
                    <ul class="slide-menu">  
                        <li>
                            <a class="slide-item" href="{{route('clients' , 3)}}">
                            {{__('main.clients')}}
                            </a>
                        </li>  
                    </ul>
                </li>  
            @endcan  
            @can(['اضافة سند صرف','عرض سند صرف'])  
                
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#">
                        <i class="fa fa-money-bills side-menu__icon"></i>
                        <span class="side-menu__label">
                        {{__('main.expenses')}}
                    </span><i class="angle fe fe-chevron-down"></i>
                    </a> 
                    <ul class="slide-menu">  
                        <li>
                            <a class="slide-item" href="{{route('expenses')}}">
                            {{__('main.expenses_list')}}
                            </a>
                        </li>                     
                    </ul>
                </li>  
            @endcan  
            @can(['اضافة سند قبض','عرض سند قبض'])     
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#">
                        <i class="fa fa-money-bills side-menu__icon"></i>
                        <span class="side-menu__label">
                        {{__('main.catches')}}
                    </span><i class="angle fe fe-chevron-down"></i>
                    </a> 
                    <ul class="slide-menu">  
                        <li>
                            <a class="slide-item" href="{{route('catches')}}">
                            {{__('main.catches_list')}}
                            </a>
                        </li>                       
                    </ul>
                </li> 
            @endcan  
            @can(['عرض سند قبض','عرض سند صرف'])     
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#">
                        <i class="fa fa-money-bills side-menu__icon"></i>
                        <span class="side-menu__label">
                        {{__('main.money')}}
                    </span><i class="angle fe fe-chevron-down"></i>
                    </a> 
                    <ul class="slide-menu">  
                        <li>
                            <a class="slide-item" href="{{route('money.entry.list')}}">
                            {{__('main.money.input')}}
                            </a>
                        </li>  
                        <li>
                            <a class="slide-item" href="{{route('money.exit.list')}}">
                            {{__('main.money.output')}}
                            </a>
                        </li>                      
                    </ul>
                </li> 
            @endcan 
             
            @can(['اضافة جرد','عرض جرد'])              
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#">
                        <i class="fa fa-newspaper side-menu__icon"></i>
                        <span class="side-menu__label">{{ __('main.inventory_menu') }}</span><i class="angle fe fe-chevron-down"></i>
                    </a> 
                    <ul class="slide-menu">    
                        <li>
                            <a class="slide-item" href="{{route('admin.inventory.create')}}">
                                {{ __('main.inventory_new') }}
                            </a>
                        </li>  
                        <li>
                            <a class="slide-item" href="{{route('admin.inventory.index')}}">
                               {{ __('main.inventory_list') }}
                            </a>
                        </li>  
                        <li>
                            <a class="slide-item" href="{{ route('admin.manufacturing.index') }}">
                                {{ __('تصنيع وتجميع الأصناف') }}
                            </a>
                        </li>
                                                              
                    </ul>
                </li> 
            @endcan
            @can('عرض حسابات')                       
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#">   
                        <i class="fa fa-circle-dollar-to-slot side-menu__icon"></i>
                        <span class="side-menu__label">
                            {{__('main.accounting')}}
                        </span>
                        <i class="angle fe fe-chevron-down"></i>
                    </a> 
                    <ul class="slide-menu">    
                        <li>
                            <a class="slide-item" href="{{route('accounts_list')}}">
                             {{__('main.accounts')}}
                            </a>
                        </li>   
                        <li><a class="slide-item" href="{{route('account_settings_list')}}">{{__('main.account_settings')}}</a></li>
                        <li><a class="slide-item" href="{{route('journals', 1)}}">{{__('main.journals')}}</a></li>
                        <li><a class="slide-item" href="{{route('manual_journal')}}">{{__('main.add_manual_journal')}}</a></li> 
                        <li><a class="slide-item" href="{{route('fiscal_years.index')}}">{{__('main.fiscal_years')}}</a></li>
                    </ul>
                </li>  
            @endcan  
            @can('التقارير المحاسبية')                  
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#"> 
                        <i class="fa fa-file-invoice-dollar side-menu__icon"></i>
                        <span class="side-menu__label">{{ __('main.accounting_reports') }}</span><i class="angle fe fe-chevron-down"></i>
                    </a> 
                    <ul class="slide-menu">   
                        <li>
                            <a class="slide-item" href="{{route('account_balance')}}">
                            {{__('main.balance_report')}}
                            </a>
                        </li> 
                        <li>
                            <a class="slide-item" href="{{route('incoming_list')}}">
                            {{__('main.incoming_list')}}
                            </a>
                        </li> 
                        <li>
                            <a class="slide-item" href="{{route('balance_sheet')}}">
                            {{__('main.balance_sheet')}}
                            </a>
                        </li> 
                        <li>
                            <a class="slide-item" href="{{route('account_movement_report')}}">
                            {{__('main.account_movement_report')}}
                            </a>
                        </li> 
                        <li>
                            <a class="slide-item" href="{{route('tax.declaration')}}">
                                {{ __('main.tax_declaration') }}
                            </a>
                        </li>             
                    </ul>
                </li>     
            @endcan  
            @can('التقارير المخزون')                  
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#">
                        <i class="fa fa-copy side-menu__icon"></i>
                        <span class="side-menu__label">
                        {{__('main.reports')}}
                        </span>
                        <i class="angle fe fe-chevron-down"></i>
                    </a> 
                    <ul class="slide-menu">   
                        <li><a class="slide-item" href="{{route('daily_sales_report')}}">{{__('main.daily_sales_report')}}</a></li>
                        <li><a class="slide-item" href="{{route('sales_item_report')}}">{{__('main.sales_report')}}</a></li>
                        <li><a class="slide-item" href="{{route('purchase_report')}}">{{__('main.purchases_report')}}</a></li>
                        <li><a class="slide-item" href="{{route('purchases_return_report')}}">{{__('main.purchases_return_report')}}</a></li>
                        <li><a class="slide-item" href="{{route('sales.return.report')}}">{{__('main.sales_return_report')}}</a></li>
                        <li><a class="slide-item" href="{{route('items_report')}}">{{__('main.items_report')}}</a></li>
                        <li><a class="slide-item" href="{{route('items_limit_report')}}">{{__('main.under_limit_items_report')}}</a></li>
                        <li><a class="slide-item" href="{{route('items_no_balance_report')}}">{{__('main.no_balance_items_report')}}</a></li>
                        <li><a class="slide-item" href="{{route('items_stock_report')}}">{{__('main.users_transactions_report')}}</a></li>
                        <li><a class="slide-item" href="{{route('items_purchased_report')}}">{{__('main.imported_items_reports')}}</a></li>                     
                    </ul>
                </li>     
            @endcan   
            @can('عرض ترميز')
                @php
                    $basicDataActive = Request::is('*admin/units*') ||
                        Request::is('*admin/categories*') ||
                        Request::is('*admin/brands*') ||
                        Request::is('*admin/currency*') ||
                        Request::is('*admin/taxRates*') ||
                        Request::is('*admin/clientGroups*');
                @endphp
                <li class="slide {{ $basicDataActive ? 'is-expanded active' : '' }}">
                    <a class="side-menu__item" data-toggle="slide" href="#">
                        <i class="fas fa-fw fa-sliders side-menu__icon"></i> 
                        <span class="side-menu__label">
                        {{__('main.basic_date')}}
                    </span><i class="angle fe fe-chevron-down"></i>
                    </a> 
                    <ul class="slide-menu">  
                        <li class="{{ Request::is('*admin/units*') ? 'active' : '' }}">
                            <a class="slide-item" href="{{route('units')}}">
                            {{__('main.units')}}
                            </a>
                        </li> 
                        <li class="{{ Request::is('*admin/categories*') ? 'active' : '' }}">
                            <a class="slide-item" href="{{route('categories')}}">
                            {{__('main.categories')}}
                            </a>
                        </li> 
                        <li class="{{ Request::is('*admin/brands*') ? 'active' : '' }}">
                            <a class="slide-item" href="{{route('brands')}}">
                            {{__('main.brands')}}
                            </a>
                        </li> 
                        <li class="{{ Request::is('*admin/currency*') ? 'active' : '' }}">
                            <a class="slide-item" href="{{route('currency')}}">
                            {{__('main.currencies')}}
                            </a>
                        </li>  
                        <li class="{{ Request::is('*admin/taxRates*') ? 'active' : '' }}">
                            <a class="slide-item" href="{{route('taxRates')}}">
                            {{__('main.tax')}}
                            </a>
                        </li> 
                        <li class="{{ Request::is('*admin/clientGroups*') ? 'active' : '' }}">
                            <a class="slide-item" href="{{route('clientGroups')}}">
                            {{__('main.c_groups')}}
                            </a>
                        </li>   
                    </ul>
                     
                </li> 
            @endcan
            @can('عرض الاعدادات')                      
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#">
                        <i class="fas fa-fw fa-gear side-menu__icon"></i> 
                        <span class="side-menu__label">
                            {{__('main.setting')}}
                        </span>
                        <i class="angle fe fe-chevron-down"></i>
                    </a> 
                    <ul class="slide-menu">  
                        <li><a class="slide-item" href="{{route('companyInfo')}}">{{__('main.companyInfo')}}</a></li>
                        <li><a class="slide-item" href="{{route('system_settings')}}">{{__('main.system_settings')}}</a></li>
                        <li><a class="slide-item" href="{{route('pos_settings')}}">{{__('main.pos_settings')}}</a></li>
                    </ul>
                </li>  
            @endcan   
            @can(['اضافة فرع','عرض فرع']) 
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#">
                        <i class="fa fa-code-branch side-menu__icon"></i>
                        <span class="side-menu__label">{{ __('main.branches') }}</span><i class="angle fe fe-chevron-down"></i>
                    </a>
                    <ul class="slide-menu">
                        @can('اضافة فرع')
                            <li>
                                <a class="slide-item" href="{{ route('admin.branches.create') }}">
                                    {{ __('main.branch_add') }}
                                </a>
                            </li>
                        @endcan
                        @can('عرض فرع')
                            <li>
                                <a class="slide-item" href="{{ route('admin.branches.index') }}">
                                    {{ __('main.branches_list') }}
                                </a>
                            </li>
                        @endcan
                        <li>
                            <a class="slide-item" href="{{route('warehouses')}}">
                            {{__('main.warehouses')}}
                            </a>
                        </li> 
                    </ul>
                </li>
            @endcan                                                                                  
            @can(['عرض صلاحية','عرض مستخدم','اضافة صلاحية','اضافة مستخدم']) 
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="#">
                        <i class="fa fa-users-gear side-menu__icon"></i>
                        <span class="side-menu__label">{{ __('main.roles_users') }}</span><i class="angle fe fe-chevron-down"></i>
                    </a>
                    <ul class="slide-menu">
                        @can('اضافة صلاحية')
                            <li>
                                <a class="slide-item" href="{{ route('admin.roles.create') }}">
                                    {{ __('main.role_add') }}
                                </a>
                            </li>
                        @endcan
                        @can('عرض صلاحية')
                            <li>
                                <a class="slide-item" href="{{ route('admin.roles.index') }}">
                                    {{ __('main.roles_list') }}
                                </a>
                            </li>
                        @endcan
                        @can('اضافة مستخدم')
                            <li>
                                <a class="slide-item" href="{{ route('admin.admins.create') }}">
                                    {{ __('main.user_add') }}
                                </a>
                            </li>
                        @endcan
                        @can('عرض مستخدم')
                            <li>
                                <a class="slide-item" href="{{ route('admin.admins.index') }}">
                                    {{ __('main.users_list') }}
                                </a>
                            </li>
                        @endcan

                    </ul>
                </li>
            @endcan
            @endif
        </ul>
    </div>
</aside>
<!-- main-sidebar -->
