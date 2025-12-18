@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
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
<!-- row opened -->
<div class="row row-sm">
    <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="col-lg-12 margin-tb">
                        <h4  class="alert alert-primary text-center">
                          {{__('main.companyInfo')}}
                        </h4>
                    </div>
                    <div class="clearfix"></div>
                </div>  
                <div class="card-body px-0 pt-0 pb-2"> 
                    <div class="card shadow mb-4"> 
                        <div class="card-body">

                            <form   method="POST" action="{{ route('storeCompanyInfo') }}"
                                    enctype="multipart/form-data" >
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('الاسم عربي') }} <span class="text-danger">*</span> </label>
                                            <input type="text"  id="name_ar" name="name_ar"
                                                   class="form-control @error('name_ar') is-invalid @enderror"
                                                   placeholder="{{ __('main.name_ar') }}"  value="{{ old('name_ar', $info->name_ar ?? '') }}" />
                                            <input type="text"  id="id" name="id"
                                                   class="form-control"
                                                   placeholder="{{ __('main.code') }}"  hidden=""   @if($info) value="{{$info -> id}}" @endif/>
                                            @error('name_ar')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('الاسم انجليزي') }} <span class="text-danger">*</span> </label>
                                            <input type="text"  id="name_en" name="name_en"
                                                   class="form-control @error('name_en') is-invalid @enderror"
                                                   placeholder="{{__('main.name_en')}}"  value="{{ old('name_en', $info->name_en ?? '') }}" />
                                            @error('name_en')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                        </div>
                                    </div> 
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('المجال عربي') }} <span class="text-danger">*</span> </label>
                                            <input type="text"  id="faild_ar" name="faild_ar"
                                                   class="form-control @error('faild_ar') is-invalid @enderror"
                                                   placeholder="{{ __('main.field_ar') }}"  value="{{ old('faild_ar', $info->faild_ar ?? '') }}" />
                                            @error('faild_ar')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                         
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('المجال انجليزي') }} <span class="text-danger">*</span> </label>
                                            <input type="text"  id="faild_en" name="faild_en"
                                                   class="form-control @error('faild_en') is-invalid @enderror"
                                                   placeholder="{{__('main.field_en')}}"  value="{{ old('faild_en', $info->faild_en ?? '') }}" />
                                            @error('faild_en')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                        </div>
                                    </div> 
                                    <div class="col-md-3 form-group" >
                                        <label>{{ __('main.phone') }} <span class="text-danger">*</span> </label>
                                        <input type="text"  id="phone" name="phone"
                                               class="form-control @error('phone') is-invalid @enderror"
                                               placeholder="{{__('main.phone')}}"  value="{{ old('phone', $info->phone ?? '') }}" />
                                        @error('phone')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                    <div class="col-md-3 form-group" >
                                        <label>{{ __('main.phone')  . ' ' . '2'  }} <span class="text-danger">*</span> </label>
                                        <input type="text"  id="phone2" name="phone2"
                                               class="form-control @error('phone2') is-invalid @enderror"
                                               placeholder="{{__('main.phone') . ' ' . '2' }}"  value="{{ old('phone2', $info->phone2 ?? '') }}" />
                                        @error('phone2')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>  
                                    <div class="col-md-3 form-group" >
                                        <label>{{ __('main.fax') }} <span class="text-danger">*</span> </label>
                                        <input type="text"  id="fax" name="fax"
                                               class="form-control @error('fax') is-invalid @enderror"
                                               placeholder="{{__('main.fax')}}" value="{{ old('fax', $info->fax ?? '') }}"  />
                                        @error('fax')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                    <div class="col-md-3 form-group" >
                                        <label>{{ __('main.email')    }} <span class="text-danger">*</span> </label>
                                        <input type="text"  id="email" name="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               placeholder="{{__('main.email')  }}"  value="{{ old('email', $info->email ?? '') }}" />
                                        @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>

                                    <div class="col-md-3 form-group" >
                                        <label>{{ __('main.website') }} <span class="text-danger">*</span> </label>
                                        <input type="text"  id="website" name="website"
                                               class="form-control @error('website') is-invalid @enderror"
                                               placeholder="{{__('main.website')}}" value="{{ old('website', $info->website ?? '') }}"  />
                                        @error('website')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div> 
                                    <div class="col-md-3 form-group" >
                                        <label>{{ __('main.vat_no')    }} <span class="text-danger">*</span> </label>
                                        <input type="text"  id="taxNumber" name="taxNumber"
                                               class="form-control @error('taxNumber') is-invalid @enderror"
                                               placeholder="{{__('main.vat_no')  }}"  value="{{ old('taxNumber', $info->taxNumber ?? '') }}" />
                                        @error('taxNumber')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                    <div class="col-md-3 form-group" >
                                        <label>{{ __('main.commercial_register')    }} <span class="text-danger">*</span> </label>
                                        <input type="text"  id="registrationNumber" name="registrationNumber"
                                               class="form-control @error('registrationNumber') is-invalid @enderror"
                                               placeholder="{{__('main.commercial_register')  }}"  value="{{ old('registrationNumber', $info->registrationNumber ?? '') }}" />
                                        @error('registrationNumber')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>  
                                    <div class="col-md-3 form-group" >
                                        <label>{{ __('main.currency_ar') }} <span class="text-danger">*</span> </label>
                                        <input type="text"  id="currency_ar" name="currency_ar"
                                               class="form-control @error('currency_ar') is-invalid @enderror"
                                               placeholder="{{__('main.currency_ar')}}"  value="{{ old('currency_ar', $info->currency_ar ?? '') }}"/>
                                        @error('currency_ar')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>
                                    <div class="col-md-3 form-group" >
                                        <label>{{ __('main.currency_en')    }} <span class="text-danger">*</span> </label>
                                        <input type="text"  id="currency_en" name="currency_en"
                                               class="form-control @error('currency_en') is-invalid @enderror"
                                               placeholder="{{__('main.currency_en')  }}"  value="{{ old('currency_en', $info->currency_en ?? '') }}" />
                                        @error('currency_en')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>

                                    <div class="col-md-3 form-group" >
                                        <label>{{ __('main.currency_label') }} <span class="text-danger">*</span> </label>
                                        <input type="text"  id="currency_label" name="currency_label"
                                               class="form-control @error('currency_label') is-invalid @enderror"
                                               placeholder="{{__('main.currency_label')}}"  value="{{ old('currency_label', $info->currency_label ?? '') }}"/>
                                        @error('currency_label')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div>

                                    <div class="col-md-3 form-group" >
                                        <label>{{ __('main.currency_label_en') }} <span class="text-danger">*</span> </label>
                                        <input type="text"  id="currency_label_en" name="currency_label_en"
                                               class="form-control @error('currency_label_en') is-invalid @enderror"
                                               placeholder="{{__('main.currency_label_en')}}" value="{{ old('currency_label_en', $info->currency_label_en ?? '') }}" />
                                        @error('currency_label_en')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                    </div> 
                              
                                    <div class="col-md-12 form-group">
                                        <div class="form-group">
                                            <label class="form-label">{{__('main.address')}}</label>
                                            <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror">{{ old('address', $info->address ?? '') }}</textarea>
                                            @error('address')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                        </div>
                                    </div> 
                                </div> 
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <div class="custom-file">
                                            <input type="file" class="form-control custom-file-input @error('image_url') is-invalid @enderror" id="image_url" name="image_url" accept="image/png, image/jpeg">
                                            <label class="custom-file-label" for="image_url" id="path">
                                                {{ $info && ($info->logo ?? $info->image_url) ? ($info->logo ?? $info->image_url) : __('main.img_choose') }}
                                                <span style="color:red;">*</span>
                                            </label>
                                            @error('image_url')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <img  src="{{ $info && ($info->logo ?? $info->image_url) ? asset('uploads/profiles/' . ($info->logo ?? $info->image_url)) : asset('assets/img/photo.png') }}"   id="profile-img-tag" width="150px" height="150px" class="profile-img"/>
                                    </div>
                                </div>

                                <div class="row">
                                @can('تعديل الاعدادات')
                                    <div class="col-md-12" style="display: block; margin: 20px auto; text-align: center;">
                                        <button type="submit" class="btn btn-labeled btn-primary"  >
                                            {{__('main.save_btn')}}
                                        </button>
                                    </div>
                                @endcan 
                                </div>
                            </form> 
                        </div>
                    </div> 
                </div> 
            </div>
            <!-- /.container-fluid -->
        </div> 
    </div>
    <!-- End of Content Wrapper -->
