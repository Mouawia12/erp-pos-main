@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
<!-- row opened -->
<style>
        table.display.w-100.text-nowrap.table-bordered.dataTable.dtr-inline {
            direction: rtl;
            text-align:center;
        }
        body{
            direction: rtl; 
        }
  
</style> 
@can('عرض سند صرف')     
<div class="row row-sm">
    <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0 text-center" id="head-right" >
                    <div class="col-lg-12 margin-tb ">
                        <h4  class="alert alert-primary text-center">
                           [ {{__('main.expenses')}} ]
                        </h4>
                    </div>
                    <div class="clearfix"></div>
                    @can('اضافة سند صرف')   
                        <button type="button" class="btn btn-labeled btn-info " id="createButton">
                            <span class="btn-label" style="margin-right: 10px;">
                            <i class="fa fa-plus"></i></span>
                            {{__('main.add_new')}}
                        </button>
                    @endcan 
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
                                            <th>{{__('main.date')}}</th>
                                            <th>{{__('main.basedon_no')}} </th>
                                            <th>{{__('main.branche')}}</th>  
                                            <th>{{__('main.from')}} </th>
                                            <th>{{__('main.to')}} </th>
                                            <th>{{__('main.total_money')}} </th>
                                            <th>{{__('main.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($bills as $bill) 
                                        <tr>
                                            <td class="text-center">{{$loop->index+1}}</td>
                                            <td class="text-center">{{$bill -> date}}</td>
                                            <td class="text-center">{{$bill -> docNumber}}</td>
                                            <td class="text-center">{{$bill->branch->branch_name}}</td>
                                            <td class="text-center">{{$bill -> from_account_name }}</td>
                                            <td class="text-center">{{$bill -> to_account_name }}</td>
                                            <td class="text-center">{{$bill -> amount}}</td>
                                            <td class="text-center">
                                               @can('عرض سند صرف')   
                                                <button type="button" class="btn btn-labeled btn-info editBtn" value="{{$bill -> id}}">
                                                    <i class="fa fa-eye"></i>
                                                </button>
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
            </div>
            <!-- /.container-fluid --> 
        </div>
        <!-- End of Main Content -->  
    </div>
    <!-- End of Content Wrapper --> 
</div>
<!-- End of Page Wrapper --> 

<div class="modal fade" id="createModal" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <label class="modelTitle"> {{__('main.expenses_create')}}</label>
                <button type="button" class="close modal-close-btn close-create"  data-bs-dismiss="modal"  aria-label="Close" >
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="paymentBody">
                <form  method="POST" action="{{ route('storeExpense') }}"
                        enctype="multipart/form-data" >
                    @csrf 
                    <div class="row">
                        <div class="col-3">
                            <div class="form-group">
                                <label>{{ __('main.basedon_no') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="text" id="docNumber" name="docNumber"
                                    class="form-control" readonly
                                    placeholder="{{__('0')}}"/>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label>{{ __('main.date') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="date" id="date" name="date"
                                    class="form-control"  readonly/>
                                <input type="text" id="id" name="id"
                                    class="form-control"
                                    placeholder="{{ __('main.code') }}"  hidden=""/>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="d-block">
                                    الفرع <span style="color:red; font-size:20px; font-weight:bold;">*</span>
                                </label>
                                @if(empty(Auth::user()->branch_id))
                                    <select required  class="form-control select2" name="branch_id" id="branch_id">
                                        <option value="">حدد الاختيار</option>
                                        @foreach($branches as $branch)
                                            <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input class="form-control" type="text" readonly
                                           value="{{Auth::user()->branch->branch_name}}"/>
                                    <input required class="form-control" type="hidden" id="branch_id"
                                           name="branch_id"
                                           value="{{Auth::user()->branch_id}}"/>
                                @endif
                    
                            </div>
                        </div> 
                    </div> 
                    <div class="row">
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.from') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <select id="from_account" name="from_account" class="js-example-basic-single w-100">
                                    @foreach($faccounts as $account)
                                        <option value="{{$account -> id}}">{{$account -> name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('main.details') ?? __('main.accounts') }}</label>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="expense_details_table">
                                        <thead>
                                        <tr>
                                            <th class="text-center">{{ __('main.to') }}</th>
                                            <th class="text-center">{{ __('main.money') }}</th>
                                            <th class="text-center">{{ __('main.tax') }}</th>
                                            <th class="text-center">{{ __('main.notes') }}</th>
                                            <th class="text-center">{{ __('main.actions') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>
                                                <select id="to_account" name="detail_account_id[]" class="js-example-basic-single w-100 detail-account">
                                                    @foreach($accounts as $account)
                                                        <option value="{{$account -> id}}">{{$account -> name}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input class="form-control" id="amount" name="detail_amount[]" type="number" step="0.01">
                                            </td>
                                            <td>
                                                <input class="form-control" id="tax_amount" name="detail_tax_amount[]" type="number" step="0.01" value="0">
                                            </td>
                                            <td>
                                                <input class="form-control" name="detail_notes[]" type="text">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-danger remove-detail">-</button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-sm btn-secondary mt-2" id="add_expense_detail">
                                    {{ __('main.add_new') }}
                                </button>
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>{{ __('main.bill_expense_client') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input class="form-control" id="client" name="client" type="text">
                            </div>
                        </div>
                        <div class="col-6" hidden>
                            <!--
                            <div class="form-group">
                                <label>{{ __('main.payment_method') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <select class="form-control" name="payment_type" id="payment_type">
                                    <option value="0"> {{__('main.cash')}} </option>
                                    <option value="1"> {{__('main.visa')}} </option> 
                                </select> 
                            </div>
                            -->
                            <input id="payment_type" name="payment_type" type="hidden" value="-1">
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-12 " >
                            <div class="form-group">
                                <label>{{ __('main.notes') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <textarea type="text"  id="notes" name="notes" class="form-control" placeholder="{{ __('main.notes') }}"></textarea>
                            </div>
                        </div> 
                    </div> 
                    <div class="row">
                        <div class="col-6" style="display: block; margin: 20px auto; text-align: center;">
                            <button type="submit" class="btn btn-labeled btn-primary" id="submitBtn" >
                                {{__('main.save_btn')}}
                            </button>   
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <label class="modelTitle"> {{__('main.deleteModal')}}</label>
                <button type="button" class="close"  data-bs-dismiss="modal"  aria-label="Close" style="color: red; font-size: 20px; font-weight: bold;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="smallBody">
                <img src="{{asset('assets/img/warning.png')}}" class="alertImage">
                <label class="alertTitle">{{__('main.delete_alert')}}</label>
                <br> <label  class="alertSubTitle" id="modal_table_bill"></label>
                <div class="row">
                    <div class="col-6 text-center">
                        <button type="button" class="btn btn-labeled btn-primary" onclick="confirmDelete()">
                            <span class="btn-label" style="margin-right: 10px;"><i class="fa fa-check"></i></span>{{__('main.confirm_btn')}}</button>
                    </div>
                    <div class="col-6 text-center">
                        <button type="button" class="btn btn-labeled btn-secondary cancel-modal"  >
                            <span class="btn-label" style="margin-right: 10px;"><i class="fa fa-close"></i></span>{{__('main.cancel_btn')}}</button>
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
        var now = new Date(); 
        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2); 
        var today = now.getFullYear()+"-"+(month)+"-"+(day) ;

        id = 0 ;
        document.title = "{{__('main.expenses')}}";

        getBillNo();   

        const $createModal = $('#createModal');
        function initExpenseSelects(context){
            if (!$.fn || !$.fn.select2) {
                return;
            }
            const $context = context ? $(context) : $(document);
            $context.find('.detail-account').each(function(){
                const $select = $(this);
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }
                $select.next('.select2-container').remove();
                $select.select2({ dropdownParent: $createModal, width: '100%' });
            });
            $('#branch_id').select2({ dropdownParent: $createModal, width: '100%' });
            $('#from_account').select2({ dropdownParent: $createModal, width: '100%' });
        }
        $createModal.on('shown.bs.modal', function(){
            initExpenseSelects($(this));
        });
        $(document).on('change', '#branch_id', function () {
            getBillNo();   
            $('#payment_type').val(0).trigger("change"); 
            $('#expense_details_table tbody').html($('#expense_details_table tbody tr:first').prop('outerHTML'));
            $('#client').val('');  
            $('#notes').val(''); 
        });

        function getBillNo() {
            let bill_number = document.getElementById('docNumber');  
            let branch_id = document.getElementById('branch_id').value;

            var url = '{{route('get.expenses.no',":id")}}';
                url = url.replace(":id",branch_id);

            $.ajax({
                type: 'get',
                url: url,
                dataType: 'json',
                success: function (response) {
                    console.log(response);
                    if (response) {
                        bill_number.value = response;
                    } else {
                        bill_number.value = '';
                    }
                }
            });
        }

        $(document).on('click', '#createButton', function(event) { 
            id = 0 ;  
            $('#createModal').modal("show");
            $(".modal-body #date").val(today );
            $(".modal-body #notes").val("");
            $(".modal-body #docNumber").val(result);

            $(".modal-body #id").val( 0 );
            $(".modal-body #type_id").val(0);
            $('#expense_details_table tbody').html($('#expense_details_table tbody tr:first').prop('outerHTML'));

            $(".modal-body #date").attr('readOnly' , false);
            $(".modal-body #amount").attr('readOnly' , false);
            $(".modal-body #payment_type").attr('readOnly' , false);
            $(".modal-body #notes").attr('disabled' , false);
            $(".modal-body #submitBtn").show();
            $(".modal-body #printtBtn").hide(); 
        });

        $(document).on('click', '#add_expense_detail', function(){
            const $row = $('#expense_details_table tbody tr:first').clone();
            $row.find('input').val('');
            $row.find('.select2-container').remove();
            $row.find('select').val('').removeAttr('id');
            $row.find('#amount').removeAttr('id');
            $row.find('#tax_amount').removeAttr('id');
            $('#expense_details_table tbody').append($row);
            initExpenseSelects($row);
        });

        $(document).on('click', '.remove-detail', function(){
            const $rows = $('#expense_details_table tbody tr');
            if ($rows.length <= 1) {
                return;
            }
            $(this).closest('tr').remove();
        });

        $(document).on('click', '.editBtn', function(event) {

            id = event.currentTarget.value ;
            event.preventDefault();
            $.ajax({
                type:'get',
                url:'getExpense' + '/' + id,
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
                                $(".modal-body #date").val(response.date );
                                $(".modal-body #notes").val(response.notes);
                                $(".modal-body #docNumber").val(response.docNumber); 
                                $(".modal-body #id").val( response.id );
                                $(".modal-body #from_account").val(response.from_account);
                                $(".modal-body #to_account").val(response.to_account);
                                $(".modal-body #amount").val(response.amount);
                                $(".modal-body #client").val(response.client);
                                $(".modal-body #payment_type").val(response.payment_type);


                                $(".modal-body #date").attr('readOnly' , true);
                                $(".modal-body #amount").attr('disabled' , true);
                                $(".modal-body #payment_type").attr('readOnly' , true);
                                $(".modal-body #from_account").attr('disabled' , true);
                                $(".modal-body #to_account").attr('disabled' , true);
                                $(".modal-body #notes").attr('disabled' , true);
                                $(".modal-body #client").attr('disabled' , true);
                                $(".modal-body #submitBtn").hide();
                                $(".modal-body #printtBtn").show();

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

        $(document).on('click' , '#printtBtn' , function (event) {
            let url = "" ;
            let val = document.getElementById('id').value    ;
            url   = "{{ route('printExpense', ':id') }}";
            url = url.replace(':id', val);
            document.location.href = url;
        });

    });
    function confirmDelete(){
        let url = "{{ route('expenses_type_destroy', ':id') }}";
        url = url.replace(':id', id);
        document.location.href=url;
    }

</script> 
@endsection 
 





