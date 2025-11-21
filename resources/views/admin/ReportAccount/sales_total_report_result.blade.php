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
            <div class="card-body px-0 pt-0 pb-2"> 
                    <div class="card shadow mb-3 ">
                        <div class="card-header py-3 " id="head-right"  style="direction: rtl;border:solid 1px gray"> 
                          <div class="row">
                            <div class="col-3"> 
                                {{$company ? $company -> name_ar : ''}}
                               <br>  س.ت : {{$company ? $company -> taxNumber : ''}}
                               <br>  ر.ض :  {{$company ? $company -> registrationNumber : ''}}
                               <br>  تليفون :   {{$company ? $company -> phone : ''}}  
                            </div>   
                            <div class="col-6 title text-center">
                                <h4  class="alert alert-primary text-center">
                                    {{__('main.sales_total_report')}}
                                </h4>
                                @if(isset($branch))
                                <h5 class="text-center"> [ {{$branch->branch_name}} ] </h5>
                                @else
                                <h5 class="text-center"> [ جميع الفروع ] </h5>
                                @endif
                                <h5 class="text-center">  {{Config::get('app.locale') == 'ar' ? $period_ar : $period}} </h5>
                            </div>
                            <div class="col-3"> 
                            </div> 
                          </div>
                        </div>
                        <div class="card-body"> 
                            <div class="table-responsive hoverable-table" style="direction: rtl;"> 
                                <table class="display w-100  text-nowrap table-bordered" id="example1" 
                                   style="text-align: center;direction: rtl;">
                                    <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">#  </th>
                                        <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7 ps-2">{{__('main.document_type')}}</th>
                                        <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7 ps-2">{{__('main.date')}}</th>
                                        <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7 ps-2">{{__('main.bill_no')}}</th>
                                        <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7 ps-2">{{__('main.client')}}</th>
                                        <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7 ps-2">{{__('main.total_weight21')}}</th>
                                        <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7 ps-2">{{__('main.total_money')}}</th>
                                        <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7 ps-2">{{__('main.discount')}}</th>
                                        <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7 ps-2">{{__('main.tax')}}</th>
                                        <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7 ps-2">{{__('main.total_with_tax')}}</th>
                                        <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7 ps-2">{{__('main.net_after_discount')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $sum_weight = 0 ?>
                                    <?php $sum_total = 0 ?>
                                    <?php $sum_tax = 0 ?>
                                    <?php $sum_made = 0 ?>
                                    <?php $sum_net = 0 ?>
                                    <?php $sum_discount = 0 ?>
                                    @foreach($all as $item)
                                        <tr>
                                            <td class="text-center">{{$loop -> index + 1}}</td>
                                            <td class="text-center">{{$item -> type == 0 ? 'فاتورة مبيعات ذهب كسر' : 'فاتورة مبيعات ذهب مشغول'}}</td>
                                            <td class="text-center">{{ \Carbon\Carbon::parse($item -> date) -> format('d-m-Y')  }}</td>
                                            <td class="text-center">
                                                @if($item -> type == 1 )
                                                    <a href="{{route('workExitPreview' , $item -> id)}}" target="_blank">{{$item -> bill_number}}</a>
                                                @else
                                                    <a href="{{route('oldExitPreview' , $item -> id)}}" target="_blank">{{$item -> bill_number}}</a>
                                                @endif
                                            </td>
                                            <td class="text-center">{{$item -> client}}</td>
                                            <td class="text-center">{{$item -> total21_gold}}</td>
                                            <!-- 
                                            //update 20-08-2023
                                            <td class="text-center">{{$item -> net_money - $item -> tax}}</td>
                                            End -->
                                            <td class="text-center">{{$item -> total_money}}</td>
                                            <td class="text-center">{{$item -> discount}}</td>
                                            <td class="text-center">{{$item -> tax}}</td>
                                            <td class="text-center">{{$item -> total_money + $item -> tax}}</td>
                                            <td class="text-center">{{($item -> total_money + $item -> tax ) - $item -> discount}}</td>
                                        </tr>

                                        <?php $sum_weight += $item -> total21_gold ?>
                                        <?php 
                                             //update 20-08-2023
                                            //$sum_total += ($item -> net_money - $item -> tax) 
                                            //<?php $sum_net += $item -> total_money  
                                            $sum_total += ($item -> total_money) 
                                        ?>
                                        <?php $sum_tax += $item -> tax ?>
                                        <?php $sum_net += $item -> total_money + $item -> tax ?>
                                        <?php $sum_discount += $item -> discount?>
                                    @endforeach 
                                        <tr class="bg-primary text-white font-weight-bolder">
                                            <td class="text-center">t</td>
                                            <td class="text-center">الإجمالي</td>
                                            <td class="text-center"></td>
                                            <td class="text-center"></td>
                                            <td class="text-center"></td> 
                                            <td class="text-center">{{$sum_weight}}</td>
                                            <td class="text-center">{{$sum_total}}</td>
                                            <td class="text-center">{{$sum_discount}}</td>
                                            <td class="text-center">{{$sum_tax}}</td>
                                            <td class="text-center">{{$sum_net}}</td>
                                            <td class="text-center">{{$sum_net - $sum_discount}}</td>
                                        </tr>
                                    </tbody> 
                                </table>

                                {{--                                <h2 class="text-center">الإجماليات حسب العيار</h2>--}}
                                {{--                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">--}}
                                {{--                                    <thead>--}}
                                {{--                                    <tr>--}}
                                {{--                                        <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">--}}
                                {{--                                            #--}}
                                {{--                                        </th>--}}
                                {{--                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.karat')}} </th>--}}
                                {{--                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.quantity')}} </th>--}}
                                {{--                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">{{__('main.weight')}}</th>--}}
                                {{--                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.total_without_tax')}} </th>--}}
                                {{--                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.gram_tax')}} </th>--}}
                                {{--                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.made_Value')}} </th>--}}
                                {{--                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.net_money')}} </th>--}}

                                {{--                                    </tr>--}}
                                {{--                                    </thead>--}}
                                {{--                                    <tbody>--}}

                                {{--                                    @foreach(Config::get('app.locale') == 'ar' ? $grouped_ar : $grouped_en as $group => $items)--}}
                                {{--                                        <?php $sum_weight_group = 0 ?>--}}
                                {{--                                        <?php $sum_total_group = 0 ?>--}}
                                {{--                                        <?php $count = 0 ?>--}}
                                {{--                                        <?php $sum_tax_g = 0 ?>--}}
                                {{--                                        <?php $sum_made_g = 0 ?>--}}
                                {{--                                        <?php $sum_net_g = 0 ?>--}}
                                {{--                                        @foreach($items as $item)--}}
                                {{--                                        <?php $sum_weight_group += $item -> weight ?>--}}
                                {{--                                        <?php $sum_total_group += ($item -> weight * $item -> gram_price) ?>--}}
                                {{--                                        <?php $count += 1 ?>--}}
                                {{--                                        <?php $sum_tax_g += $item -> gram_tax ?>--}}
                                {{--                                        <?php $sum_made_g += $item -> gram_manufacture ?>--}}
                                {{--                                        <?php $sum_net_g += $item -> net_money ?>--}}
                                {{--                                        @endforeach--}}
                                {{--                                        <tfoot>--}}
                                {{--                                        <tr>--}}
                                {{--                                          <td class="text-center">{{$loop -> index + 1}}</td>--}}
                                {{--                                          <td class="text-center">{{$group}}</td>--}}
                                {{--                                            <td class="text-center"> {{$count}} </td>--}}
                                {{--                                            <td class="text-center"> {{$sum_weight_group}} </td>--}}
                                {{--                                            <td class="text-center"> {{$sum_total_group}}</td>--}}
                                {{--                                            <td class="text-center"> {{$sum_tax_g}}</td>--}}
                                {{--                                            <td class="text-center"> {{$sum_made_g}}</td>--}}
                                {{--                                            <td class="text-center">{{$sum_net_g}} </td>--}}
                                {{--                                        </tr>--}} 
                                {{--                                    @endforeach--}}

                                {{--                                    </tbody>--}}

                                {{--                                </table>--}}


                            </div>


                        </div>
                    </div>

                </div>


            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content --> 
    </div>
    <!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
   
@endsection
<script src="{{asset('assets/js/jquery.min.js')}}"></script> 
  

<!-- Page level custom scripts -->

<script type="text/javascript">
    let id = 0;


    $(document).ready(function () {
        $(document).on('click', '#btnPrint', function (event) {
            printPage();
        });

    });

    function printPage(){
        var css = '@page { size: landscape; }',
            head = document.head || document.getElementsByTagName('head')[0],
            style = document.createElement('style');

        style.type = 'text/css';
        style.media = 'print';

        if (style.styleSheet){
            style.styleSheet.cssText = css;
        } else {
            style.appendChild(document.createTextNode(css));
        }

        head.appendChild(style);

        window.print();
    }
</script>
<script>
    $(document).ready(function () {
        document.title = "{{__('main.sales_total_report')}}";
    });
</script>


