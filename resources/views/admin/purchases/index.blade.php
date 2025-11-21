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

    @can('عرض مشتريات')

    <!-- End Navbar -->
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0"  id="head-right" >
                        <div class="col-lg-12 margin-tb">
                            <h4  class="alert alert-primary text-center">
                                {{ __('main.purchases')}}
                            </h4>
                        </div> 
                    </div>
                    <div class="row mt-1 mb-1 text-center justify-content-center align-content-center"> 
                        @can('اضافة مشتريات')   
                            <a href="{{route('add_purchase')}}" type="button" class="btn btn-labeled btn-info" >
                                <i class="fa fa-plus"></i></span>
                                {{__('main.add_new')}}
                            </a>
                        @endcan  
                    </div> 
                    <div class="clearfix"><hr></div>
                    
                    <div class="card-body">
                        <div class="table-responsive hoverable-table">
                            <table class="display w-100 text-nowrap table-bordered" id="example1"> 
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{__('main.bill_number')}}</th>
                                        <th>{{__('main.bill_date')}}</th> 
                                        <th>{{__('main.branche')}}</th>  
                                        <th>{{__('main.warehouse')}}</th>
                                        <th>{{__('main.supplier_name')}}</th>
                                        <th>{{__('main.invoice.total')}}</th>
                                        <th>{{__('main.tax')}}</th>
                                        <th>{{__('main.total')}}</th>  
                                        <th>{{__('main.paid')}}</th>
                                        <th>{{__('main.remain')}}</th>
                                        <th>{{__('main.InvoiceType')}}</th>
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
                                        <td>{{$process->warehouse_name}}</td>
                                        <td>{{$process->customer_name}}</td>
                                        <td>{{$process->net}}</td>
                                        <td>{{$process->tax}}</td>
                                        <td>{{$process->total}}</td>  
                                        <td>{{$process->paid}}</td>
                                        <td>{{$process->net - $process->paid}}</td>
                                        <td>
                                            @if($process->net > 0)
                                                <span class="text-success">
                                                    [ {{__('main.purchase')}} ]
                                                </span>
                                            @else
                                                <span class="text-success">
                                                    [ {{__('main.return_purchase')}} ]
                                                </span>
                                            @endif
                                        </td>
                                        <td> 
                                           @can('عرض سند صرف') 
                                            <a  href="javascript:;" onclick="showPayments({{$process->id}})"
                                                class="btn btn-success"> 
                                                <i class="fa fa-eye"></i>
                                                {{__('main.view_payments')}} 
                                            </a> 
                                            @endcan
                                            @can('اضافة سند صرف') 
                                            @if(abs($process->net) - abs($process->paid) > 0)
                                                <a  class="btn btn-primary"
                                                    href="javascript:;" onclick="addPayments({{$process->id}})"> 
                                                    {{__('main.add_payment')}}
                                                </a>  
                                            @endif
                                            @endcan
                                            @can('اضافة مردود مشتريات') 
                                            @if($process -> net > 0) 
                                                <a  class="btn btn-secondary"
                                                    href="{{route('return_purchase',$process->id)}}"> 
                                                    {{__('main.return_purchase')}}
                                                </a> 
                                            @endif
                                            @endcan
                                            <a  class="btn btn-info"
                                                href="javascript:;" onclick="view_purchase({{$process->id}})"> 
                                                {{__('main.preview')}}  
                                            </a>
                                            <!--
                                            <a  class="btn btn-danger" id="{{$process->id}}">
                                                {{__('main.delete')}} 
                                            </a>  
                                            -->
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
<!--   Delte Modal   -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close cancel-modal" data-bs-dismiss="modal" aria-label="Close" style="color: red; font-size: 20px; font-weight: bold;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="smallBody">
                <img src="../assets/img/warning.png" class="alertImage">
                <label class="alertTitle">{{__('main.delete_alert')}}</label>
                <br> <label  class="alertSubTitle" id="modal_table_bill"></label>
                <div class="row">
                    <div class="col-6 text-center">
                        <button type="button" class="btn btn-labeled btn-primary" onclick="confirmDelete()">
                            <span class="btn-label" style="margin-right: 10px;"><i class="fa fa-check"></i></span>
                            {{__('main.confirm_btn')}}
                        </button>
                    </div>
                    <div class="col-6 text-center">
                        <button type="button" class="btn btn-labeled btn-secondary cancel-modal"  >
                            <span class="btn-label" style="margin-right: 10px;"><i class="fa fa-close"></i></span>
                            {{__('main.cancel_btn')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endcan 
@endsection 
@section('js')
<script type="text/javascript"> 
    let id = 0 ;
    $(document).ready(function() {
        id = 0;
        $(document).on('click', '.deleteBtn', function(event) {
            id = event.currentTarget.id ;
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
        document.title = " {{ __('main.purchases')}}";
    });

    function confirmDelete(){
        let url = "{{ route('delete_purchase', ':id') }}";
        url = url.replace(':id', id);
        document.location.href=url;
    }

    function showPayments(id) {
        var route = '{{route('purchases_payments',":id")}}';
        route = route.replace(":id",id);

        $.get( route, function( data ) {
            $( ".show_modal" ).html( data );
            $('#paymentsModal').modal('show');
        });
    }

    function addPayments(id) {
        var route = '{{route('add_purchases_payments',":id")}}';
        route = route.replace(":id",id);

        $.get( route, function( data ) {
            $( ".show_modal" ).html( data );
            $('#paymentsModal').modal('show');
        });
    }

    function view_purchase(id) {
        console.log(id);
        var route = '{{route('preview_purchase',":id")}}';
        route = route.replace(":id",id);

        $.get( route, function( data ) {
            $( ".show_modal" ).html( data );
            $('#paymentsModal').modal('show');
        });
    }

</script>
@endsection 
