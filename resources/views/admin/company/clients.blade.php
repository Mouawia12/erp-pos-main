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
    @can('عرض عميل')
    <!-- End Navbar -->
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0"  id="head-right" >
                        <div class="col-lg-12 margin-tb">
                            <h4  class="alert alert-primary text-center">
                            {{__('main.clients')}}
                            </h4>
                        </div> 
                    </div>
                    <div class="row mt-1 mb-1 text-center justify-content-center align-content-center">  
                        @can('اضافة عميل')     
                        <button type="button" class="btn btn-labeled btn-info " id="createButton">
                            <span class="btn-label" style="margin-right: 10px;">
                            <i class="fa fa-plus"></i></span>
                            {{__('main.add_new')}}
                        </button>
                        @endcan 
                    </div> 
                    <div class="clearfix"><hr></div> 
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive hoverable-table">
                            <table class="display w-100  text-nowrap table-bordered" id="example1" 
                                   style="text-align: center;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th class="col-md-3">{{__('main.company')}}</th>
                                        <th class="col-md-2">{{__('main.name')}}</th>
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
                                            <td class="text-center">{{ number_format((float) ($company->opening_balance ?? 0) + (float) ($company->deposit_amount ?? 0), 2) }}</td>
                                            <td class="text-center"> 
                                                
                                      
                                                @can('التقارير المحاسبية')     
                                                <a href="{{ route('client_balance_report', [$company->id, $type]) }}" type="button" class="btn btn-labeled btn-success">
                                                    <span class="btn-label" style="margin-right: 10px;">
                                                    <i class="fa fa-chart"></i></span>
                                                    {{__('main.Report')}}
                                                </a>
                                                @endcan  
                                                @can('تعديل عميل')
                                                <button type="button" class="btn btn-labeled btn-info " onclick="EditModal({{$company -> id}})">
                                                    <span class="btn-label" style="margin-right: 10px;">
                                                    <i class="fa fa-pen"></i></span>{{__('main.edit')}}
                                                </button>
                                                @endcan 
                                            
                                                @can('حذف عميل')
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
                <form   method="POST" action="{{ route('storeCompany') }}"
                        enctype="multipart/form-data" >
                    @csrf
                    <input type="hidden" id="id" name="id"/>
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label>{{ __('main.company') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="text"  id="company" name="company"
                                       class="form-control @error('company') is-invalid @enderror"
                                       placeholder="{{ __('main.company') }}"  value="{{ old('company') }}" /> 
                                @error('company')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-8" >
                            <div class="form-group">
                                <label>{{ __('main.name') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="text"  id="name" name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="{{ __('main.name') }}"  value="{{ old('name') }}" />
                                <input type="text"  id="type" name="type"
                                       class="form-control" value="{{$type}}"
                                       placeholder="{{ __('main.name') }}"  hidden />
                                @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4" >
                            <div class="form-group">
                                <label>{{ __('main.phone') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="text"  id="phone" name="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       placeholder="{{ __('main.phone') }}"  value="{{ old('phone') }}" />
                                @error('phone')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-4" >
                            <div class="form-group">
                                <label>{{ __('main.email') }} <span style="color:red; font-size:20px; font-weight:bold;"></span> </label>
                                <input type="text"  id="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       placeholder="{{ __('main.email') }}"  value="{{ old('email') }}" />
                                @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-4" >
                            <div class="form-group">
                                <label>{{ __('main.address') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <textarea type="text"  id="address" name="address" class="form-control @error('address') is-invalid @enderror" placeholder="{{ __('main.address') }}">{{ old('address') }}</textarea>
                                @error('address')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div> 
                        <div class="col-4">
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
                        <div class="col-4">
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
                 
                        @if($type == 3)
                            <div class="col-4" >
                                <div class="form-group">
                                    <label>{{ __('main.c_groups') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                    <select class="js-example-basic-single w-100"
                                            name="customer_group_id" id="customer_group_id">
                                        <option value="0">{{ __('main.choose') }}</option>
                                        @foreach ($groups as $item)
                                            <option value="{{$item -> id}}" @if(old('customer_group_id')==$item->id) selected @endif> {{ $item -> name}}</option>

                                        @endforeach
                                    </select>
                                    @error('customer_group_id')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        @endif

                        <div class="col-8">
                            <div class="form-group">
                                <label>{{ __('main.account') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <select class="js-example-basic-single w-100"
                                        name="account_id" id="account_id">
                                    <option value ="0">{{ __('main.choose') }}</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{$account -> id}}" @if(old('account_id')==$account->id) selected @endif> {{ $account -> name}}</option>

                                    @endforeach
                                </select>
                                @error('account_id')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                            </div>
                        </div>  
                        <div class="col-4" >
                            <div class="form-group">
                                <label>{{ __('main.vat_no') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="text"  id="tax_number" name="tax_number"
                                       class="form-control @error('tax_number') is-invalid @enderror"
                                       placeholder="{{ __('main.vat_no') }}"  value="{{ old('tax_number') }}" />
                                @error('tax_number')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-4" >
                            <div class="form-group">
                                <label>{{ __('main.cr_number') ?? 'CR' }}</label>
                                <input type="text"  id="cr_number" name="cr_number"
                                       class="form-control"
                                       placeholder="{{ __('main.cr_number') ?? 'CR' }}"  />
                            </div>
                        </div>
                        <div class="col-4" >
                            <div class="form-group">
                                <label>{{ __('main.opening_balance') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="number" step="any"  id="opening_balance" name="opening_balance"
                                       class="form-control @error('opening_balance') is-invalid @enderror"
                                       placeholder="{{ __('main.opening_balance') }}"  value="{{ old('opening_balance') }}" />
                                @error('opening_balance')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div> 
                    @if($type == 3) 
                        <div class="col-4" >
                            <div class="form-group">
                                <label>{{ __('main.credit_limit') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="text"  id="credit_amount" name="credit_amount"
                                       class="form-control @error('credit_amount') is-invalid @enderror"
                                       placeholder="{{ __('main.credit_limit') }}"  value="{{ old('credit_amount') }}" />
                                @error('credit_amount')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-4" >
                            <div class="form-group">
                                <label>{{ __('main.stop_sale') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="checkbox"   id="stop_sale" name="stop_sale"
                                       class="form-check" style="width: 20px;"
                                       placeholder="{{ __('main.opening_balance') }}"  />
                            </div>
                        </div> 
                        <div class="col-4">
                            <div class="form-group">
                                <label>{{ __('main.parent_company') ?? 'الكيان الرئيسي' }}</label>
                                <select class="form-control" id="parent_company_id" name="parent_company_id">
                                    <option value="">{{ __('main.choose') }}</option>
                                    @foreach($parentCompanies as $c)
                                        <option value="{{$c->id}}" data-tax="{{$c->tax_number ?? $c->vat_no ?? ''}}">{{$c->company}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>{{ __('main.price_level') ?? 'مستوى السعر' }}</label>
                                <select class="form-control" id="price_level_id" name="price_level_id">
                                    <option value="">{{ __('main.choose') }}</option>
                                    @for($i=1;$i<=6;$i++)
                                        <option value="{{$i}}">{{ __('main.price_level') ?? 'مستوى السعر' }} {{$i}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>{{ __('main.default_discount') ?? 'الخصم الافتراضي' }}</label>
                                <input type="number" step="0.01" class="form-control" id="default_discount" name="default_discount" value="0">
                            </div>
                        </div>
                    @endif
                    </div> 

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
                        <div class="col-6" style="display: block; margin: 20px auto; text-align: center;">
                            <button type="submit" class="btn btn-labeled btn-primary"  >
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
        @if($errors->any())
            $('#createModal').modal({backdrop:'static', keyboard:false});
        @endif
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
                            try {
                                $(".modal-body #customer_group_id").val( response.client_group_id ).trigger("change");
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

        $('#parent_company_id').on('change', function () {
            const tax = $(this).find(':selected').data('tax') || '';
            const taxInput = $('#tax_number');
            if (!taxInput.val()) {
                taxInput.val(tax);
            }
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
                            $(".modal-body #phone").val( response.phone );
                            $(".modal-body #email").val( response.email );
                            $(".modal-body #account_id").val( response.account_id ).trigger("change");
                            $(".modal-body #tax_number").val( response.tax_number ?? response.vat_no );
                            $(".modal-body #cr_number").val( response.cr_number );
                            $(".modal-body #parent_company_id").val( response.parent_company_id ).trigger('change');
                            $(".modal-body #price_level_id").val( response.price_level_id ).trigger('change');
                            $(".modal-body #default_discount").val( response.default_discount );
                            $(".modal-body #representative_id_").val( response.representative_id_ ).trigger('change');
                            $(".modal-body #cost_center_id").val( response.cost_center_id || "" ).trigger('change');
                            $(".modal-body #opening_balance").val(  response.opening_balance );
                            try {
                                $(".modal-body #customer_group_id").val( response.customer_group_id).trigger("change");
                                $(".modal-body #credit_amount").val(  response.credit_amount );
                                $(".modal-body #stop_sale").prop('checked' , response.stop_sale);
                            }catch (err){

                            }
                            $(".modal-body #address").val( response.address );
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
@endsection 
