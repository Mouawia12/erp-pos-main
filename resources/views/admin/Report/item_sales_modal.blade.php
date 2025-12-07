 
<div class="modal fade" id="item_sales_modal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel" aria-hidden="true"
     style="width: 100%;">
    <div class="modal-dialog modal-xl" role="document" style="min-width: 1000px">
        <div class="modal-content">
            <div class="modal-header">   
                <div id="head-right">   
                    <h3 class="text-right">{{env('APP_NAME')}}</h3> 
                    <h3 class="text-center">{{__('main.sales_report_by_item')}}</h3>  
                    <h5 class="text-center">[ {{$period_ar }} ]</h5>  
                </div> 
                <button type="button" class="close cancel-modal"  data-bs-dismiss="modal"  aria-label="Close" style="color: red; font-size: 20px; font-weight: bold;">
                    <span aria-hidden="true">&times;</span>
                </button> 
            </div>
   
            <div class="modal-body" id="smallBody"> 

                <div class="table-responsive hoverable-table" style="direction: rtl;"> 
                    <table class="display w-100  table-bordered" id="example1" 
                       style="text-align: center;direction: rtl;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{__('main.bill_number')}}</th>
                                <th>{{__('main.bill_date')}}</th> 
                                <th>{{__('main.branche')}} </th>
                                <th>{{__('main.warehouse')}} </th>
                                <th>{{__('main.customer')}}</th>
                                <th>{{__('main.vehicle_plate')}}</th>
                                <th>{{__('main.vehicle_odometer')}}</th>
                                <th>{{__('main.product_code')}} </th>
                                <th>{{__('main.product_name')}}</th>
                                <th>{{__('main.quantity')}}</th>
                                <th>{{__('main.amount')}}</th>
                                <th>{{__('main.tax')}}</th>
                                <th>{{__('main.total')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                            $total = 0 ;
                            $net = 0 ; 
                            $tax = 0;
                        ?>
                            @foreach($data as $detail)
                                <tr>
                                    <td>{{$loop->index+1 }}</td>
                                    <td>{{$detail ->invoice_no }}</td>
                                    <td>{{\Carbon\Carbon::parse($detail -> created_at) -> format('Y-m-d h:m:s')}}</td> 
                                    <td>{{$detail ->branch_name}}</td>
                                    <td>{{$detail ->warehouse_name}}</td>
                                    <td>{{$detail ->customer_name ?? '-'}}</td>
                                    <td>{{$detail ->vehicle_plate ?? '-'}}</td>
                                    <td>{{$detail ->vehicle_odometer ?? '-'}}</td>
                                    <td>{{$detail ->product_code }}</td>
                                    <td>{{$detail ->product_name }}</td>
                                    <td>{{$detail ->quantity }}</td>
                                    <td>{{$detail ->total }}</td>
                                    <td>{{$detail ->tax + $detail ->tax_excise }}</td>
                                    <td>{{$detail ->tax + $detail ->tax_excise + $detail ->total }}</td>
                                </tr>
                                <?php 
                                    $total += $detail ->total ;
                                    $tax += $detail ->tax + $detail ->tax_excise;
                                    $net += $detail ->tax + $detail ->tax_excise + $detail ->total;
                                ?>
                            @endforeach 
                        </tbody>
                        <tfoot>
                            <tr class="bg-primary text-white">
                                <th colspan="10"></th> 
                                <th> الإجمالي</th> 
                                <th>{{$total}}</th>
                                <th>{{$tax}}</th>
                                <th>{{$net}}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <br> 
            </div>
        </div>
    </div>
</div>

<script> 
    $("#example1").DataTable({
        "responsive": true, "lengthChange": true, "autoWidth": false, 
        "buttons": ["copy", "excel", "print", "colvis",
		], 
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
