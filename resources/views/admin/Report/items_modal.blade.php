
<style>
    .icon{
        font-size: 30px;
        color: black;
        width: fit-content;
        margin: 10px;
    }
    td , th {
        text-align: center;
    }
</style>
<div class="modal fade" id="items_modal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel" aria-hidden="true"
     style="width: 100%;">
    <div class="modal-dialog modal-xl" role="document" >
        <div class="modal-content">
            <div class="modal-header">   
                <div id="head-right">   
                    <h3 class="text-right">{{env('APP_NAME')}}</h3> 
                    <h3 class="text-center">
                        @if($type == 0)
                        {{__('main.items_report')}}
                        @elseif($type == 1)
                            {{__('main.under_limit_items_report')}}
                        @elseif($type == 2)
                            {{__('main.no_balance_items_report')}}
                        @endif
                    </h3>   
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
                                <th>{{__('main.product_code')}} </th>
                                <th>{{__('main.product_name')}}</th>
                                @if($isbranches == 1)
                                <th>{{__('main.categories')}} </th>
                                <th>{{__('main.branche')}} </th>
                                <th>{{__('main.warehouse')}} </th>
                                @endif
                                <th>{{__('main.unit')}}</th>
                                <th>{{__('main.quantity')}}</th>
                                @if($type == 1)
                                <th>{{__('main.alert_quantity')}}</th>
                                @endif
                                <th>{{__('main.Cost')}}</th>
                                <th>{{__('main.price')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                            $total = 0; 
                            $cost = 0;
                        ?>
                            @foreach($data as $item) 
                                <tr>
                                    <td>{{$loop->index+1}}</td>
                                    <td >{{$item -> code}}</td>
                                    <td >{{$item -> name}}</td>
                                    @if($isbranches == 1)
                                    <td>{{$item ->categories_name }}</td>  
                                    <td>{{$item ->branch_name }}</td>
                                    <td>{{$item ->warehouse_name }}</td>  
                                     @endif
                                    <td >{{$item ->units->name}}</td>
                                    <td >{{$item ->qty}}</td>
                                    @if($type == 1)
                                    <td >{{$item ->alert_quantity}}</td>
                                    @endif
                                    <td >{{$item ->cost}}</td>
                                    <td >{{$item ->price}}</td>
                                </tr>
                                <?php 
                                    $total += $item -> price ;
                                    $cost += $item -> cost ;
                                ?> 
                            @endforeach 
                        </tbody>
                        <tfoot>
                            <tr class="bg-primary text-white">
                                @if($isbranches == 1)
                                <th colspan="7"></th> 
                                @else
                                <th colspan="4"></th> 
                                @endif
                                <th>{{__('main.total')}}</th> 
                                @if($type == 1)
                                <th></th>
                                @endif
                                <th>{{$cost}}</th>
                                <th>{{$total}}</th>
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
