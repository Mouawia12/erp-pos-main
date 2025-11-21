@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
    @can('عرض سند صرف')
    <!-- End Navbar -->
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0"  id="head-right" >
                        <div class="col-lg-12 margin-tb">
                            <h4  class="alert alert-primary text-center">
                            {{ __('main.box_expenses_list')}}
                            </h4>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="row mt-1 mb-1 text-center justify-content-center align-content-center"> 
                        @can('اضافة سند صرف')
                        <button type="button" class="btn btn-labeled btn-primary" id="createBtn">
                            <span class="btn-label" style="margin-right: 10px;">
                                <i class="fa fa-plus"></i>
                            </span>
                            {{__('main.add_new')}}
                        </button>
                        @endcan 
                    </div> 
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive hoverable-table">
                            <table class="display w-100 text-nowrap table-bordered" id="example1" 
                                   style="text-align: center;">  
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{__('main.bill_date')}}</th>
                                        <th>{{__('main.bill_number')}}</th>
                                        <th>{{__('main.warehouse')}}</th>
                                        <th>{{__('main.expenses_category')}}</th>
                                        <th>{{__('main.paid')}}</th>
                                        <th>{{__('main.payment_type')}}</th>
                                        <th>{{__('main.user')}}</th>
                                        <th>{{__('main.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($bills as $process)
                                    <tr>
                                        <td>{{$process->id}}</td>
                                        <td>{{$process->date}}</td>
                                        <td>{{$process->bill_number}}</td>
                                        <td>{{$process->warehouse_name}}</td>
                                        <td>{{$process->category_name}}</td>
                                        <td>{{$process->amount}}</td>
                                        <td>
                                            @if($process->payment_type == 0)
                                                {{__('main.CC')}}
                                            @elseif($process->payment_type == 1) 
                                                {{__('main.Cash')}}
                                            @elseif($process->payment_type == 2)
                                                {{__('main.Transfer_Net')}}
                                            @endif
                                        </td>
                                        <td>{{$process->user_name}}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-primary dropdown-toggle"
                                                        data-toggle="dropdown">
                                                    <i class="fa fa-wrench"></i>
                                                    {{__('main.actions')}}
                                                </button>
                                                <div class="dropdown-menu">
                                                    @can('تعديل سند صرف')
                                                        <a class="dropdown-item"
                                                           href="javascript:;" onclick="showExpense({{$process -> id}})"> 
                                                            {{__('main.preview')}}  
                                                        </a>
                                                    @endcan 
                                                    @can('حذف سند صرف')
                                                        <a class="dropdown-item border-radius-md deleteBtn"  id="{{$process->id}}"> 
                                                            {{__('main.delete')}}     
                                                        </a>
                                                    @endcan 
                                                </div>
                                            </div>
 
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
<!--   Core JS Files   --> 
<div class="show_modal">

</div>
<!--   Delte Modal   -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
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
                            <span class="btn-label" style="margin-right: 10px;"><i class="fa fa-check"></i></span>{{__('main.confirm_btn')}}</button>
                    </div>
                    <div class="col-6 text-center">
                        <button type="button" class="btn btn-labeled btn-secondary cancel-modal"  >
                            <span class="btn-label" style="margin-right: 10px;"><i class="fa fa-close"></i></span>{{__('main.cancel_btn')}}</button>
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
    $(document).ready(function() {
        $('#table').DataTable();
    });
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
            $('#createModal').modal("hide");
            id = 0 ;
        });
        $(document).on('click' , '.modal-close-btn' , function (event) {
            $('#expensesModal').modal("hide");
            id = 0 ;
        });
        

        $('#createBtn').click( function (event){
            var route = '{{route('create_expenses')}}';
            console.log(route);
            $.get( route, function( data ) {
                $( ".show_modal" ).html( data );
                $('#expensesModal').modal('show');
            });
        });

    });

    function confirmDelete(){
        let url = "{{ route('delete_purchase', ':id') }}";
        url = url.replace(':id', id);
        document.location.href=url;
    }

    function showExpense(id) {
        var route = '{{route('view_expenses',":id")}}';
        route = route.replace(":id",id);

        $.get( route, function( data ) {
            $( ".show_modal" ).html( data );
            $('#expensesModal').modal('show');
        });
    }

    function addExpense() {
        var route = '{{route('create_expenses')}}';
        console.log(route);
        $.get( route, function( data ) {
            $( ".show_modal" ).html( data );
            $('#expensesModal').modal('show');
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
