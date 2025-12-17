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
    @can('عرض مردود مبيعات') 
    <!-- End Navbar -->
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0"  id="head-right" >
                        <div class="col-lg-12 margin-tb">
                            <h4  class="alert alert-primary text-center">
                                {{ __('main.sales.return')}}
                            </h4>
                        </div>  
                    </div> 
                    <div class="row mt-1 mb-1 text-center justify-content-center align-content-center"> 
                       @can('اضافة مردود مبيعات')   
                        <a href="{{route('sales.return.create')}}" type="button" class="btn btn-labeled btn-info"> 
                            <i class="fa fa-plus"></i></span>
                            {{__('main.add_new')}}
                        </a>
                        @endcan  
                    </div> 
                    
                    <div class="card-body">
                        <div class="table-responsive hoverable-table">
                            <table class="display w-100 text-nowrap table-bordered text-center" id="example1"> 
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{__('main.bill_number')}}</th>
                                        <th>{{__('main.bill_date')}}</th> 
                                        <th>{{__('main.branche')}}</th>  
                                        <th>{{__('main.warehouse')}}</th>
                                        <th>{{__('main.customer')}}</th>
                                        <th>{{__('main.total')}}</th> 
                                        <th>{{__('main.discount')}}</th> 
                                        <th>{{__('main.tax')}}</th>  
                                        <th>{{__('main.amount')}}</th>
                                        <th>{{__('main.remain')}}</th> 
                                        <th>{{__('main.paid')}}</th>   
                                        <th>{{__('main.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $process)
                                    <tr>
                                        <td>{{$loop->index+1}}</td>
                                        <td>{{$process->invoice_no}}</td>
                                        <td>{{\Carbon\Carbon::parse($process->date)}}</td> 
                                        <td>{{$process->branch->branch_name}}</td>
                                        <td>{{$process->warehouse->name}}</td>
                                        <td>{{$process->customer->name}}</td>
                                        <td>{{$process->net * -1}}</td>
                                        <td>{{$process->discount ? $process->discount * -1 : 0}}</td> 
                                        <td>{{$process->tax * -1}}</td>  
                                        <td>{{$process->total *-1}}</td>  
                                        <td>{{$process->paid ? $process->paid * -1:0}}</td>
                                        <td>{{($process->net - $process->paid)*-1}}</td>
                                        <td> 
                                            <a type="button" class="btn btn-info"
                                              href="{{route('print.sales',$process->id)}}"> 
                                               عرض الفاتورة 
                                            </a>
                                            <a type="button" class="btn btn-secondary mt-1"
                                               href="{{route('print.sales',['id'=>$process->id,'format'=>'a5'])}}">
                                                طباعة A5
                                            </a>
                                        </td> 
                                    </tr>
                                @endforeach
                                </tbody>
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
    $(document).ready(function() {
        id = 0;
        @if(session('success'))
            showToast(@json(session('success')));
        @endif
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
