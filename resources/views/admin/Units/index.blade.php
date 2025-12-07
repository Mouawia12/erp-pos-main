@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
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
                            {{ __('main.units')}}
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
                                        <th>{{__('main.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($units as $unit) 
                                    <tr> 
                                        <td class="text-center">{{$unit -> id}}</td>
                                        <td class="text-center">{{$unit -> name}}</td>  
                                        <td class="text-center">
                                        @can('تعديل ترميز')
                                            <button type="button" class="btn btn-labeled btn-info " onclick="EditModal({{$unit -> id}})">
                                                <i class="fa fa-pen"></i> 
                                            </button>
                                        @endcan 
                                        @can('حذف ترميز')
                                            <button type="button" class="btn btn-labeled btn-danger deleteBtn "  id="{{$unit -> id}}" data-id="{{$unit -> id}}">
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
     <!--/div-->
<!-- Logout Modal-->

<!--   Create Modal   -->
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <label class="modelTitle"> {{__('main.units')}}</label>
                <button type="button" class="close modal-close-btn"  data-bs-dismiss="modal"  aria-label="Close" >
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="paymentBody">
                <form id="unitForm"  method="POST" action="{{ route('storeUnit') }}"
                        enctype="multipart/form-data" >
                    @csrf
                    <input type="hidden" name="id" id="id" value="0"/> 
                    <div class="row">
                        <div class="col-12 " >
                            <div class="form-group">
                                <label>{{ __('main.code') }}</label>
                                <input type="text"  id="code" name="code"
                                       class="form-control"
                                       placeholder="{{ __('main.code') }}"  /> 

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('main.unit_name_ar') }} <span class="text-danger">*</span></label>
                                <input type="text" id="name_ar" name="name_ar"
                                       class="form-control"
                                       placeholder="{{ __('main.unit_name_ar') }}" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('main.unit_name_en') }} <span class="text-danger">*</span></label>
                                <input type="text" id="name_en" name="name_en"
                                       class="form-control"
                                       placeholder="{{ __('main.unit_name_en') }}" required />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6" style="display: block; margin: 20px auto; text-align: center;">
                            <button type="submit" class="btn btn-labeled btn-primary"  >
                                {{__('main.save_btn')}}
                            </button>
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
                <img src="../../assets/img/warning.png" class="alertImage">
                <label class="alertTitle">{{__('main.delete_alert')}}</label>
                <br> <label  class="alertSubTitle" id="modal_table_bill"></label>
                <div class="row">
                    <div class="col-6 text-center">
                        <button type="button" class="btn btn-labeled btn-primary" onclick="confirmDelete()">
                            <span class="btn-label" style="margin-right: 10px;"><i class="fa fa-check"></i></span>{{__('main.confirm_btn')}}</button>
                    </div>
                    <div class="col-6 text-center">
                        <button type="button" class="btn btn-labeled btn-secondary cancel-modal"  >
                            <i class="fa fa-close"> 
                            </i> {{__('main.cancel_btn')}}
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
    $(document).ready(function()
    {
        id = 0 ;
        $(document).on('click', '#createButton', function(event) {
            id = 0 ;
            event.preventDefault();
            const $form = $('#unitForm');
            if ($form.length) {
                $form.trigger('reset');
                $form.find('#id').val(0);
            }
            $('#createModal').modal("show");
        });
        $(document).on('click', '.deleteBtn', function(event) {
             id = event.currentTarget.dataset.id || event.currentTarget.id ;
            event.preventDefault();
            $('#deleteModal').modal("show");
        });
 
        $(document).on('click' , '.cancel-modal' , function (event) {
            $('#deleteModal').modal("hide");
            id = 0 ;
        });
        $(document).on('click' , '.modal-close-btn' , function (event) {
            $('#createModal').modal("hide");
            id = 0 ;
        });

        document.title = "{{ __('main.units')}}";
    });

    function confirmDelete(){
        let url = "{{ route('deleteUnit', ':id') }}";
        url = url.replace(':id', id);
        document.location.href=url;
    }

    function EditModal(id){
        let url = "{{ route('getUnit', ':id') }}";
        url = url.replace(':id', id);

        $.ajax({
            type:'get',
            url:url,
            dataType: 'json',
            beforeSend: function() {
                $('#loader').show();
            },
            complete: function() {
                $('#loader').hide();
            },
            error: function() {
                alert('حدث خطأ غير متوقع، حاول مجدداً');
                $('#loader').hide();
            },
            success:function(response){
                if(response){
                    $('#createModal').modal("show");
                    $(".modal-body #name_ar").val( response.name_ar ?? "" );
                    $(".modal-body #name_en").val( response.name_en ?? "" );
                    $(".modal-body #code").val( response.code ?? "" );
                    $(".modal-body #id").val( response.id ?? 0 );
                }
            }
        });
    }
</script>

@endsection 
 
