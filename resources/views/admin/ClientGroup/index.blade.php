@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
    @can('عرض ترميز')
    <!-- End Navbar -->
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0"  id="head-right" >
                        <div class="col-lg-12 margin-tb">
                            <h4  class="alert alert-primary text-center">
                            {{ __('main.c_groups')}}
                            </h4>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="row mt-1 mb-1 text-center justify-content-center align-content-center"> 
                        @can('اضافة ترميز')
                        <button type="button" class="btn btn-labeled btn-info" id="createButton">
                            <span class="btn-label" style="margin-right: 10px;">
                            <i class="fa fa-plus"></i></span>
                            {{__('main.add_new')}}
                        </button>
                        @endcan 
                    </div> 
                    <div class="clearfix"><hr></div> 

                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive hoverable-table">
                            <table class="display w-100  text-nowrap table-bordered" id="example1" 
                                   style="text-align: center;">  
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{__('main.name')}}</th>
                                        <th>{{__('main.discount_percentage')}}</th>
                                        <th>{{__('main.sell_with_cost')}}</th>
                                        <th>{{__('main.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($groups as $group)
                                <tr>
                                    <td class="text-center">{{$group -> id}}</td>
                                    <td class="text-center">{{$group -> name}}</td>
                                    <td class="text-center">{{$group -> discount_percentage}} %</td>
                                    <td class="text-center">{{$group -> sell_with_cost == 0 ? __('main.false_val') : __('main.true_val')}}</td>
                                    <td class="text-center">
                                        @can('تعديل ترميز')
                                        <button type="button" class="btn btn-labeled btn-info " onclick="EditModal({{$group -> id}})">
                                                <i class="fa fa-pen"></i> 
                                        </button>
                                        @endcan 
                                        @can('حذف ترميز')
                                        <button type="button" class="btn btn-labeled btn-danger deleteBtn "  id="{{$group -> id}}">
                                            <i class="fa fa-trash"></i>
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


<!--   Create Modal   -->
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <label class="modelTitle"> {{__('main.c_groups')}}</label>
                <button type="button" class="close modal-close-btn"  data-bs-dismiss="modal"  aria-label="Close" >
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="paymentBody">
                <form   method="POST" action="{{ route('storeClientGroup') }}"
                        enctype="multipart/form-data" >
                    @csrf

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('main.name') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="text"  id="name" name="name"
                                       class="form-control"
                                       placeholder="{{ __('main.name') }}"  />
                                <input type="text"  id="id" name="id"
                                       class="form-control"
                                       placeholder="{{ __('main.code') }}"  hidden=""/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 " >
                            <div class="form-group">
                                <label>{{ __('main.discount_percentage') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="number" step="any"  id="discount_percentage" name="discount_percentage"
                                       class="form-control"
                                       placeholder="{{ __('main.discount_percentage') }}"  />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 " >
                            <div class="form-group checkRow">
                                <label>{{ __('main.sell_with_cost') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="checkbox"   id="sell_with_cost" name="sell_with_cost"
                                       class="form-check"/>
                            </div>
                        </div>
                        <div class="col-6" >
                            <div class="form-group checkRow" >
                                <label>{{ __('main.enable_discount') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="checkbox"   id="enable_discount" name="enable_discount"
                                       class="form-check" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6" style="display: block; margin: 20px auto; text-align: center;">
                            <button type="submit" class="btn btn-labeled btn-primary"  >
                                {{__('main.save_btn')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
    let id = 0 ;
    $(document).ready(function()
    {
        id = 0 ;
        $(document).on('click', '#createButton', function(event) {
            id = 0 ;
            event.preventDefault();
            let href = $(this).attr('data-attr');
            $.ajax({
                url: href,
                beforeSend: function() {
                    $('#loader').show();
                },
                // return the result
                success: function(result) {
                    $('#createModal').modal("show");
                    $(".modal-body #name").val( "" );
                    $(".modal-body #discount_percentage").val( "" );
                    $(".modal-body #sell_with_cost").prop('checked' , 0);
                    $(".modal-body #enable_discount").prop('checked' , 0);
                    $(".modal-body #id").val( 0 );
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


    });
    function confirmDelete(){
        let url = "{{ route('deleteClientGroup', ':id') }}";
        url = url.replace(':id', id);
        document.location.href=url;
    }
    function EditModal(id){
        let url = "{{ route('getClientGroup', ':id') }}";
        url = url.replace(':id', id);
        $.ajax({
            type:'get',
            url:url,
            dataType: 'json',

            success:function(response){
                console.log(response);
                if(response){
                    let href = $(this).attr('data-attr');
                    $.ajax({
                        url: href,
                        beforeSend: function() {
                            $('#loader').show();
                        },
                        // return the result
                        success: function(result) {
                            $('#createModal').modal("show");
                            $(".modal-body #name").val( response.name );
                            $(".modal-body #discount_percentage").val(response.discount_percentage);
                            $(".modal-body #sell_with_cost").prop('checked' , response.sell_with_cost);
                            $(".modal-body #enable_discount").prop('checked' , response.enable_discount);
                            $(".modal-body #id").val( response.id );

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
                } 
            }
        });
    }
</script>
@endsection 
