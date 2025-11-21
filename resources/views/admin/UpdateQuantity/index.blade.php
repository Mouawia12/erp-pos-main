@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
  
    @can('عرض كمية') 
 
    <!-- End Navbar -->
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0"  id="head-right" >
                        <div class="col-lg-12 margin-tb">
                            <h4  class="alert alert-primary text-center">
                            {{ __('main.update_qnt')}}
                            </h4>
                        </div>
                        
                    </div>
                    <div class="row mt-1 mb-1 text-center justify-content-center align-content-center"> 
                        @can('اضافة صنف')  
                        <a href="{{route('add_update_qnt')}}" type="button" class="btn btn-labeled btn-primary" >
                             
                            <i class="fa fa-plus"></i></span>
                            {{__('main.add_new')}}
                        </a>
                        @endcan  
                    </div> 
                    <div class="clearfix"><hr></div>
                    
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive hoverable-table">
                            <table class="display w-100  text-nowrap table-bordered" id="ItemTable" 
                                   style="text-align: center;">
                                <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">#</th>
                                    <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7 ps-2">{{__('main.bill_date')}}</th>
                                    <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">{{__('main.bill_number')}}</th>
                                    <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">{{__('main.warehouse')}}</th>
                                    <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">{{__('main.user_enter')}}</th>
                                    <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">{{__('main.notes')}}</th>
                                    <th class="text-end text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">{{__('main.actions')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $process)
                                    <tr>
                                        <td class="text-center">{{$process->id}}</td>
                                        <td class="text-center">{{$process->bill_date}}</td>
                                        <td class="text-center">{{$process->bill_number}}</td>
                                        <td class="text-center">{{$process->warehouse ->  name}}</td>
                                        <td class="text-center">{{$process->user -> name}}  {{$process->user -> last_name}}</td>
                                        <td class="text-center">{{$process->notes}}</td>
                                        <td class="text-center">
                                            @can('تعديل كمية') 
                                            <a href="{{route('edit_Update_qnt' , $process -> id)}}">
                                                <button type="button" class="btn btn-labeled btn-info" >
                                                    <span class="btn-label" style="margin-right: 10px;">
                                                    <i class="fa fa-pen"></i></span>{{__('main.edit')}}
                                                </button>
                                            </a> 
                                            @endcan 
                                            @can('حذف كمية') 
                                            <button type="button" class="btn btn-labeled btn-danger deleteBtn " value="{{$process -> id}}">
                                                <span class="btn-label" style="margin-right: 10px;">
                                                <i class="fa fa-trash"></i></span>{{__('main.delete')}}
                                            </button> 
                                            @endcan 

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




<!--   Delte Modal   -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"  data-bs-dismiss="modal"  aria-label="Close" style="color: red; font-size: 20px; font-weight: bold;">
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
    });

    function confirmDelete(){
        let url = "{{ route('deleteUpdate_qnt', ':id') }}";
        url = url.replace(':id', id);
        document.location.href=url;
    }

</script>
@endsection 