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
                            {{ __('main.categories')}}
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
                                        <th>{{__('main.parent')}}</th> 
                                        <th>{{__('main.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @php $row = 1; @endphp
                                @php
                                    $renderTree = function($nodes, $depth = 0) use (&$renderTree, &$row) {
                                        foreach ($nodes as $node) {
                                            $pad = str_repeat('-- ', $depth);
                                            @endphp
                                            <tr>
                                                <td class="text-center">{{ $row++ }}</td>
                                                <td class="text-center">{{ $pad }}{{ $node['name'] }}</td>
                                                <td class="text-center">{{ $node['parent_name'] ?? '-' }}</td>
                                                <td class="text-center">
                                                    @can('تعديل ترميز')
                                                        <button type="button" class="btn btn-labeled btn-info" onclick="EditModal({{ $node['id'] }})">
                                                            <i class="fa fa-pen"></i>
                                                        </button>
                                                    @endcan
                                                    @can('حذف ترميز')
                                                        <button type="button" class="btn btn-labeled btn-danger deleteBtn" id="{{ $node['id'] }}">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    @endcan
                                                </td>
                                            </tr>
                                            @php
                                            if (!empty($node['children'])) {
                                                $renderTree($node['children'], $depth + 1);
                                            }
                                        }
                                    };
                                    $renderTree($tree);
                                @endphp
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
<div class="modal fade" id="createModal" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <label class="modelTitle"> {{__('main.categories')}}</label>
                <button type="button" class="close modal-close-btn"  data-bs-dismiss="modal"  aria-label="Close" >
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="paymentBody">
                <form   method="POST" action="{{ route('storeCategory') }}"
                        enctype="multipart/form-data" >
                    @csrf
                    <input type="text"  id="id" name="id"
                                       class="form-control"
                                       placeholder="{{ __('main.code') }}"  hidden=""/>

                    <div class="row">
                        <div class="col-md-6" >
                            <div class="form-group">
                                <label>{{ __('main.name') }} <span class="text-danger">*</span> </label>
                                <input type="text"  id="name" name="name"
                                       class="form-control"
                                       placeholder="{{ __('main.name') }}"  />
                            </div>
                        </div>
                        <div class="col-md-6" >
                            <div class="form-group">
                                <label>{{ __('main.parent') }} <span class="text-danger">*</span> </label>
                                <select class="form-control"
                                        name="parent_id" id="parent_id">
                                    <option value="">حدد الاختيار</option>
                                    <option value ="0">رئيسي</option>
                                    @php
                                        $renderOptions = function($nodes, $depth = 0) use (&$renderOptions) {
                                            foreach($nodes as $node){
                                                $pad = str_repeat('-- ', $depth);
                                                echo '<option value="'.$node['id'].'">'.$pad.$node['name'].'</option>';
                                                if(!empty($node['children'])){
                                                    $renderOptions($node['children'], $depth+1);
                                                }
                                            }
                                        };
                                        $renderOptions($tree);
                                    @endphp
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row"> 
                        <div class="col-md-12" >
                            <div class="form-group">
                                <label>الضريبية الانتقائية<span class="text-danger">*</span> </label>
                                <select  id="tax_excise" name="tax_excise" class="js-example-basic-single w-100 @error('tax_excise') is-invalid @enderror" required>
                                    <option value="0">بدون ضريبة انتقائية</option>
                                    @foreach($tax_excises as $tax_excise)
                                        <option value="{{$tax_excise->rate}}">{{$tax_excise->name .' => % '.$tax_excise->rate  }}</option>
                                    @endforeach
                                </select> 
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-8" >
                            <div class="form-group">
                                <label>{{ __('main.description') }} <span class="text-danger">*</span> </label>
                                <textarea type="text"  id="description" name="description" class="form-control" placeholder="{{ __('main.description') }}"></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>slug <span class="text-danger">*</span> </label>
                                <input type="text"  id="slug" name="slug"
                                       class="form-control"
                                       placeholder="slug"  />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="custom-file">
                                <input type="file" class="form-control custom-file-input" id="image_url"   name="image_url"  accept="image/png, image/jpeg" >
                                <label class="custom-file-label" for="img" id="path">{{__('main.img_choose')}}   <span style="color:red;">*</span></label>
                            </div> 
                        </div>
                        <div class="col-md-6 text-right">
                            <img src="../assets/img/photo.png" id="profile-img-tag" width="150px" height="150px" class="profile-img"/>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6" style="display: block; margin: 20px auto; text-align: center;">
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
                            <span class="btn-label" style="margin-right: 10px;"><i class="fa fa-check"></i></span>{{__('main.confirm_btn')}}
                        </button>
                    </div>
                    <div class="col-6 text-center">
                        <button type="button" class="btn btn-labeled btn-secondary cancel-modal"  >
                            <span class="btn-label" style="margin-right: 10px;"><i class="fa fa-close"></i></span>{{__('main.cancel_btn')}}
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
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#profile-img-tag').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#image_url").change(function(){
        readURL(this);
    });
</script>

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
                    $(".modal-body #name").val("");
                    $(".modal-body #code").val("");
                    $(".modal-body #slug").val("");
                    $(".modal-body #description").val("");
                    $(".modal-body #parent_id").val(""); 
                    $(".modal-body #image_url").val("");
                    //$(".modal-body #tax_excise").prop('checked' , 0);
                    $(".modal-body #tax_excise").val( 0 );
                    $(".modal-body #profile-img-tag").attr('src' , '../assets/img/photo.png' );
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

        document.title = "{{ __('main.categories')}}";
    });
    function confirmDelete(){
        let url = "{{ route('deleteCategory', ':id') }}";
        url = url.replace(':id', id);
        document.location.href=url;
    }
    function EditModal(id){
        let url = "{{ route('getCategory', ':id') }}";
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
                            var img = '{{env('APP_URL')}}/uploads/images/Category/' + response.image_url ;
                            $(".modal-body #profile-img-tag").attr('src' , img );
                            $(".modal-body #name").val(response.name);
                            $(".modal-body #code").val(response.code);
                            $(".modal-body #slug").val(response.slug);
                            $(".modal-body #description").val(response.description);
                            $(".modal-body #parent_id").val(response.parent_id).trigger("change");
                            $(".modal-body #tax_excise").val(response.tax_excise).trigger("change");
                            $(".modal-body #test").val(response.tax_excise);
                            $(".modal-body #id").val(response.id);
                          
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
