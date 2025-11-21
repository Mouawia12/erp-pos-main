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

                ajax: "{{ route('sales') }}",
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
            }).buttons().container().appendTo('#ItemTable_wrapper .col-md-6:eq(0)');
        });
</script> 
<script type="text/javascript">
    $(document).ready(function() {
        $('#table').DataTable();
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
