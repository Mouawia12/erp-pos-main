<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion no-print" id="accordionSidebar"
        @if(Config::get('app.locale') == 'ar') style="padding: 5px;" @endif>

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('home')}}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Gold MS</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->

    <li class="nav-item @if($slag == 1) active @endif"
        @if(Config::get('app.locale') == 'ar') style="direction: rtl" @endif>
        <a class="nav-link" href="{{route('home')}}"
           @if(Config::get('app.locale') == 'ar') style="text-align: right ; " @endif>
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>{{__('main.cpanel')}}</span></a>
    </li>



    @if(  in_array('categories' , $routes )  || in_array('karats' , $routes )  ||
       in_array('prices' ,$routes)   ||in_array('warehouses' , $routes ) )
    <!-- Divider -->
    <hr class="sidebar-divider">
    <!-- Nav Item - Pages Collapse Menu -->

    <li class="nav-item @if($slag == 2) active @endif"
        @if(Config::get('app.locale') == 'ar') style="direction: rtl" @endif>
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
           aria-expanded="true" aria-controls="collapseTwo"
           @if(Config::get('app.locale') == 'ar') style="text-align: right ; direction: rtl; " @endif>
            <i class="fas fa-fw fa-cog"></i>
            <span>{{__('main.basic_data')}}</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded @if(Config::get('app.locale') == 'ar') text-right @endif">
                @if(  in_array('categories' , $routes )  )
                <a class="collapse-item @if($subSlag == 1) active @endif"
                   href="{{route('categories')}}">{{__('main.cats')}}</a>
                @endif
                    @if( in_array('karats' , $routes ) )
                <a class="collapse-item @if($subSlag == 2) active @endif"
                   href="{{route('karats')}}">{{__('main.karat')}}</a>
                    @endif
                    @if(  in_array('prices' , $routes )   )
                <a class="collapse-item @if($subSlag == 3) active @endif"
                   href="{{route('prices')}}">{{__('main.prices')}}</a>
                    @endif
                    @if( in_array('warehouses' , $routes )  )
                <a class="collapse-item @if($subSlag == 23) active @endif"
                   href="{{route('warehouses')}}">{{__('main.warehouses')}}</a>
                    @endif

            </div>
        </div>
    </li>

    @endif
    @if( in_array('items' , $routes )   || in_array('gold_stock' , $routes )    )
    <li class="nav-item @if($slag == 3) active @endif"
        @if(Config::get('app.locale') == 'ar') style="direction: rtl" @endif>
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseThree"
           aria-expanded="true" aria-controls="collapseThree"
           @if(Config::get('app.locale') == 'ar') style="text-align: right ; direction: rtl; " @endif>
            <i class="fas fa-fw fa-cog"></i>
            <span>{{__('main.items')}}</span>
        </a>
        <div id="collapseThree" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded @if(Config::get('app.locale') == 'ar') text-right @endif">
                @if(  in_array('items' , $routes )  )
                <a class="collapse-item @if($subSlag == 4) active @endif"
                   href="{{route('items')}}">{{__('main.item_list')}}</a>
                @endif

                    @if(  in_array('items' , $routes )  )
                        <a class="collapse-item @if($subSlag == 43) active @endif"
                           href="{{route('lost_barcode')}}">{{__('main.lost_barcode')}}</a>
                    @endif

                    @if(  in_array('gold_stock' , $routes )   )
                <a class="collapse-item @if($subSlag == 13) active @endif"
                   href="{{route('gold_stock')}}">{{__('main.gold_stock')}}</a>
                    @endif

            </div>


        </div>
    </li>
    @endif



    @if( in_array('clients/4' , $routes ))
        <hr class="sidebar-divider d-none d-md-block">
    <li class="nav-item @if($slag == 4) active @endif"
        @if(Config::get('app.locale') == 'ar') style="direction: rtl" @endif>
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseFour"
           aria-expanded="true" aria-controls="collapseFour"
           @if(Config::get('app.locale') == 'ar') style="text-align: right ; direction: rtl; " @endif>
            <i class="fas fa-fw fa-cog"></i>
            <span>{{__('main.suppliers')}}</span>
        </a>
        <div id="collapseFour" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded @if(Config::get('app.locale') == 'ar') text-right @endif">
                <a class="collapse-item @if($subSlag == 7) active @endif"
                   href="{{route('clients' , 4)}}">{{__('main.suppliers')}}</a>

            </div>
        </div>
    </li>
    @endif
    @if(  in_array('clients/3' , $routes )  )
    <li class="nav-item @if($slag == 5) active @endif"
        @if(Config::get('app.locale') == 'ar') style="direction: rtl" @endif>
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseFive"
           aria-expanded="true" aria-controls="collapseFive"
           @if(Config::get('app.locale') == 'ar') style="text-align: right ; direction: rtl; " @endif>
            <i class="fas fa-fw fa-cog"></i>
            <span>{{__('main.clients')}}</span>
        </a>
        <div id="collapseFive" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded @if(Config::get('app.locale') == 'ar') text-right @endif">
                <a class="collapse-item @if($subSlag == 7) active @endif"
                   href="{{route('clients' , 3)}}">{{__('main.clients')}}</a>

            </div>
        </div>
    </li>
    @endif


    @if(  in_array('workEntryAll' , $routes )  )
        <hr class="sidebar-divider d-none d-md-block">
    <li class="nav-item @if($slag == 6) active @endif"
        @if(Config::get('app.locale') == 'ar') style="direction: rtl" @endif>
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSix"
           aria-expanded="true" aria-controls="collapseSix"
           @if(Config::get('app.locale') == 'ar') style="text-align: right ; direction: rtl; " @endif>
            <i class="fas fa-fw fa-cog"></i>
            <span>{{__('main.enter_work')}}</span>
        </a>
        <div id="collapseSix" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded @if(Config::get('app.locale') == 'ar') text-right @endif">
                <a class="collapse-item @if($subSlag == 9) active @endif"
                   href="{{route('workEntryAll')}}">{{__('main.enter_work_list')}}</a>
                <a class="collapse-item @if($subSlag == 10) active @endif "
                   href="{{route('workEntryCreate')}}">{{__('main.enter_work_create')}}</a>

            </div>
        </div>
    </li>
    @endif

    @if(env('PROGRAMME_TYPE') == 0)
    <li class="nav-item @if($slag == 8) active @endif"
        @if(Config::get('app.locale') == 'ar') style="direction: rtl" @endif>
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseEight"
           aria-expanded="true" aria-controls="collapseEight"
           @if(Config::get('app.locale') == 'ar') style="text-align: right ; direction: rtl; " @endif>
            <i class="fas fa-fw fa-cog"></i>
            <span>{{__('main.work_exit')}}</span>
        </a>
        <div id="collapseEight" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded @if(Config::get('app.locale') == 'ar') text-right @endif">
                <a class="collapse-item @if($subSlag == 16) active @endif"
                   href="{{route('workExitAll')}}">{{__('main.work_exit_list')}}</a>
                <a class="collapse-item @if($subSlag == 17) active @endif "
                   href="{{route('workExitCreate')}}">{{__('main.work_exit_create')}}</a>

            </div>
        </div>
    </li>

    <li class="nav-item @if($slag == 7) active @endif"
        @if(Config::get('app.locale') == 'ar') style="direction: rtl" @endif>
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSeven"
           aria-expanded="true" aria-controls="collapseSeven"
           @if(Config::get('app.locale') == 'ar') style="text-align: right ; direction: rtl; " @endif>
            <i class="fas fa-fw fa-cog"></i>
            <span>{{__('main.enter_old')}}</span>
        </a>
        <div id="collapseSeven" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded @if(Config::get('app.locale') == 'ar') text-right @endif">
                <a class="collapse-item @if($subSlag == 11) active @endif"
                   href="{{route('oldEntryAll')}}">{{__('main.enter_old_list')}}</a>
                <a class="collapse-item @if($subSlag == 12) active @endif "
                   href="{{route('oldEntryCreate')}}">{{__('main.enter_old_create')}}</a>

            </div>
        </div>
    </li>

    <li class="nav-item @if($slag == 9) active @endif"
        @if(Config::get('app.locale') == 'ar') style="direction: rtl" @endif>
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseNine"
           aria-expanded="true" aria-controls="collapseNine"
           @if(Config::get('app.locale') == 'ar') style="text-align: right ; direction: rtl; " @endif>
            <i class="fas fa-fw fa-cog"></i>
            <span>{{__('main.old_gold_exit')}}</span>
        </a>
        <div id="collapseNine" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded @if(Config::get('app.locale') == 'ar') text-right @endif">
                <a class="collapse-item @if($subSlag == 14) active @endif"
                   href="{{route('oldExitAll')}}">{{__('main.old_gold_exit_list')}}</a>
                <a class="collapse-item @if($subSlag == 15) active @endif "
                   href="{{route('oldExitCreate')}}">{{__('main.old_gold_exit_create')}}</a>

            </div>
        </div>
    </li>
    @endif

    @if(  in_array('money_entry_list' , $routes )  )
        <hr class="sidebar-divider d-none d-md-block">
    <li class="nav-item @if($slag == 10) active @endif"
        @if(Config::get('app.locale') == 'ar') style="direction: rtl" @endif>
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseten"
           aria-expanded="true" aria-controls="collapseten"
           @if(Config::get('app.locale') == 'ar') style="text-align: right ; direction: rtl; " @endif>
            <i class="fas fa-fw fa-cog"></i>
            <span>{{__('main.money_entry')}}</span>
        </a>
        <div id="collapseten" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded @if(Config::get('app.locale') == 'ar') text-right @endif">
                <a class="collapse-item @if($subSlag == 18) active @endif"
                   href="{{route('money_entry_list')}}">{{__('main.money_entry_list')}}</a>

            </div>
        </div>
    </li>
    @endif

    @if(  in_array('money_exit_list' , $routes )   )
    <li class="nav-item @if($slag == 11) active @endif"
        @if(Config::get('app.locale') == 'ar') style="direction: rtl" @endif>
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseelev"
           aria-expanded="true" aria-controls="collapseelev"
           @if(Config::get('app.locale') == 'ar') style="text-align: right ; direction: rtl; " @endif>
            <i class="fas fa-fw fa-cog"></i>
            <span>{{__('main.money_exit')}}</span>
        </a>
        <div id="collapseelev" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded @if(Config::get('app.locale') == 'ar') text-right @endif">
                <a class="collapse-item @if($subSlag == 19) active @endif"
                   href="{{route('money_exit_list')}}">{{__('main.money_exit_list')}}</a>

            </div>
        </div>
    </li>
    @endif



    <hr class="sidebar-divider d-none d-md-block">
    @if(env('PROGRAMME_TYPE') == 1)

        @if( in_array('pos' , $routes )    ||in_array('pos_purchase' , $routes )    || in_array('pos_sales' , $routes )    )
    <li class="nav-item @if($slag == 15) active @endif"
        @if(Config::get('app.locale') == 'ar') style="direction: rtl" @endif>
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse15"
           aria-expanded="true" aria-controls="collapse15"
           @if(Config::get('app.locale') == 'ar') style="text-align: right ; direction: rtl; " @endif>
            <i class="fas fa-fw fa-calculator"></i>
            <span>{{__('main.pos')}}</span></a>

        <div id="collapse15" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded @if(Config::get('app.locale') == 'ar') text-right @endif">
                @if(   in_array('pos' , $routes )   )
                <a class="collapse-item @if($subSlag == 151) active @endif"
                   href="{{route('pos')}}">{{__('main.pos')}}</a>
                @endif
                    @if(  in_array('pos_purchase' , $routes )   )
                <a class="collapse-item @if($subSlag == 152) active @endif"
                   href="{{route('pos_sales')}}">{{__('main.pos_sales_list')}}</a>
                    @endif
                    @if( in_array('pos_sales' , $routes )   )
                <a class="collapse-item @if($subSlag == 153) active @endif"
                   href="{{route('pos_purchase')}}">{{__('main.pos_purchase_list')}}</a>
                    @endif
                    @if(  in_array('pos_purchase' , $routes )   )
                        <a class="collapse-item @if($subSlag == 154) active @endif"
                           href="{{route('return_sales')}}">{{__('main.return_sales')}}</a>
                    @endif



            </div>
        </div>
    </li>

        @endif
    @endif



    @if( in_array('accounts' , $routes )    ||in_array('account_settings' , $routes )    || in_array('journals' , $routes )  ||  in_array('manual_journal' , $routes ) )
   <li class="nav-item @if($slag == 16) active @endif"
       @if(Config::get('app.locale') == 'ar') style="direction: rtl" @endif>
       <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse16"
          aria-expanded="true" aria-controls="collapse14"
          @if(Config::get('app.locale') == 'ar') style="text-align: right ; direction: rtl; " @endif>
           <i class="fas fa-fw fa-cog"></i>
           <span>{{__('main.accounting')}}</span>
       </a>
       <div id="collapse16" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
           <div class="bg-white py-2 collapse-inner rounded @if(Config::get('app.locale') == 'ar') text-right @endif">
               @if(  in_array('accounts' , $routes )   )
               <a class="collapse-item @if($subSlag == 161) active @endif"
                  href="{{route('accounts_list')}}">{{__('main.accounts')}}</a>
               @endif
                   @if( in_array('account_settings' , $routes )    )
               <a class="collapse-item @if($subSlag == 162) active @endif"
                  href="{{route('account_settings_list')}}">{{__('main.account_settings')}}</a>
                   @endif
                   @if( in_array('journals' , $routes )    )
               <a class="collapse-item @if($subSlag == 163) active @endif"
                  href="{{route('journals' , 1)}}">{{__('main.journals')}}</a>


                       <a class="collapse-item @if($subSlag == 165) active @endif"
                          href="{{route('journals' , 0)}}">{{__('main.manual_journals')}}</a>

                   @endif
                   @if( in_array('manual_journal' , $routes )    )
               <a class="collapse-item @if($subSlag == 164) active @endif"
                  href="{{route('manual_journal')}}">{{__('main.manual_journal')}}</a>
                   @endif

           </div>


       </div>
   </li>
    @endif

