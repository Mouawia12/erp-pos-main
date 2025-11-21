@extends('admin.layouts.master')

@section('content')
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Errors : </strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@can('تعديل صلاحية')  
	<form action="{{route('admin.roles.update',$role->id)}}" method="POST"
                        enctype="multipart/form-data">
	@csrf
    @method('PATCH') 
    <input type="hidden" value="admin-web" name="guard_name"/>
    <div class="row">
        <div class="col-md-12">
            <div class="card mg-b-20">
                <div class="card-body">
                    <div class="col-12">
                        <h4  class="alert alert-warning  text-center" style="color:#fff;">
                            تعديل مجموعة صلاحيات
							<br>  
						</h4>
                    </div> 
                    <div class="clearfix"></div> 
                    <br>
                    <div class="main-content-label mg-b-5">
                        <div class="row">
                            <div class="form-group col-lg-12 text-center">
                                <p>اسم المجموعة </p> 
                              <input type="text" value="{{$role->name}}" readonly name="name"
                                       placeholder="اسم المجموعة"
                                       class="form-control text-center" style="font-size:16px;">      
                            </div> 
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-condensed table-hover text-center">
                            <thead>
                            <tr>
                                <th>#</th>
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
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("1", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="1">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("2", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="2">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("3", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="3">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("4", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="4">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr> 
                            <tr>
                                <td>2</td>
                                <td>صلاحيات المستخدمين</td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("5", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="5">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("6", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="6">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("7", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="7">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("8", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="8">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr> 
                            <tr>
                                <td>3</td>
                                <td> الفروع </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("9", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="9">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("10", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="10">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("11", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="11">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("12", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="12">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td> الاعدادات</td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("18", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="18">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("19", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="19">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("20", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="20">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("21", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="21">
                                        <span class="slider round"></span>
                                    </label>
                                </td> 
                            </tr>
                            <tr>
                                <td>5</td>
                                <td> المبيعات </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("22", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="22">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("23", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="23">
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
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("26", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="26">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("27", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="27">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>  
                            <tr>
                                <td>7</td>
                                <td>  المشتريات</td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("30", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="30">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("31", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="31">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>8</td>
                                <td> مردود المشتريات</td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("34", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="34">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("35", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="35">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>

                            <tr>
                                <td>9</td>
                                <td> الاصناف</td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("38", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="38">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("39", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="39">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("40", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="40">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("41", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="41">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr> 
                            <tr>
                                <td>10</td>
                                <td> الموردين </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("42", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="42">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("43", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="43">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("44", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="44">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("45", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="45">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>11</td>
                                <td> العملاء </td> 
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("46", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="46">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("47", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="47">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("48", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="48">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("49", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="49">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
							</tr>	
							<tr>
                                <td>12</td>
                                <td> الحسابات </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("50", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="50">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("51", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="51">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("52", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="52">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("53", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="53">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>13</td>
                                <td> الجرد</td>
                                <td >
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("54", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="54">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
								<td >
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("55", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="55">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
								<td >
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("56", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="56">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
								<td >
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("57", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="57">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>14</td>
                                <td> سندات الصرف </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("62", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="62">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("63", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="63">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("64", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="64">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("65", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="65">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>15</td>
                                <td>  الترميز</td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("66", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="66">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("67", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="67">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("68", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="68">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("69", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="69">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>16</td>
                                <td>التقارير المحاسبية</td>
                                <td></td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("70", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="70">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td></td>
                                <td></td>  
                            </tr>
                            <tr>
                                <td>17</td>
                                <td>تقارير المخزون</td>
                                <td></td>
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("71", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="71">
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
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("72", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="72">
                                        <span class="slider round"></span>
                                    </label>
                                </td>  
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("73", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="73">
                                        <span class="slider round"></span>
                                    </label>
                                </td>  
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("74", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="74">
                                        <span class="slider round"></span>
                                    </label>
                                </td>  
                                <td>
                                    <label class="switch">
                                        <input type="checkbox" name="permission[]"
                                               <?php if (in_array("75", $rolePermissions)) {
                                                   echo "checked";
                                               } ?> value="75">
                                        <span class="slider round"></span>
                                    </label>
                                </td>  
                            </tr>  
                            </tbody>
                        </table>
                    </div> 
                    <div class="row">
                        <div class="col-xs-12 col-md-12 col-md-12 text-center">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-edit"></i> تأكيد وتعديل الصلاحيات</button>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
    <!-- main-content closed -->
    </form>
@endcan 
@endsection 
@section('js')
    <!-- main-content closed --> 
    <script>
        $('#check_all').click(function () {
            $('input[type=checkbox]').prop('checked', true);
        });
    </script>
@endsection
