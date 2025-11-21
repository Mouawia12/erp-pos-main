@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
    @can('عرض الاعدادات')
    <!-- End Navbar -->
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0"  id="head-right" >
                        <div class="col-lg-12 margin-tb">
                            <h4  class="alert alert-primary text-center">
                            {{ __('main.warehouses')}}
                            </h4>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="row mt-1 mb-1 text-center justify-content-center align-content-center"> 
                        @can('اضافة الاعدادات')
                        <button type="button" class="btn btn-labeled btn-primary " id="createButton">
                            <span class="btn-label" style="margin-right: 10px;">
                            <i class="fa fa-plus"></i></span>
                            {{__('main.add_new')}}
                        </button>
                        @endcan 
                    </div> 
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive hoverable-table">
                            <table class="display w-100  text-nowrap table-bordered" id="example1" 
                                   style="text-align: center;">  
                                <thead>
                                <tr>
                                    <th>#</th> 
                                    <th>{{__('main.name')}}</th>
                                    <th>{{__('main.phone')}}</th>
                                    <th>{{__('main.email')}}</th>
                                    <th>{{__('main.address')}}</th>
                                    <th>{{__('main.branche')}}</th>
                                    <th>{{__('main.status')}}</th>
                                    <th>{{__('main.actions')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($warehouses as $warehouse)
                                <tr>
                                    <td>{{$warehouse -> id}}</td> 
                                    <td>{{$warehouse -> name}}</td>
                                    <td>{{$warehouse -> phone}}</td>
                                    <td>{{$warehouse -> email}}</td>
                                    <td>{{$warehouse -> address}}</td>
                                    <td>{{$warehouse -> branch->branch_name}}</td>
                                    <td> 
                                        <input type="checkbox" name="status[]" 
                                        @if($warehouse->status == 1)
                                            checked 
                                        @endif 
                                        value="{{ $warehouse->status}}">
                                    </td>

                                    <td>
                                        @can('تعديل الاعدادات')
                                        <button type="button" class="btn btn-labeled btn-info" onclick="EditModal({{$warehouse -> id}})">
                                            <span class="btn-label" style="margin-right: 10px;"><i class="fa fa-pen"></i></span>
                                            {{__('main.edit')}}
                                        </button>
                                        @endcan 
                                        @can('حذف الاعدادات')
                                        <button type="button" class="btn btn-labeled btn-danger deleteBtn "  id="{{$warehouse -> id}}">
                                            <span class="btn-label" style="margin-right: 10px;"><i class="fa fa-trash"></i></span>
                                            {{__('main.delete')}}
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
<!--   Core JS Files   -->


<!--   Create Modal   -->
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <label class="modelTitle"> {{__('main.warehouses')}}</label>
                <button type="button" class="close modal-close-btn"  data-bs-dismiss="modal"  aria-label="Close" >
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="paymentBody">
                <form   method="POST" action="{{ route('storeWarehouse') }}"
                        enctype="multipart/form-data" >
                    @csrf
                    <input type="hidden" name="id" id="id" value="0"/>
                    <div class="row"> 
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="d-block">
                                {{ __('main.branche') }}<span class="text-danger">*</span> 
                                </label>
                                @if(empty(Auth::user()->branch_id))
                                    <select required  class="form-control" name="branch_id" id="branch_id">
                                        <option value="0">حدد الاختيار</option>
                                        @foreach($branches as $branch)
                                            <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input class="form-control" type="text" readonly
                                           value="{{Auth::user()->branch->branch_name}}"/>
                                    <input required class="form-control" type="hidden" id="branch_id"
                                           name="branch_id"
                                           value="{{Auth::user()->branch_id}}"/>
                                @endif 

                            </div>
                        </div>
                        <div class="col-md-6" >
                            <div class="form-group">
                                <label>{{ __('main.name') }} <span class="text-danger">*</span> </label>
                                <input type="text"  id="name" name="name"
                                       class="form-control"
                                       placeholder="{{ __('main.name') }}"  />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6" >
                            <div class="form-group">
                                <label>{{ __('main.phone') }} <span class="text-danger">*</span> </label>
                                <input type="text" id="phone" name="phone"
                                       class="form-control"
                                       placeholder="{{ __('main.phone') }}"  />
                            </div>
                        </div>
                        <div class="col-md-6" >
                            <div class="form-group">
                                <label>{{ __('main.email') }} <span class="text-danger">*</span> </label>
                                <input type="text" id="email" name="email"
                                       class="form-control"
                                       placeholder="{{ __('main.email') }}"  />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" >
                            <div class="form-group">
                                <label>{{ __('main.address') }} <span class="text-danger">*</span> </label>
                                <textarea type="text"  id="address" name="address" class="form-control" placeholder="{{ __('main.address') }}"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6" >
                            <div class="form-group">
                                <label>{{ __('main.serial_prefix') }} <span class="text-danger">*</span> </label>
                                <input type="text"  id="serial_prefix" name="serial_prefix"
                                       class="form-control"
                                       placeholder="{{ __('main.serial_prefix') }}"/>
                            </div>
                        </div>
                    </div>
                    <div class="row" hidden>
                        <div class="col-md-6" >
                            <div class="form-group">
                                <label>{{ __('main.vat_no') }} <span class="text-danger">*</span> </label>
                                <input type="text" id="tax_number" name="tax_number"
                                       class="form-control"
                                       placeholder="{{ __('main.vat_no') }}"/>
                            </div>
                        </div>
                        <div class="col-md-6" >
                            <div class="form-group">
                                <label>{{ __('main.commercial_register') }} <span class="text-danger">*</span> </label>
                                <input type="text" id="commercial_registration" name="commercial_registration"
                                       class="form-control"
                                       placeholder="{{ __('main.commercial_register') }}"/>
                            </div>
                        </div>
                    </div> 

                    <div class="row">
                        <div class="col-md-12 text-center">
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
                <img src="../assets/img/warning.png" class="alertImage">
                <label class="alertTitle">{{__('main.delete_alert')}}</label>
                <br> <label  class="alertSubTitle" id="modal_table_bill"></label>
                <div class="row">
                    <div class="col-6 text-center">
                        <button type="button" class="btn btn-labeled btn-primary" onclick="confirmDelete()">
                            <i class="fa fa-check"></i>{{__('main.confirm_btn')}}
                        </button>
                    </div>
                    <div class="col-6 text-center">
                        <button type="button" class="btn btn-labeled btn-secondary cancel-modal"  >
                            <i class="fa fa-close"></i>{{__('main.cancel_btn')}}
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
                    $(".modal-body #code").val( "" );
                    $(".modal-body #phone").val( "" );
                    $(".modal-body #email").val( "" );
                    $(".modal-body #address").val( "" );
                    $(".modal-body #tax_number").val( "" );
                    $(".modal-body #commercial_registration").val( "" );
                    $(".modal-body #serial_prefix").val( "" );
                    @if(empty(Auth::user()->branch_id))
                        $(".modal-body #branch_id").val(0).trigger("change");  
                    @endif
                    $(".modal-body #id").val(0);
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
        document.title = "{{ __('main.warehouses')}}";

    });
    function confirmDelete(){
        let url = "{{ route('deleteWarehouse', ':id') }}";
        url = url.replace(':id', id);
        document.location.href=url;
    }
    function EditModal(id){
        $.ajax({
            type:'get',
            url:'getWarehouse' + '/' + id,
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
                            $(".modal-body #code").val( response.code );
                            $(".modal-body #symbol").val( response.symbol);
                            $(".modal-body #phone").val( response.phone );
                            $(".modal-body #email").val(  response.email );
                            $(".modal-body #address").val(  response.address);
                            $(".modal-body #tax_number").val( response.tax_number );
                            $(".modal-body #commercial_registration").val( response.commercial_registration );
                            $(".modal-body #serial_prefix").val( response.serial_prefix );
                            $(".modal-body #branch_id").val(response.branch_id).trigger("change");  
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
                } else {

                }
            }
        });
    }
</script>
@endsection
 