</div>
<!-- End of Page Wrapper -->


@endsection
<script src="{{asset('assets/js/jquery.min.js')}}"></script>

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
        const fileName = this.files && this.files[0] ? this.files[0].name : "{{ __('main.img_choose') }}";
        $('#path').text(fileName).addClass('selected');
    });
</script>

<script type="text/javascript">
    let id = 0 ;
    $(document).ready(function()
    {
        id = 0 ;
        $(document).on('click', '#createButton', function(event) {
            console.log('clicked');
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
                    $(".modal-body #name_ar").val( "" );
                    $(".modal-body #name_en").val( "" );
                    $(".modal-body #description").val("");
                    $(".modal-body #id").val( 0 );
                    $(".modal-body #image_url").val("");
                    $(".modal-body #profile-img-tag").attr('src' , '../assets/img/photo.png' );

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



        $(document).on('click', '.editBtn', function(event) {

            id = event.currentTarget.value ;
            event.preventDefault();
            $.ajax({
                type:'get',
                url:'getCategory' + '/' + id,
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
                                if(response.image_url){
                                    var img =  '../images/Category/' + response.image_url ;

                                    $(".modal-body #profile-img-tag").attr('src' , img );
                                }

                                $(".modal-body #name_ar").val( response.name_ar );
                                $(".modal-body #name_en").val( response.name_en );
                                $(".modal-body #description").val(response.description);
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

        });
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
        $(document).on('click' , '.close-create' , function (event) {
            $('#createModal').modal("hide");
            id = 0 ;
        });



    });
    function confirmDelete(){
        let url = "{{ route('deleteCategory', ':id') }}";
        url = url.replace(':id', id);
        document.location.href=url;
    }
    function EditModal(id){
        $.ajax({
            type:'get',
            url:'getCategory' + '/' + id,
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
                            var img =  '../images/Category/' + response.image_url ;
                            $(".modal-body #profile-img-tag").attr('src' , img );
                            $(".modal-body #name").val( response.name );
                            $(".modal-body #code").val( response.code );
                            $(".modal-body #slug").val(response.slug);
                            $(".modal-body #description").val(response.description);
                            $(".modal-body #parent_id").val(response.parent_id);
                            $(".modal-body #id").val( response.id );
                            $(".modal-body #isGold").prop('checked' , response.isGold);


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
 
