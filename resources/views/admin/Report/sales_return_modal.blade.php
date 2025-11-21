<style>
    .icon {
        font-size: 30px;
        color: black;
        width: fit-content;
        margin: 10px;
    }

    td, th {
        text-align: center;
    }
</style>
<div class="modal fade" id="purchase_return_modal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel"
     aria-hidden="true"
     style="width: 100%;">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">   
                <div id="head-right">   
                    <h3 class="text-right">{{env('APP_NAME')}}</h3> 
                    <h3>{{__('main.sales_return_report')}}</h3>  
                    <h5>[ {{$period_ar }} ]</h5>   
                </div> 
                <button type="button" class="close cancel-modal" data-bs-dismiss="modal"  aria-label="Close" style="color: red; font-size: 20px; font-weight: bold;">
                    <span aria-hidden="true">&times;</span>
                </button> 
            </div>   
            <div class="modal-body" id="smallBody"> 
                <div class="table-responsive hoverable-table"> 
                    <table class="display w-100 table-bordered" id="example1">
                        <thead>
                            <tr>
                                <th>#</th> 
                                <th>{{__('main.bill_number')}}</th>
                                <th>{{__('main.bill_date')}}</th>
                                <th>{{__('main.branche')}} </th>
                                <th>{{__('main.warehouse')}} </th>
                                <th>{{__('main.supplier_name')}}</th>
                                <th>{{__('main.total')}}</th>
                                <th>{{__('main.paid')}}</th>
                                <th>{{__('main.remain')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                            $total = 0;
                            $paid = 0;
                            $remain = 0; 
                        ?>
                        @foreach($data as $detail) 
                            <tr>
                                <td>{{$loop->index+1}}</td>
                                <td>{{$detail ->invoice_no }}</td>
                                <td>{{\Carbon\Carbon::parse($detail ->created_at) -> format('Y-m-d h:m:s')}}</td> 
                                <td>{{$detail ->branch->branch_name }}</td>
                                <td>{{$detail ->warehouse->name }}</td>  
                                <td>{{$detail ->customer->name}}</td>
                                <td>{{$detail ->net * -1}}</td>
                                <td>{{$detail ->paid <>0 ? $detail ->paid * -1:$detail ->paid}}</td>
                                <td>{{($detail ->net *-1) - ($detail ->paid*-1)}}</td>
                            </tr>
                            <?php 
                                $total += $detail->net *-1;
                                $paid += $detail->paid*-1;
                                $remain += ($detail->net *-1 - $detail->paid*-1);
                            ?> 
                        @endforeach 
                        </tbody>
                        <tfoot>
                            <tr class="bg-primary text-white">
                                <th colspan="5"></th> 
                                <th>{{__('main.total.final')}}</th> 
                                <th>{{$total}}</th>
                                <th>{{$paid}}</th>
                                <th>{{$remain}}</th>
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
