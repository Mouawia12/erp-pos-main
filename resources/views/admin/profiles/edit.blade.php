@extends('admin.layouts.master')
<!-- Internal Data table css -->
<style>

    [role='combobox'] {
        left: -90px !important;
        width: 220px;
    }
</style>

@section('content')
    @if (session('success'))

        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Errors :</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- row -->
    <div id="form-control-repeater">
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                      
					<h4  class="alert alert-warning  text-center" style="color:#fff;">
                        تعديل بيانات المستخدم
                    </h5>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body">
                            <form action="{{route('admin.profile.update',Auth::user()->id)}}" method="POST"
                                  enctype="multipart/form-data">
                                @csrf
                                @method('PATCH')
                                <div class="row mb-3">

                                </div>
                                <div class="row mb-3">
                                    <div class="form-group col-md-6 pull-right ">
                                        <label for=""> اسم المستخدم </label>
                                        <input type="text" class="form-control" required
                                               value="{{ Auth::user()->name }}"
                                               placeholder="اسم المستخدم" name="name">
                                    </div>
                                    <div class="form-group col-md-3 pull-right ">
                                        <label for=""> البريد الالكترونى </label>
                                        <input type="text" class="form-control text-left" dir="ltr" required
                                               value="{{ Auth::user()->email }}" placeholder="البريد الالكترونى"
                                               name="email">
                                    </div>
									<div class="col-md-3 pull-right " id="lnWrapper">
                                        <label> التلفون : <span class="text-danger"></span></label>
                                        <input placeholder="رقم الجوال" value="{{$user->phone_number}}" required
                                               class="form-control  mg-b-20" style="text-align: left;direction:ltr;"
                                               data-parsley-class-handler="#lnWrapper"
                                               name="phone_number" type="number" min="1">
                                    </div>
                                    <div class="form-group col-md-6 pull-right ">
                                        <label for="">كلمة المرور</label>
                                        <input type="password" class="form-control" style="text-align: left;" dir="ltr"
                                               required
                                               placeholder="كلمة المرور" name="password">
                                    </div>
                                    <div class="form-group col-md-6 pull-right ">
                                        <label for=""> تاكيد كلمة المرور </label>
                                        <input type="password" class="form-control" style="text-align: left;" dir="ltr"
                                               required
                                               placeholder="تاكيد كلمة المرور" name="confirm-password">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                  
                                    <div class="col-lg-6 pull-right ">
                                        <label for=""> الصورة </label>
                                        <input accept="image/*" type="file"
                                               oninput="pic.src=window.URL.createObjectURL(this.files[0])" id="file"
                                               name="profile_pic" class="form-control">
                                        <label for="" class="d-block mt-3"> معاينة الصورة </label>
                                        <img id="pic" src="{{asset($user->profile_pic)}}"
                                             style="width: 100px; height:100px;"/>
                                    </div>
                                </div>
                        </div>
                        <div class="card-footer">
                            <div class="col-lg-12 text-center">
							    <button type="submit" name="submit" class="btn btn-success btn-md">
                                    <i class="la la-edit"></i> تعديل البيانات
                                </button>
                                <button type="reset" name="reset" class="btn btn-info btn-md">
                                    <i class="la la-refresh"></i> اعادة التعيين
                                </button>
                                
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#reset-btn').on('click', function () {
                var $image = $('#pic');
                $image.removeAttr('src').replaceWith($image.clone());
            });
        });
    </script>
@endsection

