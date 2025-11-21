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
                            <div class="col-3" > 
                                {{$company ? $company -> name_ar : ''}}
                               <br>  س.ت : {{$company ? $company -> taxNumber : ''}}
                               <br>  ر.ض :  {{$company ? $company -> registrationNumber : ''}}
                               <br>  تليفون :   {{$company ? $company -> phone : ''}}  
                            </div> 
                            <div class="col-6 title text-center"> 
                                <h4  class="alert alert-primary text-center">
                                    {{__('main.purchase_report')}}
                                </h4>
                                @if(isset($branch))
                                 <h5 class="text-center"> [ {{$branch->branch_name}} ] </h5>
                                 @else
                                 <h5 class="text-center"> [ جميع الفروع ] </h5>
                                 @endif
                                <h5 class="text-center">  {{Config::get('app.locale') == 'ar' ? $period_ar : $period}} </h5>
                            </div>
                            <div class="col-3 text-left"> 
                                <br> 
                                <button type="button" class="btn btn-primary btnPrint" id="btnPrint"><i class="fa fa-print"></i></button>
                                
                            </div>
                          </div>
                        </div>   
                        <div class="card-body"> 
                            <div class="table-responsive hoverable-table"id="d-table"   style="direction: rtl;"> 
                                <table class="display w-100  text-nowrap table-bordered" id="example1" 
                                   style="text-align: center;direction: rtl;">
                                    <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">
                                            #
                                        </th>
                                        <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7 ps-2">{{__('main.date')}}</th>
                                        <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7 ps-2">{{__('main.supplier')}}</th>
                                        <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7 ps-2">{{__('main.bill_no')}}</th>
                                        <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7 ps-2">{{__('main.document_type')}}</th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.karat')}} </th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.weight')}} </th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">{{__('main.made_Value_t')}}</th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.total_weight21')}} </th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.net_money')}} </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $sum_weight = 0 ?>
                                    <?php $sum_total = 0 ?>
                                    <?php $sum_tax = 0 ?>
                                    <?php $sum_made = 0 ?>
                                    <?php $sum_net = 0 ?>
                                    @foreach($bills as $item)
                                        <tr>
                                            <td class="text-center">{{$loop -> index + 1}}</td>
                                            <td class="text-center">{{ \Carbon\Carbon::parse($item -> date) -> format('d-m-Y')  }}</td>
                                            <td class="text-center">{{ $item -> supplier  }}</td>
                                            <td class="text-center">
                                            @if($item -> type == 1 )
                                                <a href="{{route('workEntryPreview' , $item -> id)}}" target="_blank">{{$item -> bill_number}}</a>
                                                @else
                                                <a href="{{route('oldEntryPreview' , $item -> id)}}" target="_blank">{{$item -> bill_number}}</a>
                                                @endif
                                            </td>
                                            <td class="text-center">{{$item -> type == 1 ? __('main.new_gold') : __('main.old_gold')  }}</td>
                                            <td class="text-center">{{ (Config::get('app.locale') == 'ar' ? $item -> karat_name_ar : $item -> karat_name_en)  }}</td>
                                            <td class="text-center">{{$item -> weight}}</td>
                                            <td class="text-center">{{$item -> made_money}}</td>
                                            <td class="text-center">{{$item -> weight21}}</td>
                                            <td class="text-center">{{$item -> net_money}}</td>

                                        </tr>
                                        <?php $sum_weight += $item -> weight ?>
                                        <?php $sum_made += $item -> made_money ?>
                                        <?php $sum_net += $item -> weight21 ?>
                                        <?php $sum_tax += $item -> net_money ?>
                                    @endforeach

                                    <tr class="bg-primary text-white font-weight-bolder">
                                        <td class="text-center">t</td>
                                        <td class="text-center">الإجمالي</td>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                        <td class="text-center">{{$sum_weight}}</td>
                                        <td class="text-center">{{$sum_made}}</td>
                                        <td class="text-center">{{$sum_net}}</td>
                                        <td class="text-center">{{$sum_tax}}</td>
                                    </tr>
                                    </tbody>

                                </table>
                            </div>    
                            <div class="table-responsive hoverable-table" style="direction: rtl;"> 
                                <hr>
                                <h2 class="text-center">الإجماليات حسب العيار</h2>
                                <table class="display w-100  text-nowrap table-bordered" id="example1" 
                                   style="text-align: center;direction: rtl;">
                                    <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">
                                            #
                                        </th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.karat')}} </th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">{{__('main.net_weight')}}</th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">{{__('main.total_weight21')}}</th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.net_money')}} </th>

                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach(Config::get('app.locale') == 'ar' ? $grouped_ar : $grouped_en as $group => $items)
                                        <?php $sum_weight_group = 0 ?>
                                        <?php $sum_made_g = 0 ?>
                                        <?php $sum_net_g = 0 ?>
                                        <?php $weight21 = 0 ?>
                                        @foreach($items as $item)
                                            <?php $sum_weight_group += $item -> weight ?>
                                            <?php $sum_made_g += $item -> made_money ?>
                                            <?php $sum_net_g += $item -> net_money ?>
                                            <?php $weight21 += $item -> weight21 ?>
                                        @endforeach
                                        <tr>
                                            <td class="text-center">{{$loop -> index + 1}}</td>
                                            <td class="text-center">{{$group}}</td>
                                            <td class="text-center"> {{$sum_weight_group}} </td>
                                            <td class="text-center"> {{$weight21}} </td>

                                            <td class="text-center">{{$sum_net_g}} </td>
                                        </tr>
                                    @endforeach

                                    </tbody>

                                </table>


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
 
<!-- Page level custom scripts -->
@endsection
<script src="{{asset('assets/js/jquery.min.js')}}"></script>

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
        document.getElementById("main-header").style.display = 'none';
        document.getElementById("main-footer").style.display = 'none'; 
        document.getElementById("back-to-top").style.display = 'none';
        document.getElementById("example1").style.display = 'none';
        document.getElementById("d-table").style.display = 'none';
        window.print();
        document.getElementById("main-header").style.display = 'block';
        document.getElementById("main-footer").style.display = 'block'; 
        document.getElementById("back-to-top").style.display = 'block';
        document.getElementById("example1").style.display = 'block';
        document.getElementById("d-table").style.display = 'block';
    }
</script>
<script>
    $(document).ready(function () {
        document.title = "{{__('main.purchase_report')}}";
    });
</script>
 



