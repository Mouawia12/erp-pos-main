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

</style>
@section('content')
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
    <div class="row">
        <div class="col-md-12">
            <div class="card mg-b-20">
                <div class="card-body">
                    <div class="col-12">
                        <h4 class="alert alert-primary  text-center">
                            اضافة صلاحية جديدة
                        </h4>
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <form action="{{route('admin.roles.store')}}" method="POST"
                        enctype="multipart/form-data">
	                @csrf
                    @method('POST') 
                    <input type="hidden" value="admin-web" name="guard_name"/>
                    <div class="main-content-label mg-b-5">
                        <div class="row">
                            <div class="col-md-6  col-md-offset-6 mx-auto">
                                <div class="form-group text-center">
                                    <p> اسم مجموعة الصلاحية </p>
                                    <input type="text" name="name"
                                       placeholder="اسم المجموعة"
                                       class="form-control text-center" style="font-size:16px;">  
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-condensed table-hover text-center">
                            <thead>
                            <tr>
                                <th class="text-center"> #</th>
                                <th class="text-center">اسم الصلاحية</th>
                                <th class="text-center">اضافة</th>
                                <th class="text-center">عرض</th>
                                <th class="text-center">تعديل</th>
                                <th class="text-center">حذف</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>1</td>
                                <td>المستخدمين</td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="1">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="2">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="3">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="4">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>صلاحيات المستخدمين</td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="5">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="6">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="7">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="8">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td> الفروع </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="9">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="10">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="11">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="12">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td> الاعدادات </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="18">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="19">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="20">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="21">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td> المبيعات </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="22">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="23">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td></td>
                                <td></td> 
                            </tr>
                            <tr>
                                <td>6</td>
                                <td> مرتجع المبيعات </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="26">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="27">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td></td>
                                <td></td> 
                            </tr>
                            <tr>
                                <td>7</td>
                                <td>  المشتريات </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="30">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="31">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td></td>
                                <td></td> 
                            </tr>
                            <tr>
                                <td>8</td>
                                <td> مردود المشتريات </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="34">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="35">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td></td>
                                <td></td> 
                            </tr>
                            <tr>
                                <td>9</td>
                                <td>الاصناف</td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="38">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="39">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="40">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="41">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>10</td>
                                <td>الموردين</td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="42">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="43">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="44">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="45">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>11</td>
                                <td>العملاء</td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="46">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="47">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="48">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="49">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr> 
                            <tr>
                                <td>12</td>
                                <td>  الحسابات</td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="50">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="51">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="52">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="53">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>13</td>
                                <td>  الجرد</td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="54">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="55">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="56">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="57">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>14</td>
                                <td>سندات الصرف</td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="62">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="63">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="64">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="65">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr> 
                            <tr>
                                <td>15</td>
                                <td>الترميز</td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="66">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="67">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="68">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="69">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr> 
                            <tr>
                                <td>16</td>
                                <td> تقارير المحاسبية </td>
								<td></td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="70">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>17</td>
                                <td> تقارير المخزون </td>
								<td></td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="71">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td></td>
                                <td></td>
                            </tr> 
                            <tr>
                                <td>18</td>
                                <td>سندات القبض</td> 
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="72">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="73">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="74">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]" value="75">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr> 
                           </tbody>
                        </table>
                    </div> 
                    <div class="row">
                        <div class="col-xs-12 col-md-12 col-md-12 text-center">
                            <button type="button" id="check_all" class="btn btn-danger"><i class="fa fa-check"></i> تحديد الكل</button>
                            <button type="submit" class="btn btn-info"><i class="fa fa-plus"></i> تأكيد واضافة الصلاحيات</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- main-content closed -->
    <script src="{{asset('assets/js/jquery.min.js')}}"></script>
    <script>
        $('#check_all').click(function () {
            $('input[type=checkbox]').prop('checked', true);
        });
    </script>
@endsection
