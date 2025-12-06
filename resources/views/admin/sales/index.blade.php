@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif 
<style>
.hoverable-table .btn-primary {
    margin-left: 5px !important;
}

a.btn {
    margin-left: 5px;
}
</style>
    @can('عرض مبيعات') 
    <!-- End Navbar -->
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0"  id="head-right" >
                        <div class="col-lg-12 margin-tb">
                            <h4  class="alert alert-primary text-center">
                                {{ __('main.sales_invoices')}}
                            </h4>
                        </div> 
                    </div>
                    <div class="row mt-1 mb-1 text-center justify-content-center align-content-center"> 
                        @can('اضافة مبيعات')   
                        <a href="{{route('add_sale')}}" type="button" class="btn btn-labeled btn-info"> 
                            <i class="fa fa-plus"></i></span>
                            {{__('main.add_new')}}
                        </a>
                        @endcan  
                    </div> 
                    <div class="clearfix"><hr></div> 
                    <div class="px-3">
                        <form id="filterForm" class="row g-2">
                            <div class="col-md-2">
                                <label>{{ __('main.bill_number') }}</label>
                                <input type="text" class="form-control" id="filter_invoice_no" name="invoice_no" placeholder="{{__('main.bill_number')}}">
                            </div>
                            <div class="col-md-2">
                                <label>{{ __('main.clients') }}</label>
                                <select class="form-control" id="filter_customer_id" name="customer_id">
                                    <option value="">{{ __('main.choose') }}</option>
                                    @foreach($customers as $c)
                                        <option value="{{$c->id}}">{{$c->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>{{ __('main.representatives') }}</label>
                                <select class="form-control" id="filter_representative_id" name="representative_id">
                                    <option value="">{{ __('main.choose') }}</option>
                                    @foreach($representatives as $r)
                                        <option value="{{$r->id}}">{{$r->user_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>{{ __('main.branche') }}</label>
                                @if(empty(Auth::user()->branch_id))
                                    <select class="form-control" id="filter_branch_id" name="branch_id">
                                        <option value="">{{ __('main.choose') }}</option>
                                        @foreach($branches as $b)
                                            <option value="{{$b->id}}">{{$b->branch_name}}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="text" class="form-control" value="{{Auth::user()->branch->branch_name}}" readonly>
                                @endif
                            </div>
                            <div class="col-md-2">
                                <label>{{ __('main.from_date') ?? 'من تاريخ' }}</label>
                                <input type="date" class="form-control" id="filter_date_from" name="date_from">
                            </div>
                            <div class="col-md-2">
                                <label>{{ __('main.to_date') ?? 'إلى تاريخ' }}</label>
                                <input type="date" class="form-control" id="filter_date_to" name="date_to">
                            </div>
                            <div class="col-md-3 mt-2">
                                <label>{{ __('main.item') ?? 'الصنف' }}</label>
                                <input type="text" class="form-control" id="filter_item_search" name="item_search" placeholder="{{__('main.product_code')}}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end gap-2 mt-2">
                                <button type="submit" class="btn btn-primary">{{ __('main.search') }}</button>
                                <button type="button" id="filterReset" class="btn btn-secondary">{{ __('main.reset') ?? 'إعادة تعيين' }}</button>
                            </div>
                        </form>
                    </div>
                    <div class="clearfix"><hr></div> 
                    <div class="card-body">
                        <div class="table-responsive hoverable-table">
                            <table class="display w-100 text-nowrap table-bordered text-center" id="salesTable"> 
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{__('main.bill_number')}}</th>
                                        <th>{{__('main.bill_date')}}</th> 
                                        <th>{{__('main.branche')}}</th>  
                                        <th>{{__('main.warehouse')}}</th>
                                        <th>{{__('main.customer')}}</th>
                                        <th>{{__('main.invoice.total')}}</th> 
                                        <th>{{__('main.discount')}}</th> 
                                        <th>{{__('main.tax')}}</th>
                                        <th>{{__('main.amount')}}</th>
                                        <th>{{__('main.remain')}}</th>  
                                        <th>{{__('main.paid')}}</th>  
                                        <th>{{__('main.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody></tbody> 
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div> 

<div class="show_modal">

</div>

@endcan 
@endsection 
@section('js')
<script type="text/javascript">
    function showToast(message){
        var toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-success border-0';
        toast.style.position = 'fixed';
        toast.style.top = '20px';
        toast.style.right = '20px';
        toast.style.zIndex = 9999;
        toast.role = 'alert';
        toast.innerHTML = '<div class="d-flex"><div class="toast-body">'+message+'</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div>';
        document.body.appendChild(toast);
        var bsToast = new bootstrap.Toast(toast,{delay:3000});
        bsToast.show();
        toast.addEventListener('hidden.bs.toast',function(){ toast.remove(); });
    }
          $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var table = $('#salesTable').DataTable({
                processing: true,
                //serverSide: true,
                responsive: true,

                ajax: {
                    url: "{{ route('sales') }}",
                    data: function(d){
                        d.invoice_no = $('#filter_invoice_no').val();
                        d.customer_id = $('#filter_customer_id').val();
                        d.representative_id = $('#filter_representative_id').val();
                        d.branch_id = $('#filter_branch_id').val();
                        d.date_from = $('#filter_date_from').val();
                        d.date_to = $('#filter_date_to').val();
                        d.item_search = $('#filter_item_search').val();
                    }
                },
                columns: [
                    {
                        data: 'id', 
                        name: 'id'
                    },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no'
                    },
                    {
                        data:'created_at',
                        name: 'created_at'
                    }, 
                    {
                        data: 'branch_name',
                        name: 'branch_name'
                    },
                    {
                        data: 'warehouse_name',
                        name: 'warehouse_name'
                    },
                    {
                        data: 'customer_name',
                        name: 'customer_name'
                    },
                    {
                        data: 'net',
                        name: 'net'
                    }, 
                    {
                        data: 'discount',
                        name: 'discount'
                    }, 
                    {
                        data: 'tax',
                        name: 'tax'
                    },
                    {
                        data: 'total',
                        name: 'total'
                    },
    
                    {
                        data: 'remain',
                        name: 'remain'
                    },  
                    {
                        data: 'paid',
                        name: 'paid'
                    },  
                    { 
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                dom: 'lBfrtip',
                buttons: [
                    "copy", "excel", "print", "colvis"
                ],
                "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
                order: [[0, 'desc']]
            });

            $('#filterForm').on('submit', function(e){
                e.preventDefault();
                if(table){
                    table.ajax.reload();
                }
            });
            $('#filterReset').on('click', function(){
                $('#filterForm')[0].reset();
                if(table){
                    table.ajax.reload();
                }
            });

            @if(session('success'))
                showToast("{{ session('success') }}");
            @endif
        });
</script> 
<script type="text/javascript">
    $(document).ready(function() {
    });
    let id = 0 ;
    $(document).ready(function() {
        id = 0;
        $(document).on('click', '.deleteBtn', function(event) {
            id = event.currentTarget.value ;
            event.preventDefault();
            let href = $(this).attr('data-attr');
            $.ajax({
                url: href,
                beforeSend: function() {
                    $('#loader').show();
                },
                // return the result
                success: function(result) {
                    $('#deleteModal').modal("show");
                },
                complete: function() {
                    $('#loader').hide();
                },
                error: function(jqXHR, testStatus, error) {
                    console.log(error);
                    alert("Page " + href + " cannot open. Error:" + error);
                    $('#loader').hide();
                },
                timeout: 8000
            })
        });
        $(document).on('click' , '.cancel-modal' , function (event) {
            $('#deleteModal').modal("hide");
            id = 0 ;
        });
        $(document).on('click' , '.modal-close-btn' , function (event) {
            $('#paymentsModal').modal("hide");
            id = 0 ;
        }); 

        document.title = "{{ __('main.sales_invoices')}}";
    });

    function confirmDelete(){
        let url = "{{ route('deleteUpdate_qnt', ':id') }}";
        url = url.replace(':id', id);
        document.location.href=url;
    }

    function showPayments(id) {
        var route = '{{route('sales_payments',":id")}}';
        route = route.replace(":id",id);

        $.get( route, function( data ) {
            $( ".show_modal" ).html( data );
            $('#paymentsModal').modal('show');
        });
    }

    function addPayments(id) {
        var route = '{{route('add_sales_payments',":id")}}';
        route = route.replace(":id",id);

        $.get( route, function( data ) {
            $( ".show_modal" ).html( data );
            $('#paymentsModal').modal('show');
        });
    }

    function view_sales(id) {
        var route = '{{route('preview_sales',":id")}}';
            route = route.replace(":id",id);

        $.get( route, function( data ) {
            $( ".show_modal" ).html( data );
            $('#paymentsModal').modal('show');
        });
    }


</script>
@endsection 
