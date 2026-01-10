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
  
    @can('عرض مورد') 
 
    <!-- End Navbar -->
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0"  id="head-right" >
                        <div class="col-lg-12 margin-tb">
                            <h4  class="alert alert-primary text-center">
                            {{ __('main.supplier')}}
                            </h4>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="row mt-1 mb-1 text-center justify-content-center align-content-center"> 
                        @can('اضافة مورد')  
                        <button type="button" class="btn btn-labeled btn-info" id="createButton">
                            <span class="btn-label" style="margin-right: 10px;">
                            <i class="fa fa-plus"></i></span>
                            {{__('main.add_new')}}
                        </button>
                        @endcan 
                    </div> 
                    <div class="clearfix"><hr></div> 
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive hoverable-table">
                            <table class="display w-100 text-nowrap table-bordered" id="example1" 
                                   style="text-align: center;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th class="col-md-3">{{__('main.company')}}</th>
                                        <th class="col-md-3">{{__('main.name')}}</th>
                                        <th>{{__('main.phone')}}</th>
                                        <th>{{__('main.email')}}</th>
                                        @if($type == 3)
                                        <th>{{__('main.c_groups')}}</th>
                                        @endif
                                        <th>{{__('main.vat_no')}}</th>
                                        <th>{{__('main.balance')}}</th>
                                        <th>{{__('main.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @php
                                   $i = 0;
                                @endphp
                                @foreach($companies as $company)
                                    @if($company -> group_id == $type)
                                        @php
                                           $i++;
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{$i}}</td>
                                            <td class="text-center">{{$company -> company}}</td>
                                            <td class="text-center">{{$company -> name}}</td>
                                            <td class="text-center">{{$company -> phone}}</td>
                                            <td class="text-center">{{$company -> email}}</td>
                                            @if($type == 3)
                                            <td class="text-center">{{ $company -> group ? $company -> group -> name : '---'}}</td>
                                            @endif
                                            <td class="text-center">{{$company->tax_number ?? $company->vat_no}}</td>
                                            <td class="text-center">{{$company -> deposit_amount}}</td>
                                            <td class="text-center">
                                               
                                               @can('التقارير المحاسبية')     
                                                <a href="{{route('account.company.report.search', $company ->account_id)}}" type="button" class="btn btn-labeled btn-success"  onclick="openReport({{$company -> id}})">
                                                    <span class="btn-label" style="margin-right: 10px;">
                                                    <i class="fa fa-chart"></i></span>
                                                    {{__('main.Report')}}
                                                </a>
                                                @endcan  
                                                
                                                @can('تعديل مورد')  
                                                <button type="button" class="btn btn-labeled btn-info " onclick="EditModal({{$company -> id}})">
                                                    <span class="btn-label" style="margin-right: 10px;">
                                                    <i class="fa fa-pen"></i></span>{{__('main.edit')}}
                                                </button>
                                                @endcan 
                                                
                                               @can('حذف مورد')  
                                                <button type="button" class="btn btn-labeled btn-danger deleteBtn "  id="{{$company -> id}}">
                                                    <span class="btn-label" style="margin-right: 10px;">
                                                    <i class="fa fa-trash"></i></span>
                                                    {{__('main.delete')}}
                                                </button>
                                                @endcan 
        
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
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
<div class="modal fade" id="createModal"  role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <label class="modelTitle"> {{$type == 3 ? __('main.clients') : __('main.supplier')}}</label>
                <button type="button" class="close modal-close-btn"  data-bs-dismiss="modal"  aria-label="Close" >
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="paymentBody">
                <form  method="POST" action="{{ route('storeCompany') }}"
                        enctype="multipart/form-data" >
                    @csrf
                    <input type="hidden" id="id" name="id"/>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>{{ __('main.company') }} <span class="text-danger">*</span> </label>
                                <input type="text" id="company" name="company"
                                       class="form-control"
                                       placeholder="{{ __('main.company') }}"/> 
                            </div>
                        </div>
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.name') }} <span class="text-danger">*</span> </label>
                                <input type="text"  id="name" name="name"
                                       class="form-control"
                                       placeholder="{{ __('main.name') }}"  />
                                <input type="text"  id="type" name="type"
                                       class="form-control" value="{{$type}}"
                                       placeholder="{{ __('main.name') }}"  hidden />

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.phone') }} <span class="text-danger">*</span> </label>
                                <input type="text"  id="phone" name="phone"
                                       class="form-control"
                                       placeholder="{{ __('main.phone') }}"  />
                            </div>
                        </div>
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.email') }} <span class="text-danger"></span> </label>
                                <input type="text"  id="email" name="email"
                                       class="form-control"
                                       placeholder="{{ __('main.email') }}"  />
                            </div>
                        </div>
                        <div class="col-6" >
                            <div class="form-group">
                                <label>{{ __('main.address') }} <span class="text-danger">*</span> </label>
                                <textarea type="text"  id="address" name="address" class="form-control" placeholder="{{ __('main.address') }}"></textarea>
                            </div>
                        </div> 
                 
                        @if($type == 3)
                            <div class="col-6" >
                                <div class="form-group">
                                    <label>{{ __('main.c_groups') }} <span class="text-danger">*</span> </label>
                                    <select class="js-example-basic-single w-100"
                                            name="customer_group_id" id="customer_group_id">
                                        <option selected value ="0">Choose...</option>
                                        @foreach ($groups as $item)
                                            <option value="{{$item -> id}}"> {{ $item -> name}}</option>

                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                        <div @if($type == 3) class="col-6 " @else  class="col-6" @endif>
                            <div class="form-group">
                                <label>{{ __('main.account') }} <span class="text-danger">*</span> </label>
                                <select class="js-example-basic-single w-100"
                                        name="account_id" id="account_id" required>
                                    <option selected value ="0">{{ __('main.choose') }}</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{$account -> id}}"> {{ $account -> name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.vat_no') }} <span class="text-danger">*</span> </label>
                                <input type="text"  id="tax_number" name="tax_number"
                                       class="form-control"
                                       placeholder="{{ __('main.vat_no') }}"  />
                            </div>
                        </div>
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.cr_number') ?? 'CR' }}</label>
                                <input type="text"  id="cr_number" name="cr_number"
                                       class="form-control"
                                       placeholder="{{ __('main.cr_number') ?? 'CR' }}"  />
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>{{ __('main.representatives') }}</label>
                                <select class="form-control" id="representative_id_" name="representative_id_">
                                    <option value="">{{ __('main.choose') }}</option>
                                    @foreach($representatives as $rep)
                                        <option value="{{$rep->id}}">{{$rep->user_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>{{ __('main.cost_center') }}</label>
                                <select class="form-control" id="cost_center_id" name="cost_center_id">
                                    <option value="">{{ __('main.choose') }}</option>
                                    @foreach($costCenters as $center)
                                        <option value="{{$center->id}}">{{$center->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if($type != 3)
                            <div class="col-6">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="is_default_supplier" name="is_default_supplier" value="1">
                                    <label class="form-check-label" for="is_default_supplier">
                                        {{ __('main.default_supplier') ?? 'مورد افتراضي' }}
                                    </label>
                                </div>
                            </div>
                        @endif
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.opening_balance') }} <span class="text-danger">*</span> </label>
                                <input type="number" step="any"  id="opening_balance" name="opening_balance"
                                       class="form-control"
                                       placeholder="{{ __('main.opening_balance') }}"  />
                            </div>
                        </div>
                    </div>
                    @if($type == 3)
                        <div class="row" >
                            <div class="col-6 " >
                                <div class="form-group">
                                    <label>{{ __('main.credit_limit') }} <span class="text-danger">*</span> </label>
                                    <input type="text"  id="credit_amount" name="credit_amount"
                                           class="form-control"
                                           placeholder="{{ __('main.credit_limit') }}"  />
                                </div>
                            </div>
                            <div class="col-6 " >
                                <div class="form-group">
                                    <label>{{ __('main.stop_sale') }} <span class="text-danger">*</span> </label>
                                    <input type="checkbox"   id="stop_sale" name="stop_sale"
                                           class="form-check" style="width: 20px;"
                                           placeholder="{{ __('main.opening_balance') }}"  />
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-12">
                            <hr>
                            <h6>{{ __('main.national_address') ?? 'العنوان الوطني' }}</h6>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>{{ __('main.national_address_short') ?? 'العنوان المختصر' }}</label>
                                <input type="text" id="national_address_short" name="national_address_short" class="form-control" />
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>{{ __('main.national_address_building_no') ?? 'رقم المبنى' }}</label>
                                <input type="text" id="national_address_building_no" name="national_address_building_no" class="form-control" />
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>{{ __('main.national_address_street') ?? 'الشارع' }}</label>
                                <input type="text" id="national_address_street" name="national_address_street" class="form-control" />
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>{{ __('main.national_address_district') ?? 'الحي' }}</label>
                                <input type="text" id="national_address_district" name="national_address_district" class="form-control" />
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>{{ __('main.national_address_city') ?? 'المدينة' }}</label>
                                <input type="text" id="national_address_city" name="national_address_city" class="form-control" />
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>{{ __('main.national_address_region') ?? 'المنطقة' }}</label>
                                <input type="text" id="national_address_region" name="national_address_region" class="form-control" />
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>{{ __('main.national_address_postal_code') ?? 'الرمز البريدي' }}</label>
                                <input type="text" id="national_address_postal_code" name="national_address_postal_code" class="form-control" />
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>{{ __('main.national_address_additional_no') ?? 'الرقم الإضافي' }}</label>
                                <input type="text" id="national_address_additional_no" name="national_address_additional_no" class="form-control" />
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>{{ __('main.national_address_unit_no') ?? 'رقم الوحدة' }}</label>
                                <input type="text" id="national_address_unit_no" name="national_address_unit_no" class="form-control" />
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>{{ __('main.national_address_proof_no') ?? 'رقم إثبات العنوان' }}</label>
                                <input type="text" id="national_address_proof_no" name="national_address_proof_no" class="form-control" />
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>{{ __('main.national_address_proof_issue_date') ?? 'تاريخ الإصدار' }}</label>
                                <input type="date" id="national_address_proof_issue_date" name="national_address_proof_issue_date" class="form-control" />
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>{{ __('main.national_address_proof_expiry_date') ?? 'تاريخ الانتهاء' }}</label>
                                <input type="date" id="national_address_proof_expiry_date" name="national_address_proof_expiry_date" class="form-control" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
 
                    </div>
                    <div class="row">
                        <div class="col-6" style="display: block; margin: 20px auto; text-align: center;">
                            <button type="submit" class="btn btn-labeled btn-primary"  >
                                {{__('main.save_btn')}}</button>
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
                <button type="button" class="close"  data-bs-dismiss="modal"  aria-label="Close" style="color: red; font-size: 20px; font-weight: bold;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="smallBody">
                <img src="../assets/img/warning.png" class="alertImage">
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
    let id = 0 ;
    $(document).ready(function()
    {
        if (new URLSearchParams(window.location.search).get('create') === '1') {
            $('#createButton').trigger('click');
        }
        id = 0 ;
        $(document).on('click', '#createButton', function(event) {
            id = 0 ;
            event.preventDefault();
            let href = $(this).attr('data-attr');
            let url = "{{ route('settings') }}";
            $.ajax({
                type:'get',
                url:url,
                dataType: 'json',

                success:function(response) {
                    $.ajax({
                        url: href,
                        beforeSend: function() {
                            $('#loader').show();
                        },
                        // return the result
                        success: function(result) {
                            $('#createModal').modal("show");
                            $(".modal-body #company").val("");
                            $(".modal-body #name").val( "" );
                            $(".modal-body #phone").val( "" );
                            $(".modal-body #email").val( "" );
                            $(".modal-body #account_id").val( "" ).trigger("change");
                            $(".modal-body #tax_number").val( "" );
                            $(".modal-body #opening_balance").val( "0" );
                            $(".modal-body #cost_center_id").val( "" ).trigger("change");
                            $(".modal-body #is_default_supplier").prop('checked', false);
                            try {
                                $(".modal-body #customer_group_id").val( response.client_group_id );
                                $(".modal-body #credit_amount").val( "0" );
                                $(".modal-body #stop_sale").prop('checked' ,0);
                            }catch (err){

                            }
                            $(".modal-body #address").val( "" );
                            $(".modal-body #national_address_short").val( "" );
                            $(".modal-body #national_address_building_no").val( "" );
                            $(".modal-body #national_address_street").val( "" );
                            $(".modal-body #national_address_district").val( "" );
                            $(".modal-body #national_address_city").val( "" );
                            $(".modal-body #national_address_region").val( "" );
                            $(".modal-body #national_address_postal_code").val( "" );
                            $(".modal-body #national_address_additional_no").val( "" );
                            $(".modal-body #national_address_unit_no").val( "" );
                            $(".modal-body #national_address_proof_no").val( "" );
                            $(".modal-body #national_address_proof_issue_date").val( "" );
                            $(".modal-body #national_address_proof_expiry_date").val( "" );
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
                }
        });



        });
        $(document).on('click', '.deleteBtn', function(event) {
             id = event.currentTarget.id ;
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
        $(document).on('click' , '.modal-close-btn' , function (event) {
            $('#createModal').modal("hide");
            id = 0 ;
        });


    });
    function confirmDelete(){
        let url = "{{ route('deleteCompany', ':id') }}";
        url = url.replace(':id', id);
        document.location.href=url;
    }
    function EditModal(id){
        let url = "{{ route('getCompany', ':id') }}";
        url = url.replace(':id', id);
        $.ajax({
            type:'get',
            url:url,
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
                            $(".modal-body #company").val( response.company );
                            $(".modal-body #name").val( response.name );
                            $(".modal-body #phone").val(  response.phone );
                            $(".modal-body #email").val(  response.email );
                            $(".modal-body #account_id").val(  response.account_id ).trigger("change");
                            $(".modal-body #tax_number").val(  response.tax_number ?? response.vat_no );
                            $(".modal-body #cr_number").val(  response.cr_number );
                            $(".modal-body #representative_id_").val(  response.representative_id_ ).trigger('change');
                            $(".modal-body #cost_center_id").val( response.cost_center_id || "" ).trigger('change');
                            $(".modal-body #is_default_supplier").prop('checked', response.is_default_supplier == 1);
                            $(".modal-body #opening_balance").val(  response.opening_balance );
                            try {
                                $(".modal-body #customer_group_id").val(  response.customer_group_id );
                                $(".modal-body #credit_amount").val(  response.credit_amount );
                                $(".modal-body #stop_sale").prop('checked' , response.stop_sale);
                            }catch (err){

                            }
                            $(".modal-body #address").val(  response.address );
                            $(".modal-body #national_address_short").val( response.national_address_short );
                            $(".modal-body #national_address_building_no").val( response.national_address_building_no );
                            $(".modal-body #national_address_street").val( response.national_address_street );
                            $(".modal-body #national_address_district").val( response.national_address_district );
                            $(".modal-body #national_address_city").val( response.national_address_city );
                            $(".modal-body #national_address_region").val( response.national_address_region );
                            $(".modal-body #national_address_postal_code").val( response.national_address_postal_code );
                            $(".modal-body #national_address_additional_no").val( response.national_address_additional_no );
                            $(".modal-body #national_address_unit_no").val( response.national_address_unit_no );
                            $(".modal-body #national_address_proof_no").val( response.national_address_proof_no );
                            $(".modal-body #national_address_proof_issue_date").val( response.national_address_proof_issue_date );
                            $(".modal-body #national_address_proof_expiry_date").val( response.national_address_proof_expiry_date );
                            $(".modal-body #id").val(  response.id );

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

    function openReport(id) {
        const type = document.getElementById('type').value ;
        let url = "{{ route('client_balance_report', [':id' , ':slag']) }}";
        url = url.replace(':id', id);
        url = url.replace(':slag', type);

        window.location.href=url;
    }
</script>
@endsection 
