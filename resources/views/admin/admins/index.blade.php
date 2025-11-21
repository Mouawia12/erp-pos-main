@extends('admin.layouts.master')
<style>
    tfoot input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
    }

    .btn-md {
        height: 40px !important;
        min-width: 100px !important;
        padding: 10px !important;
        text-align: center !important;
    }

    input[type="checkbox"] {
        width: 20px;
        height: 20px;
    }
</style>
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
    <!-- row opened -->
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="col-lg-12 margin-tb">
                        <h4  class="alert alert-info text-center">
                            عرض كل مستخدمين النظام
                        </h4>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="row mt-1 mb-1 text-center justify-content-center align-content-center">
                     
                   <a href="{{route('admin.admins.create')}}" role="button" class="btn btn-md btn-info m-1">
                        <i class="fa fa-plus"></i>
                        اضافة مستخدم جديد
                    </a>


                </div>
                <div class="card-body p-1 m-1">
                    <div class="table-responsive hoverable-table">
                        <table class="display w-100 table-bordered" id="example-table"
                               style="text-align: center;">
                            <thead>
                            <tr>
                                <th class="border-bottom-0 text-center">
                                    <input type="checkbox" id="check_all"/>
                                </th>
                                <th class="border-bottom-0 text-center">#</th>
                                <th class="border-bottom-0 text-center">اسم المستخدم</th>
                                <th class="border-bottom-0 text-center">البريد الالكترونى</th>
                                <th class="border-bottom-0 text-center">الفرع</th>
								<th class="border-bottom-0 text-center">الصلاحية</th>
                                <th class="border-bottom-0 text-center">الصورة الشخصية</th>
                               
                                <th style="width: 5%!important;" class="border-bottom-0 text-center">اعدادات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $i = 0;
                            @endphp

                            @foreach ($data as $key => $admin)
                                <tr @if($admin->id == 1) class="bg-danger text-white" @endif>
                                    <td>
                                        <input class="check @if($admin->id == 1) d-none @endif"
                                               name="admins[]" form="myForm"
                                               value="{{$admin->id}}"
                                               type="checkbox">
                                    </td>
                                    <td style="@if($admin->id == 1) color:#000; @endif">{{ ++$i }}</td>
                                    <td>{{ $admin->name}}</td>
                                    <td>{{ $admin->email }}</td>
                                    <td>
                                        @if(empty($admin->branch_id))
                                            كل الفروع
                                        @else
                                            {{$admin->branch->branch_name}}
                                        @endif
                                    </td>
                                    <td>
                                        {{$admin->role_name}}
                                    </td>
									<td>
                                        @if(empty($admin->profile_pic))
                                            <img data-toggle="modal" href="#modaldemo9"
                                                 src="{{asset('assets/img/avatar.png')}}"
                                                 style="width: 70px;cursor: pointer; height: 70px;border-radius: 100%; padding: 3px; border: 1px solid #aaa;">
                                        @else
                                            <img data-toggle="modal" href="#modaldemo9"
                                                 src="{{asset($admin->profile_pic)}}"
                                                 style="width: 70px;cursor: pointer; height: 70px;border-radius: 100%; padding: 3px; border: 1px solid #aaa;">
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-primary dropdown-toggle"
                                                    data-toggle="dropdown">
                                                <i class="fa fa-wrench"></i>
                                                ادارة
                                            </button>
                                            <div class="dropdown-menu">
                                                @can('عرض مستخدم')
                                                    <a href="{{ route('admin.admins.show', $admin->id) }}"
                                                       class="dropdown-item">
                                                        <i class="fa fa-eye"></i>
                                                        عرض
                                                    </a>
                                                @endcan
                                                @can('تعديل مستخدم')
                                                    <a href="{{ route('admin.admins.edit', $admin->id) }}"
                                                       class="dropdown-item">
                                                        <i class="fa fa-edit"></i>
                                                        تعديل
                                                    </a>
                                                @endcan
                                                @can('حذف مستخدم')
                                                    @if ($admin->id != 1)
                                                        <a class="dropdown-item delete_admin"
                                                           admin_id="{{ $admin->id }}"
                                                           email="{{ $admin->email }}" data-toggle="modal"
                                                           href="#modaldemo8">
                                                            <i class="fa fa-trash"></i>
                                                            حذف
                                                        </a>
                                                    @endif
                                                @endcan
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            </tfoot>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--/div-->

        <!-- Modal effects -->
        <div class="modal" id="modaldemo8">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header text-center">
                        <h6 class="modal-title w-100" style="font-family: 'Almarai'; ">حذف مستخدم</h6>
                        <button aria-label="Close" class="close"
                                data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="{{ route('admin.admins.destroy', 'test') }}" method="post">
                        {{ method_field('delete') }}
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <p>هل انت متأكد انك تريد الحذف ؟</p><br>
                            <input type="hidden" name="admin_id" id="admin_id" value="">
                            <input class="form-control" name="email" id="email" type="text" readonly>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">الغاء</button>
                            <button type="submit" class="btn btn-danger">حذف</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal effects -->
    <div class="modal" id="modaldemo9">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-demo">
                <div class="modal-header text-center">
                    <h6 class="modal-title w-100"
                        style="font-family: 'Almarai'; ">عرض صورة المستخدم</h6>
                    <button aria-label="Close" class="close"
                            data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <img id="image_larger" alt="image" style="width: 100%;height: 400px!important;  "/>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-md btn-danger"><i class="fa fa-colse"></i> اغلاق
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection
<script src="{{asset('assets/js/jquery.min.js')}}"></script>
<script>
    $(document).ready(function () {
        $('#check_all').click(function () {
            if (this.checked) {
                $('input.check').prop('checked', true);
            } else {
                $('input.check').prop('checked', false);
            }
        });
        $('.delete_admin').on('click', function () {
            var admin_id = $(this).attr('admin_id');
            var email = $(this).attr('email');
            $('.modal-body #admin_id').val(admin_id);
            $('.modal-body #email').val(email);
        });
        $('img').on('click', function () {
            var image_larger = $('#image_larger');
            var path = $(this).attr('src');
            $(image_larger).prop('src', path);
        });
 
        $('#example-table tfoot tr th:nth-child(3)').html('<input class="form-control" type="text" placeholder="اسم المستخدم" />');
        $('#example-table tfoot tr th:nth-child(4)').html('<input class="form-control" type="text" placeholder="البريد الالكترونى" />');
        $('#example-table tfoot tr th:nth-child(6)').html('<select id="roles" class="form-control">@foreach($roles as $role)<option value="{{$role}}">{{$role}}</option>@endforeach</select>');
        $('#example-table').DataTable({
            "columnDefs": [
                {"orderable": false, "targets": [0, 4, 7]}
            ],
            "order": [[1, "desc"]],
            initComplete: function () {
                this.api().columns().every(function () {
                    var that = this;
                    $('input[type="text"]', this.footer()).on('keyup change clear', function () {
                        if (that.search() !== this.value) {
                            that.search(this.value).draw();
                        }
                    });
                    $('select', this.footer()).on('change', function () {
                        if (that.search() !== this.value) {
                            that.search(this.value).draw();
                        }
                    });
                });
            }
        });
    });
</script>