{{--    @if( in_array('users' , $routes )    ||in_array('roles' , $routes )    || in_array('roleViews' , $routes )  )--}}
   @if(false)
   <hr class="sidebar-divider d-none d-md-block">
   <li class="nav-item @if($slag == 17) active @endif"
       @if(Config::get('app.locale') == 'ar') style="direction: rtl" @endif>
       <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse17"
          aria-expanded="true" aria-controls="collapse17"
          @if(Config::get('app.locale') == 'ar') style="text-align: right ; direction: rtl; " @endif>
           <i class="fas fa-fw fa-cog"></i>
           <span>{{__('main.users_tab')}}</span>
       </a>
       <div id="collapse17" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
           <div class="bg-white py-2 collapse-inner rounded @if(Config::get('app.locale') == 'ar') text-right @endif">
               @if(  in_array('users' , $routes )  )
               <a class="collapse-item @if($subSlag == 171) active @endif"
                  href="{{route('users')}}">{{__('main.users')}}</a>
               @endif

                   @if(  in_array('roles' , $routes ) )
               <a class="collapse-item @if($subSlag == 172) active @endif"
                  href="{{route('roles')}}">{{__('main.roles')}}</a>
                   @endif

                   @if(  in_array('roleViews' , $routes ) )
               <a class="collapse-item @if($subSlag == 173) active @endif"
                  href="{{route('roleViews')}}">{{__('main.roleViews')}}</a>
                   @endif




           </div>


       </div>
   </li>
    @endif

    @if(  in_array('tax_settings' , $routes )  || in_array('companyInfo' , $routes ) )
   <hr class="sidebar-divider d-none d-md-block">


   <li class="nav-item @if($slag == 18) active @endif"
       @if(Config::get('app.locale') == 'ar') style="direction: rtl" @endif>
       <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse18"
          aria-expanded="true" aria-controls="collapse18"
          @if(Config::get('app.locale') == 'ar') style="text-align: right ; direction: rtl; " @endif>
           <i class="fas fa-fw fa-cog"></i>
           <span>{{__('main.settings')}}</span>
       </a>
       <div id="collapse18" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
           <div class="bg-white py-2 collapse-inner rounded @if(Config::get('app.locale') == 'ar') text-right @endif">
               <a class="collapse-item @if($subSlag == 181) active @endif"
                  href="{{route('tax_settings')}}">{{__('main.additional_tax')}}</a>
               <a class="collapse-item @if($subSlag == 182) active @endif"
                  href="{{route('companyInfo')}}">{{__('main.companyInfo')}}</a>

           </div>



       </div>
   </li>
    @endif

    @if(  in_array('daily_all_movements' , $routes)  || in_array('gold_stock_report' , $routes) ||   in_array('vendor_account' , $routes) ||
      in_array( 'purchase_report' , $routes)  || in_array( 'sales_report' , $routes)  || in_array('sold_items_report' , $routes) ||
        in_array('item_list_report' , $routes) || in_array('balance_sheet' , $routes ) || in_array('incoming_list' , $routes ))
   <hr class="sidebar-divider d-none d-md-block">
   <li class="nav-item @if($slag == 14) active @endif"
       @if(Config::get('app.locale') == 'ar') style="direction: rtl" @endif>
       <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse14"
          aria-expanded="true" aria-controls="collapse14"
          @if(Config::get('app.locale') == 'ar') style="text-align: right ; direction: rtl; " @endif>
           <i class="fas fa-fw fa-cog"></i>
           <span>{{__('main.reports')}}</span>
       </a>
       <div id="collapse14" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
           <div class="bg-white py-2 collapse-inner rounded @if(Config::get('app.locale') == 'ar') text-right @endif">
               @if( in_array('item_list_report' , $routes )  )
               <a class="collapse-item @if($subSlag == 141) active @endif"
                  href="{{route('item_list_report')}}">{{__('main.item_list_report')}}</a>
               @endif
                   @if( in_array('sold_items_report' , $routes ) )
               <a class="collapse-item @if($subSlag == 142) active @endif"
                  href="{{route('sold_items_report')}}">{{__('main.sold_items_report')}}</a>
                   @endif
                   @if( in_array('sales_report' , $routes ) )
               <a class="collapse-item @if($subSlag == 143) active @endif"
                  href="{{route('sales_report')}}">{{__('main.sales_report')}}</a>
                   @endif
                   <a class="collapse-item @if($subSlag == 1442) active @endif"
                      href="{{route('sales_total_report')}}">{{__('main.sales_total_report')}}</a>
                   @if( in_array('purchase_report' , $routes ) )
               <a class="collapse-item @if($subSlag == 144) active @endif"
                  href="{{route('purchase_report')}}">{{__('main.purchase_report')}}</a>
                   @endif

                   <a class="collapse-item @if($subSlag == 1443) active @endif"
                      href="{{route('purchase_total_report')}}">{{__('main.purchase_total_report')}}</a>

                   @if(  in_array('vendor_account' , $routes ) )
               <a class="collapse-item @if($subSlag == 145) active @endif"
                  href="{{route('vendor_account')}}">{{__('main.vendor_account')}}</a>
                   @endif

                   @if( in_array('gold_stock_report' , $routes ) )

               <a class="collapse-item @if($subSlag == 146) active @endif"
                  href="{{route('gold_stock_report')}}">{{__('main.gold_stock_report')}}</a>

                   @endif
                   @if( in_array('daily_all_movements' , $routes ))
               <a class="collapse-item @if($subSlag == 147) active @endif"
                  href="{{route('daily_all_movements')}}">{{__('main.daily_all_movements')}}</a>

                   @endif


                       <a class="collapse-item @if($subSlag == 1440) active @endif"
                          href="{{route('box_movement_report')}}">{{__('main.box_movement_report')}}</a>


                       <a class="collapse-item @if($subSlag == 1441) active @endif"
                          href="{{route('bank_movement_report')}}">{{__('main.bank_movement_report')}}</a>





                   <a class="collapse-item @if($subSlag == 1444) active @endif" hidden
                      href="{{route('purchase_sales_total_report')}}">{{__('main.purchase_sales_total_report')}}</a>

                      <a class="collapse-item @if($subSlag == 1445) active @endif"
                      href="{{route('movement_report')}}">{{__('main.movement_report')}}</a>








                   @if(  in_array('balance_report' , $routes )  )
                       <a class="collapse-item @if($subSlag == 150) active @endif"
                          href="{{route('account_balance')}}">{{__('main.balance_report')}}</a>

                   @endif

                   @if(  in_array('incoming_list' , $routes )  )
                       <a class="collapse-item @if($subSlag == 148) active @endif"
                          href="{{route('incoming_list')}}">{{__('main.incoming_list')}}</a>

                   @endif
                   @if( in_array('balance_sheet' , $routes )  )
                       <a class="collapse-item @if($subSlag == 149) active @endif"
                          href="{{route('balance_sheet')}}">{{__('main.balance_sheet')}}</a>

                   @endif

                   <a class="collapse-item @if($subSlag == 1446) active @endif"
                      href="{{route('account_movement_report')}}">{{__('main.account_movement_report')}}</a>






           </div>


       </div>
   </li>

    @endif


    <hr class="sidebar-divider d-none d-md-block">
    <li class="nav-item @if($slag == 19) active @endif"
        @if(Config::get('app.locale') == 'ar') style="direction: rtl" @endif>
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse19"
           aria-expanded="true" aria-controls="collapse19"
           @if(Config::get('app.locale') == 'ar') style="text-align: right ; direction: rtl; " @endif>
            <i class="fas fa-fw fa-cog"></i>
            <span>{{__('main.gold_convert_doc')}}</span>
        </a>
        <div id="collapse19" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded @if(Config::get('app.locale') == 'ar') text-right @endif">
                    <a class="collapse-item @if($subSlag == 191) active @endif"
                       href="{{route('gold_convert_doc')}}">{{__('main.gold_convert_list')}}</a>

                <a class="collapse-item @if($subSlag == 192) active @endif"
                   href="{{route('gold_convert_create')}}">{{__('main.gold_convert_create')}}</a>




            </div>


        </div>
    </li>

    <li class="nav-item @if($slag == 20) active @endif"
        @if(Config::get('app.locale') == 'ar') style="direction: rtl" @endif>
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse20"
           aria-expanded="true" aria-controls="collapse20"
           @if(Config::get('app.locale') == 'ar') style="text-align: right ; direction: rtl; " @endif>
            <i class="fas fa-fw fa-cog"></i>
            <span>{{__('main.expenses')}}</span>
        </a>
        <div id="collapse20" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded @if(Config::get('app.locale') == 'ar') text-right @endif">
                <a class="collapse-item @if($subSlag == 201) active @endif"
                   href="{{route('expenses')}}">{{__('main.expenses_list')}}</a>

