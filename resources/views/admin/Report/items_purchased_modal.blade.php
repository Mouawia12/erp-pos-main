<div class="modal fade" id="purchase_modal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">   
                <div id="head-right">   
                    <h3 class="text-right">{{env('APP_NAME')}}</h3> 
                    <h3>{{__('main.imported_items_reports')}}</h3>   
                    <h5 class="text-center">[ {{$period_ar }} ]</h5>     
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
                                <th>{{__('main.product_code')}}</th>
                                <th>{{__('main.product_name')}}</th>
                                <th>{{__('main.quantity')}}</th>
                                <th>{{__('main.Cost')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $detail)
                            <tr>
                                <td>{{$loop->index+1}}</td>
                                <td>{{$detail ->invoice_no }}</td>
                                <td>{{\Carbon\Carbon::parse($detail ->date) -> format('Y-m-d h:m:s')}}</td>
                                <td>{{$detail ->branch_name }}</td>  
                                <td>{{$detail ->warehouse_name }}</td> 
                                <td>{{$detail ->supplier_name }}</td> 
                                <td>{{$detail ->product_code }}</td>
                                <td>{{$detail ->product_name }}</td>
                                <td>{{$detail ->quantity }}</td>
                                <td>{{$detail ->cost_with_tax }}</td>
                            </tr> 
                        @endforeach
                        </tbody>
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