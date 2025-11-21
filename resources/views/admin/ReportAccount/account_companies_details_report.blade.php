@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
        <!-- row opened -->
    <style>
        table.display.w-100.text-nowrap.table-bordered.dataTable.dtr-inline {
            direction: rtl;
            text-align:center;
        }
        body{
            direction: rtl; 
        }
        th.text-center {
            border: 1px solid #eee !important;
        }
  
    </style>
<div class="row row-sm"> 
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header pb-0" id="card-header">
                <div class="col-lg-12 margin-tb"> 
                </div>
                <div class="clearfix"></div> 
            </div>   
            <div class="card-body px-0 pt-0 pb-2"> 
                <div class="card shadow mb-4">
                    <div class="card-header py-3 " id="head-right"  style="direction: rtl;border:solid 1px gray"> 
                        <header>
                            <div class="row" style="direction: ltr;">
                                <div class="col-4 text-left">   
                                    <br> 
                                    <button type="button" class="btn btn-primary btnPrint" id="btnPrint"><i class="fa fa-print"></i></button>
                                </div>
                                <div class="col-4 c">
                                    <h4 class="alert alert-primary text-center">
                                         {{__('تقرير حركة حساب تفصيلي')}}
                                    </h4>
                                    @if(isset($branch))
                                    <h5 class="text-center"> [ {{$branch->branch_name}} ] </h5>
                                    @else
                                    <h5 class="text-center"> [ جميع الفروع ] </h5>
                                    @endif

                                    <h5 class="text-center">  {{Config::get('app.locale') == 'ar' ? $period_ar : $period}} </h5>
                                </div>
                                <div class="col-4 c">
                               <span style="text-align: right;">
                                   {{$company ? $company -> name_ar : ''}}
                                <br>  س.ت : {{$company ? $company -> taxNumber : ''}}
                                <br>  ر.ض :  {{$company ? $company -> registrationNumber : ''}}
                                <br>  تليفون :   {{$company ? $company -> phone : ''}}
                               </span>
                                </div>
                            </div>
                        </header> 
                    </div>
                </div>          
      
                <div class="card-body">
                    <div class="table-responsive hoverable-table" style="direction: rtl;"> 
                        <table class="display w-100  text-nowrap table-bordered" id="example1" 
                           style="text-align: center;direction: rtl;">
                            <thead>
                                <tr>
                                    <th class=" ps-2" rowspan="2">#</th>
                                    <th class=" ps-2" rowspan="2">{{__('main.date')}}</th>
                                    <th class="text-center " rowspan="2" >{{__('السند')}}</th>
                                    <th class="text-center " rowspan="2" > رقمه</th>
                                    <th class="text-center " rowspan="1" colspan="2"> النقدية</th>
                                    <th class="text-center " rowspan="1" colspan="6">الذهب</th> 
                                </tr>
                                <tr> 
                                    <th class="text-center " rowspan="1" >{{__('مبلغ مدين')}}</th>
                                    <th class="text-center " rowspan="1" >{{__('مبلغ دائن')}}</th>
                                    <th class="text-center " rowspan="1" >{{__('18 مدين')}}</th>
                                    <th class="text-center " rowspan="1" >{{__('18 دائن')}}</th>
                                    <th class="text-center " rowspan="1" >{{__('21 مدين')}}</th>
                                    <th class="text-center " rowspan="1" >{{__('21 دائن')}}</th>
                                    <th class="text-center " rowspan="1" >{{__('24 مدين')}}</th>
                                    <th class="text-center " rowspan="1" >{{__('24 دائن')}}</th> 
                                </tr> 
                            </thead>
                            <tbody>
                            @php 
                                   
                                $net_debit = 0;
                                $net_credit = 0;
                                $balance = 0;
    
                                $k18_debit = 0;
                                $k18_credit = 0; 
                                $k21_debit = 0;
                                $k21_credit = 0; 
                                $k24_debit = 0;
                                $k24_credit = 0;
                                $side = 0;
                                $net = 0;
                                $basedon_no = 0;
    
                                if(isset($account_balance) and $account_balance->side == 1){
                                    $balance = $account_balance->before_debit - $account_balance->before_credit; 
                                    $balance_debit = $balance;
                                    $balance_credit = 0;
                                }elseif(isset($account_balance) and $account_balance->side == 2){  
                                    $balance = $account_balance->before_credit - $account_balance->before_debit;
                                    $balance_debit = 0;
                                    $balance_credit = $balance;
                                }else{
                                    $balance_debit = 0;
                                    $balance_credit = 0;
                                    $balance = 0;
                                }
                                  
                                $net_debit = $balance_debit;
                                $net_credit = $balance_credit;
                            @endphp
    
                                <tr>
                                    <td class="text-center">1</td>
                                    <td class="text-center">--</td> 
                                    <td class="text-center">رصيد اول المدة</td> 
                                    <td class="text-center"></td> 
                                    <td class="text-center">{{$balance_debit}}</td>
                                    <td class="text-center">{{$balance_credit}}</td>  
                                    <td class="text-center">0</td>
                                    <td class="text-center">0</td>
                                    <td class="text-center">0</td>
                                    <td class="text-center">0</td>
                                    <td class="text-center">0</td>
                                    <td class="text-center">0</td>
                                </tr> 
    
                            @foreach($accounts as  $unit)
                                <tr>  
                                    <td class="text-center">{{$loop -> index + 2}}</td>
                                    <td class="text-center"> {{\Carbon\Carbon::parse($unit->date) -> format('d-m-Y')}}</td> 
                                    <td class="text-center"> {{$unit->baseon_text}}</td>
                                    <td class="text-center"> {{$unit->basedon_no}}</td>  
                                    <td class="text-center">{{$unit->debit}}</td>
                                    <td class="text-center">{{$unit->credit}}</td> 
    
                                    @if($basedon_no <> $unit->basedon_no)  
                                    <td class="text-center">0</td>
                                    <td class="text-center">{{(float) $unit->K18}}</td>
                                    <td class="text-center">0</td>
                                    <td class="text-center">{{(float) $unit->K21}}</td>
                                    <td class="text-center">0</td>
                                    <td class="text-center">{{(float) $unit->K24}}</td>  
                                    @else
                                    <td class="text-center">0</td>
                                    <td class="text-center">0</td>
                                    <td class="text-center">0</td>
                                    <td class="text-center">0</td>
                                    <td class="text-center">0</td>
                                    <td class="text-center">0</td>
                                    @endif
    
                                    @php   
                                        $net_debit += $unit->debit;
                                        $net_credit += $unit->credit;
    
                                        if($basedon_no <> $unit->basedon_no)
                                        {
                                            if($unit->side == 1){ 
                                            $k18_debit += (float) $unit->K18 ; 
                                            $k21_debit += (float) $unit->K21; 
                                            $k24_debit += (float) $unit->K24; 
                                            $side = 1;
                                      
                                            }else{
                                                $k18_credit += (float) $unit->K18;  
                                                $k21_credit += (float) $unit->K21;  
                                                $k24_credit += (float) $unit->K24; 
                                                $side = 2;
                                            } 
    
                                        }
                                        
                                        $basedon_no = $unit->basedon_no;
                                    @endphp
                                </tr> 
                            @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bolder" style="background:lightblue">
                                    <td class="text-center"></td>
                                    <td class="text-center" ></td>
                                    <td class="text-center" ></td>
                                    <td class="text-center" >{{__('الاجمالي')}}</td>
                                    <td class="text-center" >{{$net_debit}}</td>
                                    <td class="text-center" >{{$net_credit}}</td>
                                    <td class="text-center" >{{$k18_debit}}</td>
                                    <td class="text-center" >{{$k18_credit}}</td>
                                    <td class="text-center" >{{$k21_debit}}</td>
                                    <td class="text-center" >{{$k21_credit}}</td>
                                    <td class="text-center" >{{$k24_debit}}</td>
                                    <td class="text-center" >{{$k24_credit}}</td> 
                                </tr>
                                <tr class="font-weight-bolder" style="background: lightblue">
                                    <td class="text-center"></td>
                                    <td class="text-center" ></td>
                                    <td class="text-center" ></td>
                                    <td class="text-center" >{{__('الرصيد')}}</td>
                                    <td class="text-center" colspan="2"> 
                                        @if($side == 1) 
                                            {{$net_debit - $net_credit}}
                                        @else
                                            {{$net_credit - $net_debit}}
                                        @endif 
                                    </td> 
                                    <td class="text-center" colspan="2"> 
                                        @if($side == 1)
                                            {{$k18_debit - $k18_credit}}
                                        @else
                                            {{$k18_credit - $k18_debit}}
                                        @endif  
                                    </td>  
                                    <td class="text-center" colspan="2"> 
                                        @if($side == 1)
                                            {{$k21_debit - $k21_credit}}
                                        @else 
                                            {{$k21_credit - $k21_debit}} 
                                        @endif  
                                    </td> 
                                    <td class="text-center" colspan="2"> 
                                        @if($side == 1)
                                            {{$k24_debit - $k24_credit}}
                                        @else
                                            {{$k24_credit - $k24_debit}} 
                                        @endif 
                                    </td>  
                                </tr>
                          
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</div>
<!-- End of Main Content --> 
 
@endsection
<script src="{{asset('assets/js/jquery.min.js')}}"></script>  

<script type="text/javascript">
    let id = 0; 
    $(document).ready(function () {
        $(document).on('click', '#btnPrint', function (event) {
            printPage();
        }); 
    }); 

    function printPage() {
        var css = '@page { size: landscape; }',
            head = document.head || document.getElementsByTagName('head')[0],
            style = document.createElement('style');

        style.type = 'text/css';
        style.media = 'print';

        if (style.styleSheet) {
            style.styleSheet.cssText = css;
        } else {
            style.appendChild(document.createTextNode(css));
        }

        head.appendChild(style);
        document.getElementById("main-header").style.display = 'none';
        document.getElementById("main-footer").style.display = 'none';
        document.getElementById("card-header").style.display = 'none'; 
        window.print();
        document.getElementById("main-header").style.display = 'block';
        document.getElementById("main-footer").style.display = 'block';
        document.getElementById("card-header").style.display = 'block'; 
    } 
</script>
<script>
    $(document).ready(function () {
        document.title = "{{__('main.account_movement_report') .'-'. $account_name}} ";
    });
</script>

 

