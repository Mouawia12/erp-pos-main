<div class="modal fade" id="items_modal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">   
                <div id="head-right">   
                    <h3 class="text-right">{{env('APP_NAME')}}</h3> 
                    <h3 class="text-center">{{__('main.users_transactions_report')}}</h3>  
                    <h4 class="text-center">
                        [ 
                            {{__('main.branche')}} : <strong>{{ $branch_name }}</strong>  &nbsp&nbsp/&nbsp&nbsp
                            {{__('main.warehouse')}} : <strong>{{$warehouse_name }}</strong> &nbsp&nbsp/&nbsp&nbsp
                            {{$period_ar }}
                        ]
                    </h4>   
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
                                <th>{{__('main.product_code')}}</th>
                                <th>{{__('main.product_name')}}</th> 
                                <th>{{__('main.qnt_purchase')}}</th>
                                <th>{{__('main.qnt_purchase_return')}}</th>
                                <th>{{__('main.qnt_sales')}}</th>
                                <th>{{__('main.qnt_sales_return')}}</th>
                                <th>{{__('main.qnt_net')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($result as $detail)
                            @if(\Carbon\Carbon::parse($detail['date'] ) -> gte ( \Carbon\Carbon::parse($fdate)) &&
                                  \Carbon\Carbon::parse($detail['date'] ) -> lte ( \Carbon\Carbon::parse($tdate))
                            )
                                @if($detail['warehouse']== $warehouse || $warehouse == 0)
                                    @if($detail['item_id']  == $item_id || $item_id == 0)
                                            <tr>
                                                <td>{{$loop->index+1}}</td>
                                                <td>{{$detail['product_code'] }}</td>
                                                <td>{{$detail['product_name'] }}</td> 
                                                <td>{{$detail['qnt_purchase'] }}</td>
                                                <td>{{$detail['qnt_purchase_return'] }}</td>
                                                <td>{{$detail['qnt_sales'] }}</td>
                                                <td>{{$detail['qnt_sales_return']  }}</td>
                                                <td>
                                                    {{$detail['qnt_update'] + $detail['qnt_purchase']  +
                                                    $detail['qnt_purchase_return'] - $detail['qnt_sales'] - $detail['qnt_sales_return']}}
                                                </td>
                                            </tr>
                                    @endif
                                @endif
                            @endif
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