{{--                <a class="collapse-item @if($subSlag == 202) active @endif"--}}
{{--                   href="{{route('expenses_type' , 0)}}">{{__('main.expenses_type')}}</a>--}}




            </div>


        </div>
    </li>

    <li class="nav-item @if($slag == 21) active @endif"
        @if(Config::get('app.locale') == 'ar') style="direction: rtl" @endif>
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse21"
           aria-expanded="true" aria-controls="#collapse21"
           @if(Config::get('app.locale') == 'ar') style="text-align: right ; direction: rtl; " @endif>
            <i class="fas fa-fw fa-cog"></i>
            <span>{{__('main.catches')}}</span>
        </a>
        <div id="collapse21" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded @if(Config::get('app.locale') == 'ar') text-right @endif">
                <a class="collapse-item @if($subSlag == 211) active @endif"
                   href="{{route('catches')}}">{{__('main.catches_list')}}</a>

{{--                <a class="collapse-item @if($subSlag == 212) active @endif"--}}
{{--                   href="{{route('expenses_type' , 1)}}">{{__('main.catches_type')}}</a>--}}




            </div>


        </div>
    </li>


   <!-- Sidebar Toggler (Sidebar) -->
   <div class="text-center d-none d-md-inline"
        @if(Config::get('app.locale') == 'ar') style=" transform: scaleX(-1);" @endif>
       <button class="rounded-circle border-0" id="sidebarToggle"></button>
   </div>

   <!-- Sidebar Message -->


</ul>
