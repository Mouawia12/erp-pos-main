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
 @can('التقارير المخزون')  
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">  
                <div class="card-body px-0 pt-0 pb-2"> 
                    <div class="card shadow mb-4">
                        <div class="card-header py-3"  id="head-right"  style="direction: rtl;"> 
                            <header>
                                    <div class="row" style="direction: ltr;">
                                        <div class="col-3 text-left">
                                            <br> (رقم  :{{$inventory -> id}})
                                            <br> (تاريخ : {{$inventory -> date}})
                                        </div>
                                        <div class="col-6 c text-center">
                                            <h4 class="alert alert-primary text-center">تقرير الجرد</h4> 
                                            <h4>[ <strong>الفرع </strong>: {{$inventory -> branch->branch_name}}  /  <strong>المستودع </strong>: {{$inventory -> warehouse->name}} ]</h4>
                                        </div>
                                        <div class="col-3 c">
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
                        <div class="card-body" style="direction: rtl;"> 
                            <div class="table-responsive hoverable-table" style="direction: rtl;"> 
                                <table class="display w-100  text-nowrap table-bordered" id="example1" 
                                   style="text-align: center;direction: rtl;">
                                    <thead>
                                        <tr>
                                            <th> #</th> 
                                            <th>{{__('main.code')}}</th>
                                            <th>{{__('main.item')}}</th>
                                            <th>{{__('main.unit')}}</th>
                                            <th>{{__('main.balance_book')}}</th> 
                                            <th>{{__('main.balance_now')}}</th> 
                                            <th>{{__('الفارق')}}</th> 
                                            <th>{{__('main.status')}}</th> 
                                        </tr>
                                    </thead>
                                    <tbody> 
                                    <?php 
                                        $sum_total = 0 ;
                                        $status1='مطابق';
                                        $status2='عجز'; 
                                        $status3='زيادة';   
                                        $sum_quantity = 0;
                                        $sum_new_quantity = 0;
                                        $increase = 0;
                                        $impotence = 0;
                                    ?>
                                    @foreach($inventory_items as $item)
                                        <?php  
                                            $new_quantity = $item -> new_quantity > 0 ? $item -> new_quantity : $item -> quantity;
                                        ?>
                                        <tr>
                                            <td class="text-center">{{$loop -> index + 1}}</td>
                                            <td class="text-center">{{$item ->item-> code}}</td>
                                            <td class="text-center">{{$item ->item-> name}}</td> 
                                            <td class="text-center">{{$item ->units-> name}}</td>
                                            <td class="text-center">{{$item -> quantity}}</td>
                                            <td class="text-center">{{$new_quantity}}</td>  
                                            <td class="text-center">@if($new_quantity == $item -> quantity) {{0}} @else {{$new_quantity - $item -> quantity}} @endif</td>  
                                            <td class="text-center">@if($new_quantity == $item -> quantity) {{$status1}} @elseif($new_quantity < $item -> quantity) {{$status2}} @else {{$status3}}  @endif</td> 
                                        <?php 
                                            $sum_total = $loop -> index;
                                            $sum_quantity += $item -> quantity;
                                            $sum_new_quantity += $new_quantity;
                                            if($new_quantity > $item -> quantity){ 
                                                $increase += $new_quantity - $item -> quantity; 
                                            }elseif($new_quantity < $item -> quantity){ 
                                                $impotence += $item -> quantity - $new_quantity; 
                                            }  
                                        ?> 
                                        </tr> 
                                    @endforeach   
                                    </tbody>  
                                    <tfoot>
                                        <tr> 
                                            <td class="text-center bg-primary text-white" colspan="4">{{__('main.total')}}</td> 
                                            <td class="text-center bg-primary text-white">{{$sum_quantity}}</td> 
                                            <td class="text-center bg-primary text-white">{{$sum_new_quantity}}</td>  
                                            <td class="text-center bg-primary text-white">
                                                {{($increase == $impotence) ? 0 : ''}}
                                                {{$impotence>0 ? $status2.': '.$impotence : ''}}<br>
                                                {{$increase>0 ? $status3.': '.$increase : ''}}
                                               
                                             
                                            </td>
                                            <td class="text-center bg-primary text-white">{{($increase == $impotence) ? $status1 : ''}}</td> 
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

    </div>
    <!-- End of Content Wrapper --> 
<!-- End of Page Wrapper --> 
@endcan 
@endsection 
@section('js') 
<script type="text/javascript">
    let id = 0;  
    $(document).ready(function () {
        $(document).on('click', '#btnPrint', function (event) {
            print(); 
        }); 
        document.title = "تقرير الجرد  - رقم: {{$inventory -> id}} - بتاريخ :{{$inventory -> date}}";
    });
</script> 
@endsection 
 



