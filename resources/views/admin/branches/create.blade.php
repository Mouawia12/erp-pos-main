@extends('admin.layouts.master')
<style>
</style>
@section('content')
    <!-- main-content closed -->
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
    @if (session('error'))
        <div class="alert alert-danger fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('error') }}
        </div>
    @endif
    @can('اضافة فرع') 
    <!-- row opened -->
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h4 class="alert alert-primary  text-center">
                        اضافة فرع جديد
                    </h4>
                </div>
                <div class="card-body" style="padding:5%;">
                    <form action="{{route('admin.branches.store')}}" method="post"
                          enctype="multipart/form-data">
                        {{csrf_field()}}
                        <div class="row m-t-3 mb-3">
                            <input type="hidden" name="status" value="1"/>
                            <div class="col-md-4">
                                <label> اسم الفرع <span class="text-danger"> </span></label>
                                <input class="form-control mg-b-20" name="branch_name" required="" type="text">
                            </div>
    
                            <div class="col-md-4">
                                <label> السجل التجاري </label>
                                <input class="form-control mg-b-20" name="cr_number" type="text">
                            </div>

                            <div class="col-md-4">
                                <label> الرقم الضريبي </label>
                                <input class="form-control mg-b-20" name="tax_number" type="text">
                            </div>
                        </div>
                        <div class="row m-t-3 mb-3">
                            <div class="col-md-4">
                                <label> التلفون <span class="text-danger"> </span></label>
                                <input class="form-control mg-b-20" min="1" dir="ltr" name="branch_phone" required="" type="number">
                            </div>
    
                            <div class="col-md-4">
                                <label> العنوان <span class="text-danger"> </span></label>
                                <input class="form-control mg-b-20" dir="rtl" name="branch_address" required="" type="text">
                            </div>

                            <div class="col-md-4">
                                <label> مدير الفرع </label>
                                <input class="form-control mg-b-20" name="manager_name" type="text">
                            </div>
                        </div>
                        <div class="row m-t-3 mb-3">
                            <div class="col-md-4">
                                <label> البريد الإلكتروني </label>
                                <input class="form-control mg-b-20" name="contact_email" type="email">
                            </div>
                            <div class="col-md-4">
                                <label>{{ __('main.invoice_type') }} الافتراضي للفرع</label>
                                <select class="form-control" name="default_invoice_type">
                                    <option value="tax_invoice" @if(($defaultInvoiceType ?? '')==='tax_invoice') selected @endif>{{ __('main.invoice_type_tax') }}</option>
                                    <option value="simplified_tax_invoice" @if(($defaultInvoiceType ?? 'simplified_tax_invoice')==='simplified_tax_invoice') selected @endif>{{ __('main.invoice_type_simplified') }}</option>
                                    <option value="non_tax_invoice" @if(($defaultInvoiceType ?? '')==='non_tax_invoice') selected @endif>{{ __('main.invoice_type_nontax') }}</option>
                                </select>
                            </div>
                        </div>  
                        <div class="row m-t-3 mb-3">
                            <div class="col-12">
                                <hr>
                                <h6>{{ __('main.national_address') ?? 'العنوان الوطني' }}</h6>
                            </div>
                            <div class="col-md-4">
                                <label>{{ __('main.national_address_short') ?? 'العنوان المختصر' }}</label>
                                <input class="form-control mg-b-20" name="national_address_short" type="text">
                            </div>
                            <div class="col-md-4">
                                <label>{{ __('main.national_address_building_no') ?? 'رقم المبنى' }}</label>
                                <input class="form-control mg-b-20" name="national_address_building_no" type="text">
                            </div>
                            <div class="col-md-4">
                                <label>{{ __('main.national_address_street') ?? 'الشارع' }}</label>
                                <input class="form-control mg-b-20" name="national_address_street" type="text">
                            </div>
                            <div class="col-md-4">
                                <label>{{ __('main.national_address_district') ?? 'الحي' }}</label>
                                <input class="form-control mg-b-20" name="national_address_district" type="text">
                            </div>
                            <div class="col-md-4">
                                <label>{{ __('main.national_address_city') ?? 'المدينة' }}</label>
                                <input class="form-control mg-b-20" name="national_address_city" type="text">
                            </div>
                            <div class="col-md-4">
                                <label>{{ __('main.national_address_region') ?? 'المنطقة' }}</label>
                                <input class="form-control mg-b-20" name="national_address_region" type="text">
                            </div>
                            <div class="col-md-4">
                                <label>{{ __('main.national_address_postal_code') ?? 'الرمز البريدي' }}</label>
                                <input class="form-control mg-b-20" name="national_address_postal_code" type="text">
                            </div>
                            <div class="col-md-4">
                                <label>{{ __('main.national_address_additional_no') ?? 'الرقم الإضافي' }}</label>
                                <input class="form-control mg-b-20" name="national_address_additional_no" type="text">
                            </div>
                            <div class="col-md-4">
                                <label>{{ __('main.national_address_unit_no') ?? 'رقم الوحدة' }}</label>
                                <input class="form-control mg-b-20" name="national_address_unit_no" type="text">
                            </div>
                            <div class="col-md-4">
                                <label>{{ __('main.national_address_proof_no') ?? 'رقم إثبات العنوان' }}</label>
                                <input class="form-control mg-b-20" name="national_address_proof_no" type="text">
                            </div>
                            <div class="col-md-4">
                                <label>{{ __('main.national_address_proof_issue_date') ?? 'تاريخ الإصدار' }}</label>
                                <input class="form-control mg-b-20" name="national_address_proof_issue_date" type="date">
                            </div>
                            <div class="col-md-4">
                                <label>{{ __('main.national_address_proof_expiry_date') ?? 'تاريخ الانتهاء' }}</label>
                                <input class="form-control mg-b-20" name="national_address_proof_expiry_date" type="date">
                            </div>
                            <div class="col-md-4">
                                <label>{{ __('main.country') ?? 'الدولة' }}</label>
                                <input class="form-control mg-b-20" name="national_address_country" type="text" value="SA">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                <button class="btn btn-info pd-x-20" type="submit">
                                    <i class="fa fa-plus"></i> اضافة
                                </button>
                            </div>
                        </div>  

                    </form>
                </div>
            </div>
        </div>
    </div>
@endcan 
@endsection  
