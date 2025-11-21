@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
@can('اضافة حسابات')
<!-- row opened -->
<div class="row row-sm">
    <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="col-lg-12 margin-tb">
                        <h4  class="alert alert-primary text-center">
                            {{__('main.account_settings')}}
                        </h4>
                    </div>
                    <div class="clearfix"></div>
                </div>  
                </div> 
                <div class="card-body px-0 pt-0 pb-2">

                    <div class="card shadow mb-4"> 
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="display w-100  text-nowrap table-bordered" id="example1" 
                                   style="text-align: center;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{__('main.branche')}}</th>
                                            <th>{{__('main.safe_account')}}</th>
                                            <th>{{__('main.sales_account')}}</th>
                                            <th>{{__('main.purchase_account')}}</th> 
                                            <th>{{__('main.return_sales_account')}}</th>
                                            <th>{{__('main.return_purchase_account')}}</th>
                                            <th>{{__('main.stock_account')}}</th>
                                            <th>{{__('main.sales_discount_account')}}</th>
                                            <th>{{__('main.sales_tax_account')}}</th>
                                            <th>{{__('main.purchase_discount_account')}}</th>
                                            <th>{{__('main.purchase_tax_account')}}</th>
                                            <th>{{__('main.cost_account')}}</th>
                                            <th>{{__('main.profit_account')}}</th>
                                            <th>{{__('main.reverse_profit_account')}}</th>
                                            <th>{{__('main.actions')}}</th> 
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($accounts as $unit)
                                    <tr>
                                        <td class="text-center">{{$unit->id}}</td>
                                        <td class="text-center">{{$unit->branch_name}}</td>
                                        <td class="text-center">{{$unit->safe_account_name}}</td>
                                        <td class="text-center">{{$unit->sales_account_name}}</td>
                                        <td class="text-center">{{$unit->purchase_account_name}}</td>
                                        <td class="text-center">{{$unit->return_sales_account_name}}</td> 
                                        <td class="text-center">{{$unit->return_purchase_account_name}}</td>
                                        <td class="text-center">{{$unit->stock_account_name}}</td>
                                        <td class="text-center">{{$unit->sales_discount_account_name}}</td>
                                        <td class="text-center">{{$unit->sales_tax_account_name}}</td>
                                        <td class="text-center">{{$unit->purchase_discount_account_name}}</td> 
                                        <td class="text-center">{{$unit->purchase_tax_account_name}}</td>
                                        <td class="text-center">{{$unit->cost_account_name}}</td>
                                        <td class="text-center">{{$unit->profit_account_name}}</td>
                                        <td class="text-center">{{$unit->reverse_profit_account_name}}</td>

                                        <td class="text-center">
                                            <a href="{{route('edit_account_settings' , $unit -> id)}}">
                                                <button type="button" class="btn btn-labeled btn-secondary "> 
                                                    <i class="fa fa-pen"></i>{{__('main.edit')}}
                                                </button>
                                            </a> 
                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody> 
                                </table>
                            </div>
                        </div>
                    </div> 
                </div> 
            </div>
            <!-- /.container-fluid --> 
        </div>
        <!-- End of Main Content -->  
    </div>
    <!-- End of Content Wrapper --> 
</div>
<!-- End of Page Wrapper -->


<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <label class="modelTitle"> {{__('main.deleteModal')}}</label>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"
                        style="color: red; font-size: 20px; font-weight: bold;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="smallBody">
                <img src="../assets/img/warning.png" class="alertImage">
                <label class="alertTitle">{{__('main.delete_alert')}}</label>
                <br> <label class="alertSubTitle" id="modal_table_bill"></label>
                <div class="row">
                    <div class="col-6 text-center">
                        <button type="button" class="btn btn-labeled btn-primary" onclick="confirmDelete()">
                            <span class="btn-label" style="margin-right: 10px;"><i
                                    class="fa fa-check"></i></span>{{__('main.confirm_btn')}}</button>
                    </div>
                    <div class="col-6 text-center">
                        <button type="button" class="btn btn-labeled btn-secondary cancel-modal">
                            <span class="btn-label" style="margin-right: 10px;"><i
                                    class="fa fa-close"></i></span>{{__('main.cancel_btn')}}</button>
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

    $("#image_url").change(function () {
        readURL(this);
    });
</script>

<script type="text/javascript">
    let id = 0;
    $(document).ready(function () {
        id = 0;
        $(document).on('click', '#createButton', function (event) {
            console.log('clicked');
            id = 0;
            event.preventDefault();
            let href = $(this).attr('data-attr');
            $.ajax({
                url: href,
                beforeSend: function () {
                    $('#loader').show();
                },
                // return the result
                success: function (result) {
                    $('#createModal').modal("show");
                    $(".modal-body #code").val("");
                    $(".modal-body #name_ar").val("");
                    $(".modal-body #name_en").val("");
                    $(".modal-body #item_type").val("");
                    $(".modal-body #category_id").val("");
                    $(".modal-body #karat_id").val("");
                    $(".modal-body #weight").val("");
                    $(".modal-body #no_metal").val("");
                    $(".modal-body #no_metal_type").val("");
                    $(".modal-body #tax").val("");
                    $(".modal-body #made_Value").val("");
                    $(".modal-body #state").val("");
                    $(".modal-body #id").val(0);

                    $(".modal-body #karat_id").change(function (){
                        $.ajax({
                            type: 'get',
                            url: 'getKarat' + '/' + this.value,
                            dataType: 'json',

                            success: function (response) {

                                $(".modal-body #tax").val(response.stamp_value);
                            }
                        });
                    });

                },
                complete: function () {
                    $('#loader').hide();
                },
                error: function (jqXHR, testStatus, error) {
                    console.log(error);
                    alert("Page " + href + " cannot open. Error:" + error);
                    $('#loader').hide();
                },
                timeout: 8000
            })
        });



        $(document).on('click', '.deleteBtn', function (event) {
            console.log('clicked');
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
                timeout: 8000
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

    function confirmDelete() {
        let url = "{{ route('delete_account_settings', ':id') }}";
        url = url.replace(':id', id);
        document.location.href = url;
    }

    function EditModal(id) {
        $.ajax({
            type: 'get',
            url: 'getCategory' + '/' + id,
            dataType: 'json',

            success: function (response) {
                console.log(response);
                if (response) {
                    let href = $(this).attr('data-attr');
                    $.ajax({
                        url: href,
                        beforeSend: function () {
                            $('#loader').show();
                        },
                        // return the result
                        success: function (result) {
                            $('#createModal').modal("show");
                            var img = '../images/Category/' + response.image_url;
                            $(".modal-body #profile-img-tag").attr('src', img);
                            $(".modal-body #name").val(response.name);
                            $(".modal-body #code").val(response.code);
                            $(".modal-body #slug").val(response.slug);
                            $(".modal-body #description").val(response.description);
                            $(".modal-body #parent_id").val(response.parent_id);
                            $(".modal-body #id").val(response.id);
                            $(".modal-body #isGold").prop('checked', response.isGold);


                        },
                        complete: function () {
                            $('#loader').hide();
                        },
                        error: function (jqXHR, testStatus, error) {
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