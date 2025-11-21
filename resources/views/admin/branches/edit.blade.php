@extends('admin.layouts.master')
<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 25px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
 .slider {
        position: absolute;
        cursor: pointer;
        top: 5px;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 0px;
        bottom: 0px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #2196F3;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
    span.select2-selection.select2-selection--single{
        padding:2px;
    }

</style>
@section('content')
    <!-- row -->
    @can('تعديل فرع') 
    <div class="row">
        <div class="col-lg-12 col-md-12">

            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>الاخطاء :</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif 
            <div class="card">
                <div class="card-body"  style="padding:5%;">
                    <div class="col-lg-12">
                        <h4  class="alert alert-warning  text-center" style="color:#fff;">
                            تعديل بيانات الفرع
                        </h4>
                        <div class="clearfix"></div>
                    </div>
                    <br>
 
                    <form action="{{route('admin.branches.update',$branch->id)}}" method="POST"
                                  enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')  
                    <div class="row m-t-3 mb-3">
                        <div class="col-md-4">
                            <label> اسم الفرع <span class="text-danger"> </span></label>
                            <input value="{{$branch->branch_name}}" class="form-control mg-b-20" name="branch_name" required="" type="text">
                        </div>
                        <div class="col-md-4">
                            <label> التلفون <span class="text-danger"> </span></label>
                            <input value="{{$branch->branch_phone}}" class="form-control mg-b-20" dir="ltr" min="1" name="branch_phone" required="" type="number">
                        </div>
                        <div class="col-md-4">
                            <label> العنوان <span class="text-danger"> </span></label>
                            <input value="{{$branch->branch_address}}" class="form-control mg-b-20" dir="rtl" name="branch_address" required="" type="text">
                        </div>
                    </div>
           
                    <div class="col-md-12">
                        <label> الحالة<span class="text-danger"> </span></label>
						<label class="switch">
                           <input type="checkbox" id="status" name="status" 
                           @if($branch->status == 1)
                                checked 
                               @endif
							value="{{$branch->status}}" >	
                           <span class="slider round"></span>
                        </label> 
                    </div> 

                    <div class="col-lg-12 text-center mt-3 mb-3 text-center">
                        <button class="btn btn-info btn-md" type="submit"><i class="fa fa-edit"></i> تعديل</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- main-content closed -->
@endcan 
@endsection 
@section('js')
    <script>
        $('#status').click(function () {
			if (document.getElementById("status").value=="1") {
			    document.getElementById("status").value= "0"; 
			}else{
			    document.getElementById("status").value= "1"; 
			} 
        });
    </script>
@endsection
