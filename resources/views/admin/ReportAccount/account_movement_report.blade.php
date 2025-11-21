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
                                </div>
                                <div class="col-4 c">
                                    <h4 class="alert alert-primary text-center">
                                         {{__('main.account_movement_report')}}
                                    </h4>
                                    <h3 class="text-center">{{$account_name}} </h3> 
                                    <h5 class="text-center"> 
                                        [ {{Config::get('app.locale') == 'ar' ? $period_ar : $period}} ]
                                    </h5>
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
                               </tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">{{__('main.date')}}</th> 
                                    <th class="text-center">{{__('main.document_type')}}</th>
                                    <th class="text-center">{{__('main.Debit')}}</th>
                                    <th class="text-center">{{__('main.Credit')}}</th>
                                    <th class="text-center">{{__('main.balance')}}</th>
                                </tr> 
                            </thead>
                            <tbody>
                            @php 
                                $net = 0;
                                $balance = 0;
                                
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
                                  
                                $net = $balance;
                            @endphp 
                                <tr>
                                    <td class="text-center">1</td>
                                    <td class="text-center">--</td> 
                                    <td class="text-center">رصيد اول المده</td> 
                                    <td class="text-center">{{number_format($balance_debit,2)}}</td>
                                    <td class="text-center">{{number_format($balance_credit,2)}}</td>  
                                    <td class="text-center">{{number_format($balance,2)}}</td>
                                </tr>  
                            @foreach($accounts as $index=>$unit)
                                <tr>
                                    <td class="text-center">{{$loop -> index + 1}}</td>
                                    <td class="text-center"> {{\Carbon\Carbon::parse($unit->date) -> format('d-m-Y')}}</td>  
                                    <td class="text-center"> {{$unit->basedon_no.'-'.$unit->baseon_text}}</td> 
                                    <td class="text-center">{{number_format($unit->debit,2)}}</td>
                                    <td class="text-center">{{number_format($unit->credit,2)}}</td>  
                                    <td class="text-center">{{number_format($unit->debit - $unit->credit,2) }}</td> 
                                    @php 
                                        if($unit->side == 1){
                                            $net += ($unit->debit - $unit->credit);
                                        }else{
                                            $net += ($unit->credit - $unit->debit);
                                        }    
    
                                    @endphp
                                </tr> 
                            @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="background: #c3e6cb">
                                   <td class="text-center" colspan="2"></td>
                                    <td class="text-center">{{__('main.total_balance')}}</td>
                                    <td class="text-center" colspan="2"></td>
                                    <td class="text-center" >{{number_format($net,2)}}</td>  
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 
<!-- End of Page Wrapper -->

@endsection 
@section('js')    
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
@endsection 

 

