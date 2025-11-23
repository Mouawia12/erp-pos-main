@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
  
    @can('عرض صنف') 
 
    <!-- End Navbar -->
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0"  id="head-right" >
                        <div class="col-lg-12 margin-tb">
                            <h4  class="alert alert-primary text-center">
                            {{ __('main.products_list')}}
                            </h4>
                        </div> 
                    </div>
                    <div class="row mt-1 mb-1 text-center justify-content-center align-content-center"> 
                        @can('اضافة صنف')  
                        <a href="{{route('createProduct')}}" type="button" class="btn btn-labeled btn-info" >
                            <i class="fa fa-plus"></i></span>
                            {{__('main.add_new')}}
                        </a>
                        @endcan  
                    </div> 
                    <div class="clearfix"><hr></div> 
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive hoverable-table">
                            <table class="display w-100  text-nowrap table-bordered" id="ItemTable" 
                                   style="text-align: center;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{__('main.code')}}</th>
                                        <th>{{__('main.name')}}</th>
                                        <th>{{__('main.unit')}}</th>
                                        <th>{{__('main.brand')}}</th>
                                        <th>{{__('main.main_category')}}</th>
                                        <th>{{__('main.price')}}</th>
                                        <th>{{__('main.cost')}}</th> 
                                        @if(empty(Auth::user()->branch_id))
                                        <th>{{__('main.quantity')}}</th> 
                                        @endif
                                        <th>{{__('main.tax')}}</th>
                                        <th>{{__('main.tax_excise')}}</th>
                                        <th>{{__('main.alert_quantity')}}</th>
                                        <th>{{__('main.status')}}</th>
                                        <th>{{__("main.locations") ?? "المستودعات"}}</th>
                                        <th>{{__('main.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody> 
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
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <label class="modelTitle"> {{__('main.brands')}}</label>
                <button type="button" class="close modal-close-btn" data-bs-dismiss="modal" aria-label="Close" >
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="paymentBody">
                <form   method="POST" action="{{ route('storeUnit') }}"
                        enctype="multipart/form-data" >
                    @csrf

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('main.code') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="text"  id="code" name="code"
                                       class="form-control"
                                       placeholder="{{ __('main.code') }}"  />
                                <input type="text"  id="id" name="id"
                                       class="form-control"
                                       placeholder="{{ __('main.code') }}"  hidden=""/>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('main.name') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="text"  id="name" name="name"
                                       class="form-control"
                                       placeholder="{{ __('main.name') }}"  />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-labeled btn-primary">
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
            <form action="{{ route('product.delete') }}" method="post">
                @csrf
                @method('POST') 
                <div class="modal-body">
                    <p>{{__('main.delete_alert')}}</p><br>
                    <input type="hidden" name="id" id="id" value=""> 
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">{{__('main.confirm_btn')}}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('main.cancel_btn')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan 
@endsection 
@section('js')
<script type="text/javascript">
          $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }); 
            var table = $('#ItemTable').DataTable({
                processing: true,
                //serverSide: true,
                responsive: true, 
                ajax: "{{ route('products') }}",
                columns: [
                    {
                        data: 'id', 
                        name: 'id'
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'unitName',
                        name: 'unitName'
                    },
                    {
                        data: 'brandName',
                        name: 'brandName'
                    },
                    {
                        data: 'category_name_ar',
                        name: 'category_name_ar'
                    },
        
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'cost',
                        name: 'cost'
                    },
                    @if(empty(Auth::user()->branch_id))
                    {
                        data: 'quantity',
                        name: 'quantity'
                    }, 
                    @endif
                    {
                        data: 'tax',
                        name: 'tax'
                    },
                    {
                        data: 'tax_excise',
                        name: 'tax_excise'
                    },
                    {
                        data: 'alert_quantity',
                        name: 'alert_quantity'
                    }, 
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id',
                        name: 'locations',
                        orderable: false,
                        searchable: false,
                        render: function(data){
                            return '<button class="btn btn-sm btn-outline-info locationsBtn" data-id="'+data+'">{{__("main.locations") ?? "المستودعات"}}</button>';
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                dom: 'lBfrtip',
                buttons: [
                    "copy", "excel", "print", "colvis"
                ],
                "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
                order: [[0, 'desc']]
            }).buttons().container().appendTo('#ItemTable_wrapper .col-md-6:eq(0)');
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
                    $(".modal-body #name").val( "" );
                    $(".modal-body #code").val( "" );
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
            var id = $(this).attr('id'); 
            event.preventDefault();
            let href = $(this).attr('data-attr');
            $.ajax({
                url: href,
                beforeSend: function() {
                    $('#loader').show();
                },
                // return the result
                success: function(result) {
                    $('#deleteModal #id').val(id); 
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

        $(document).on('click','.locationsBtn', function(){
            const id = $(this).data('id');
            $.get("{{ url('/admin/products') }}/"+id+"/locations", function(resp){
                if(resp && resp.locations){
                    let html = '<h6>'+resp.product.name+' ('+resp.product.code+')</h6>';
                    html += '<table class="table table-bordered"><thead><tr><th>{{__("main.warehouse")}}</th><th>{{__("main.quantity")}}</th><th>{{__("main.cost")}}</th><th>{{__("main.last_sale_price") ?? "آخر سعر بيع"}}</th></tr></thead><tbody>';
                    resp.locations.forEach(function(loc){
                        html += '<tr><td>'+loc.warehouse_name+'</td><td>'+loc.quantity+'</td><td>'+loc.cost+'</td><td>'+loc.last_sale_price+'</td></tr>';
                    });
                    html += '</tbody></table>';
                    $('#locationsBody').html(html);
                    $('#locationsModal').modal('show');
                }
            });
        });
        document.title = "{{ __('main.products_list')}}";

    });
    function confirmDelete(){ 
       /*
        let url = "{{ route('product.delete', ':id') }}";
        let id = $('#deleteModal #id').val();
        url = url.replace(':id', id);
        alert(id);
        document.location.href=url;
        */
    }
    function EditModal(id){
        $.ajax({
            type:'get',
            url:'getUnit' + '/' + id,
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

<div class="modal fade" id="locationsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('main.locations') ?? 'المستودعات' }}</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="locationsBody"></div>
            </div>
        </div>
    </div>
</div>
@endsection 
