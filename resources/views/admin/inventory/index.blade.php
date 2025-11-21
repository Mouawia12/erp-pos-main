@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif 
    <style>
        table.display.w-100.text-nowrap.table-bordered.dataTable.dtr-inline {
            direction: rtl;
            text-align:center;
        }
        body{
            direction: rtl; 
        }
  
    </style>  
        <!-- row opened -->
        <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0" id="head-right" >
                    <div class="col-lg-12 margin-tb">
                        <h4  class="alert alert-primary text-center">
                           قائمة محاضر الجرد  
                        </h4>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="row mt-1 mb-1 text-center justify-content-center align-content-center"> 
                @can('اضافة جرد')
                <a class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" href="{{ route('admin.inventory.create')}}" role="button"  style="border-radius: 10px; margin:5px;">
                    <i style="margin: 5px ; padding: 5px;" class="fas fa-plus-circle fa-sm text-white-50"></i> {{__('main.add_new')}}
                </a> 
                @endcan      
                </div>
                <div class="card-body px-0 pt-0 pb-2">

                    <div class="card shadow mb-4"> 
                        <div class="card-body">
                            <div class="table-responsive hoverable-table">
                                <table class="display w-100 table-bordered" id="example1" 
                                   style="text-align: center;">
                                    <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">
                                            #
                                        </th>
                                        <th>التاريخ</th>
                                        <th>رقم محضر الجرد</th> 
                                        <th>الفرع</th> 
                                        <th>المستودع</th> 
                                        <th>{{__('main.actions')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($inventorys as $inventory)
                                        <tr>
                                            <td class="text-center">{{$loop -> index + 1}}</td>
                                            <td class="text-center">{{$inventory -> date}}</td>  
                                            <td class="text-center">{{$inventory -> id}}</td>
                                            <td class="text-center">{{$inventory -> branch->branch_name}}</td>
                                            <td class="text-center">{{$inventory -> warehouse->name}}</td>
                                            <td class="text-center"> 
                                            @can('عرض جرد')   
                                                <a class="btn btn-info" href="{{ route('inventory.report', $inventory-> id)}}" role="button"><i class="fa fa-print"></i></a>  
                                            @endcan
                                            @can('تعديل جرد')   
                                                <a class="btn btn-warning" href="{{ route('admin.inventory.edit', $inventory-> id)}}" role="button"><i class="fa fa-edit"></i></a>  
                                            @endcan
                                            @can('حذف جرد') 
                                                <a class="btn btn-danger delete_inventory"
                                                   inventory_id="{{$inventory-> id}}" data-toggle="modal"
                                                   href="#modaldemo8">
                                                    <i class="fa fa-trash"></i>
                                                   
                                                </a>
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

     <div class="modal" id="modaldemo8">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header text-center">
                        <h6 class="modal-title w-100" style="font-family: 'Almarai'; ">حذف جرد</h6>
                        <button aria-label="Close" class="close"
                                data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form id="dl-form" action="{{ route('admin.inventory.destroy', 'test') }}" method="post">
                        {{ method_field('delete') }}
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <p>هل انت متأكد انك تريد الحذف ؟</p><br>
                            <input type="hidden" name="inventory_id" id="inventory_id" value="">
                         
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
 
<div class="show_modal">

</div>

<div class="barcode_modal">

</div>

@endsection
<script src="{{asset('assets/js/jquery.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function () { 
        $('.delete_inventory').on('click', function () {
            var inventory_id = $(this).attr('inventory_id'); 
            $('.modal-body #inventory_id').val(inventory_id); 
        }); 
    }); 
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#profile-img-tag').attr('src', e.target.result);

            }
            reader.readAsDataURL(input.files[0]);
            document.getElementById('path').innerHTML = input.files[0].name;
        }
    }

    $("#img").change(function () {
        readURL(this);
    });

</script>

<script type="text/javascript">
    let id = 0; 
    $(document).ready(function () {
        id = 0; 
        $(document).on('click', '.deleteBtn', function (event) {
            id = event.currentTarget.value;
            event.preventDefault();
            let href = $(this).attr('data-attr');
            $.ajax({
                url: href,
                beforeSend: function () {
                    $('#loader').show();
                },
                // return the result
                success: function (result) {
                    $('#deleteModal').modal("show");
                },
                complete: function () {
                    $('#loader').hide();
                },
                error: function (jqXHR, testStatus, error) {
                    console.log(error);
                    alert("Page " + href + " cannot open. Error:" + error);
                    $('#loader').hide();
                },
                 timeout: 500000
            })
        }); 
        
     
        $(document).on('click', '.cancel-modal', function (event) {
            $('#deleteModal').modal("hide");
            id = 0;
        });
        $(document).on('click', '.close-create', function (event) {
            $('#createModal').modal("hide");
            id = 0;
        });  
    });
 
</script>
<script>
    $(document).ready(function () {
        document.title = "قائمة محاضر الجرد";
    });
 
</script>

