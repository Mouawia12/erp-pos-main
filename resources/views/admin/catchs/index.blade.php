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
<style>
        table.display.w-100.text-nowrap.table-bordered.dataTable.dtr-inline {
            direction: rtl;
            text-align:center;
        }
        body{
            direction: rtl; 
        }
  
</style>
@can('عرض سند قبض')  
<div class="row row-sm">
    <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0" id="head-right" >
                    <div class="col-lg-12 margin-tb">
                        <h4  class="alert alert-primary text-center">
                          [ {{__('main.catches')}} ] 
                        </h4>
                    </div>
                    <div class="clearfix"></div>
                </div> 
                <div class="row mt-1 mb-1 text-center justify-content-center align-content-center"> 
                   @can('اضافة سند قبض')  
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
                                            <th>{{__('main.basedon_no')}} </th>
                                            <th>{{__('main.date')}}</th> 
                                            <th>{{__('main.branche')}}</th>  
                                            <th>{{__('main.from')}} </th>
                                            <th>{{__('main.to')}} </th>
                                            <th>{{__('main.money')}} </th>
                                            <th>{{__('main.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($bills as $bill) 
                                        <tr>
                                            <td>{{$loop->index + 1}}</td>
                                            <td>{{$bill -> docNumber}}</td> 
                                            <td>{{$bill -> created_at}}</td> 
                                            <td>{{$bill->branch->branch_name}}</td>
                                            <td>{{$bill -> from_account_name }}</td>
                                            <td>{{$bill -> to_account_name }}</td>
                                            <td>{{$bill -> amount}}</td>
                                            <td>
                                               @can('عرض سند قبض') 
                                                    <button type="button" class="btn btn-labeled btn-info editBtn" value="{{$bill -> id}}"> 
                                                        <i class="fa fa-eye"></i> 
                                                    </button>
                                                @endcan 
                                                @can('حذف سند قبض')  
                                                <!--
                                                    <button type="button" class="btn btn-labeled btn-danger deleteBtn"
                                                        value="{{$bill -> id}}"> 
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                -->
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


<div class="modal fade" id="createModal"  role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header no-print">
                <label class="modelTitle"> {{__('main.catches_create')}}</label>
                <button type="button" class="close modal-close-btn close-create"  data-bs-dismiss="modal"  aria-label="Close" >
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="paymentBody">
                <form method="POST" action="{{route('storeCatch')}}"
                        enctype="multipart/form-data">
                    @csrf 
                    <div class="row">
                        <div class="col-3">
                            <div class="form-group">
                                <label>{{ __('main.basedon_no') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="text" id="docNumber" name="docNumber"
                                    class="form-control @error('docNumber') is-invalid @enderror" readonly
                                    placeholder="{{__('0')}}"/>
                                @error('docNumber')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label>{{ __('main.date') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="date" id="date" name="date"
                                    class="form-control @error('date') is-invalid @enderror"
                                    value="{{ old('date', now()->format('Y-m-d')) }}" />
                                <input type="text" id="id" name="id"
                                    class="form-control"
                                    placeholder="{{ __('main.code') }}"  hidden=""/>
                                @error('date')<span class="invalid-feedback">{{ $message }}</span>@enderror
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
                        <div class="col-6">
                            <div class="form-group">
                                <label>{{ __('نوع الحساب') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <select id="parent_code" name="parent_code" class="form-control select2" required>
                                    <option value="">{{ __('main.choose') ?? 'حدد الاختيار' }}</option>
                                    <option value="0">{{ __('main.Root') }}</option>
                                    <option value="1">{{ __('main.General') }}</option>
                                    <option value="2">{{ __('main.Branch') }}</option>
                                    <option value="3">{{ __('main.Branch_Ledger') }}</option>
                                </select> 
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('main.details') ?? __('main.accounts') }}</label>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="catch_details_table">
                                        <thead>
                                        <tr>
                                            <th class="text-center">{{ __('الحساب') }}</th>
                                            <th class="text-center">{{ __('main.money') }}</th>
                                            <th class="text-center">{{ __('main.notes') }}</th>
                                            <th class="text-center">{{ __('main.actions') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>
                                                <select id="account_id" name="detail_account_id[]" class="form-control select2 detail-account @error('detail_account_id.0') is-invalid @enderror" required>
                                                    <option value=""></option>
                                                    @foreach($accounts as $account)
                                                        <option value="{{$account->id}}" @if(old('detail_account_id.0')==$account->id) selected @endif>{{$account->name}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input class="form-control @error('detail_amount.0') is-invalid @enderror" id="amount" name="detail_amount[]" type="number" step="0.01" value="{{ old('detail_amount.0') }}">
                                            </td>
                                            <td>
                                                <input class="form-control" name="detail_notes[]" type="text" value="{{ old('detail_notes.0') }}">
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-danger remove-detail">-</button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-sm btn-secondary mt-2" id="add_catch_detail">
                                    {{ __('main.add_new') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.payment_method') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <select class="form-control @error('payment_type') is-invalid @enderror" name="payment_type" id="payment_type">
                                    <option value="0" @if(old('payment_type')==='0') selected @endif> {{__('main.cash')}} </option>
                                    <option value="1" @if(old('payment_type')==='1') selected @endif> {{__('main.visa')}} </option> 
                                </select> 
                                @error('payment_type')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-12 " >
                            <div class="form-group">
                                <label>{{ __('main.notes') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <textarea type="text"  id="notes" name="notes" class="form-control @error('notes') is-invalid @enderror" placeholder="{{ __('main.notes') }}">{{ old('notes') }}</textarea>
                                @error('notes')<span class="invalid-feedback">{{ $message }}</span>@enderror
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
        @if($errors->any())
            $('#createModal').modal({backdrop:'static', keyboard:false});
        @endif
        const $createModal = $('#createModal');
        const select2Options = { dropdownParent: $createModal, width: '100%' };
        function initCatchSelects(context) {
            if (!$.fn || !$.fn.select2) {
                setTimeout(initCatchSelects, 150);
                return;
            }
            const $context = context ? $(context) : $(document);
            $context.find('.detail-account').each(function(){
                const $select = $(this);
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }
                $select.next('.select2-container').remove();
                $select.select2({ ...select2Options, placeholder: '{{ __("main.choose") ?? "اختر الحساب" }}', allowClear: true });
            });
            $('#parent_code').select2({ ...select2Options, placeholder: '{{ __("main.choose") ?? "حدد الاختيار" }}', allowClear: true });
            $('#branch_id').select2(select2Options);
        }
        initCatchSelects(document);
        $createModal.on('shown.bs.modal', function(){
            if (!$.fn || !$.fn.select2) {
                return;
            }
            $(this).find('.detail-account').select2('destroy');
            initCatchSelects($(this));
        });
        const catchNoUrl = "{{ route('get.catch.recipt.no', ['branch_id' => 'BRANCH_ID']) }}";
        const supplierAccountsUrl = "{{ route('getSupplierAccount', ['id' => 'ACCOUNT_TYPE']) }}";
        const getCatchUrl = "{{ route('getCatch', ['id' => 'CATCH_ID']) }}";
        var now = new Date();
        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);
        var today = now.getFullYear()+"-"+(month)+"-"+(day) ;
        id = 0 ;
        getBillNo(); 
        document.title = "{{__('main.catches')}}";
        const oldParent = "{{ old('parent_code') }}";
        const oldAccount = "{{ old('detail_account_id.0') }}";
        if (oldParent) {
            $('#parent_code').val(oldParent).trigger('change');
            loadAccounts(oldParent, oldAccount);
        } else {
            const defaultParent = '3';
            $('#parent_code').val(defaultParent).trigger('change');
            loadAccounts(defaultParent, '');
        }

        $(document).on('change', '#branch_id', function () {
            getBillNo(); 
            $('#amount').val(0); 
            $('#parent_code').val('').trigger("change"); 
            $('#catch_details_table tbody').html($('#catch_details_table tbody tr:first').prop('outerHTML'));
            initCatchSelects($('#catch_details_table tbody'));
            $('#payment_type').val(0).trigger("change"); 
            $('#amount').val(0); 
            $('#notes').val(''); 
            
        });

        function getBillNo() {
            let bill_number = document.getElementById('docNumber');  
            let branch_id = document.getElementById('branch_id').value;

            if (!branch_id) {
                bill_number.value = '';
                return;
            }
    
            $.ajax({
                type: 'get',
                url: catchNoUrl.replace('BRANCH_ID', branch_id),
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
            getBillNo();
            $(".modal-body #id").val(0);  
            $('#catch_details_table tbody').html($('#catch_details_table tbody tr:first').prop('outerHTML'));
            initCatchSelects($('#catch_details_table tbody'));
            const defaultParent = '3';
            $(".modal-body #parent_code").val(defaultParent).trigger('change'); 
            loadAccounts(defaultParent, '');
            $(".modal-body #amount").val(0);
            $(".modal-body #payment_type").val(0); 
            $(".modal-body #date").attr('readOnly' , false);
            $(".modal-body #amount").attr('readOnly' , false); 
            $(".modal-body #notes").attr('disabled' , false); 
            $(".modal-body #payment_type").attr('disabled' , false); 
            $(".modal-body #submitBtn").show();
            $(".modal-body #printtBtn").hide(); 
        });

        function loadAccounts(parentCode, selectedId = '') {
            if (!parentCode) {
                $('.detail-account').each(function(){
                    $(this).empty().append('<option value=""></option>').val('').trigger('change');
                });
                return;
            }
            $.ajax({
                type: 'get',
                url: supplierAccountsUrl.replace('ACCOUNT_TYPE', parentCode),
                dataType: 'json',
                success: function (response) {
                    $('.detail-account').each(function(){
                        const $select = $(this);
                        $select.empty().append('<option value=""></option>');
                        if (response) {
                            for (let i = 0; i < response.length; i++) {
                                $select.append('<option value="' + response[i].id + '">' + response[i].name + '</option>');
                            }
                        }
                        $select.val(selectedId).trigger('change');
                    });
                }
            });
        }

        $('#parent_code').on('change select2:select', function (){
            loadAccounts(this.value);
        });   

        $(document).on('click', '#add_catch_detail', function(){
            const $row = $('#catch_details_table tbody tr:first').clone();
            $row.find('input').val('');
            $row.find('.select2-container').remove();
            $row.find('select').val('').removeAttr('id').trigger('change');
            $row.find('#amount').removeAttr('id');
            $('#catch_details_table tbody').append($row);
            initCatchSelects($row);
        });

        $(document).on('click', '.remove-detail', function(){
            const $rows = $('#catch_details_table tbody tr');
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
                url: getCatchUrl.replace('CATCH_ID', id),
                dataType: 'json',

                success:function(response){
                    console.log(response.payment_type);
                    if(response){
                        $('#createModal').modal("show");
                        $(".modal-body #date").val(response.date );
                        $(".modal-body #notes").val(response.notes);
                        $(".modal-body #docNumber").val(response.docNumber); 
                        $(".modal-body #id").val( response.id );
                        const selectedType = response.account_type ?? response.parent_code;
                        $(".modal-body #parent_code").val(selectedType).trigger('change');
                        loadAccounts(selectedType, response.to_account);
                        $(".modal-body #amount").val(response.amount); 
                        $(".modal-body #payment_type").val(response.payment_type); 
                        $(".modal-body #date").attr('readOnly' , true);
                        $(".modal-body #amount").attr('readOnly' , true); 
                        $(".modal-body #notes").attr('disabled' , true); 
                        $(".modal-body #payment_type").attr('disabled' , true); 
                        $(".modal-body #submitBtn").hide();
                        $(".modal-body #printtBtn").show();
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
            url   = "{{ route('printCatch', ':id') }}";
            url = url.replace(':id', val);
            document.location.href = url;
        });  
        
    });
    function printPage(){ 
        var css = '@page { size:A4 portrait; }',
            head = document.head || document.getElementsByTagName('head')[0],
            style = document.createElement('style');

        style.type = 'text/css';
        style.media = 'print';

        if (style.styleSheet){
            style.styleSheet.cssText = css;
        } else {
            style.appendChild(document.createTextNode(css));
        }

        head.appendChild(style);

        const originalHTML = document.body.innerHTML;
        document.body.innerHTML = document.getElementById('createModal').innerHTML;
        document.querySelectorAll('.not-print')
            .forEach(img => img.remove())
        window.print();
        document.body.innerHTML = originalHTML ;

    }
    function confirmDelete(){
        let url = "{{ route('catche_destroy', ':id') }}";
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
@endsection 
 
