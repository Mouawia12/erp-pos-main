@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
  
    @can('التقارير المخزون')   
    <!-- End Navbar -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card"> 
                <div class="card shadow mb-4">
                    <div class="card-header py-3 " id="head-right"  style="direction: rtl;border:solid 1px gray"> 
                        <header>
                            <div class="row" style="direction: ltr;">
                                <div class="col-4 text-left">    
                                </div>
                                <div class="col-4 c">
                                    <h4 class="alert alert-primary text-center">
                                        {{__('main.client_movement_report')}}
                                    </h4> 
                                    <h3 class="text-center">@if(isset($data[0]->company->name)) {{$data[0]->company->name}} @endif </h3> 
                                    <h5 class="text-center"> 
                                        [ الفترة : من البداية - حتى اليوم ]
                                    </h5>
                                </div>
                                <div class="col-4 c"> 
                                    <span style="text-align: right;">
                                        {{$company ? $company -> name_ar : ''}}
                                     <br>  س.ت : {{$company ? $company -> taxNumber : ''}}
                                     <br>  ر.ض :  {{$company ? $company -> registrationNumber : ''}}
                                     <br>  تليفون :   {{$company ? $company -> phone : ''}}
                                    </span>
                                </div>
                            </div>
                        </header> 
                    </div>
                </div>      
                <div class="card-body">
                    <form method="GET" action="{{ route('client_balance_report', [$companyId ?? 0, $slag ?? 0]) }}" class="mb-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="mb-1">{{ __('main.from_date') ?? 'من تاريخ' }}</label>
                                <input type="date" name="start_date" class="form-control" value="{{ $startDateValue ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label class="mb-1">{{ __('main.to_date') ?? 'إلى تاريخ' }}</label>
                                <input type="date" name="end_date" class="form-control" value="{{ $endDateValue ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label class="mb-1">{{ __('main.branche') ?? 'الفرع' }}</label>
                                <select name="branch_id" class="form-control">
                                    <option value="0">{{ __('main.all') ?? 'كل الفروع' }}</option>
                                    @foreach($branches ?? [] as $branch)
                                        <option value="{{ $branch->id }}" @if(($branchId ?? 0) == $branch->id) selected @endif>
                                            {{ $branch->branch_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100">{{ __('main.search') ?? 'بحث' }}</button>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('client_balance_report', [$companyId ?? 0, $slag ?? 0]) }}" class="btn btn-outline-secondary w-100">
                                    {{ __('main.reset') ?? 'إعادة تعيين' }}
                                </a>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive hoverable-table" style="direction: rtl;"> 
                        <table class="display w-100 text-nowrap table-bordered" id="example1" 
                           style="text-align: center;direction: rtl;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{__('main.date')}}</th>
                                    <th>{{__('main.InvoiceType')}}</th>
                                    <th>{{__('main.bill_number')}}</th>
                                    <th>{{__('main.Debit')}}</th>
                                    <th>{{__('main.Credit')}}</th> 
                                    <th>{{__('main.balance')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @php 
                                $net = 0;
                                $balance = 0;
                                
                                if(isset($account_balance) and $account_balance->side == 1){
                                    $balance = $account_balance->before_debit - $account_balance->before_credit; 
                                    $balance_debit = $balance;
                                    $balance_credit = 0;
                                }elseif(isset($account_balance) and $account_balance->side == 2){  
                                    $balance = $account_balance->before_credit - $account_balance->before_debit;
                                    $balance_debit = 0;
                                    $balance_credit = $balance;
                                }else{
                                    $balance_debit = 0;
                                    $balance_credit = 0;
                                    $balance = 0;
                                }
                                  
                                $net = $balance;
                            @endphp   
                            @foreach($data as $index=>$unit)
                                <tr>
                                    <td class="text-center">{{$loop->index+1}}</td>
                                    <td class="text-center">{{$unit->date}}</td>
                                    <td class="text-center">
                                        @if($unit->invoice_type == 'Sales') فاتورة مبيعات
                                        @elseif($unit->invoice_type == 'Sale_Payment') مدفوعات فاتورة مبيعات
                                        @elseif($unit->invoice_type == 'Purchases') فاتورة مشتريات
                                        @elseif($unit->invoice_type == 'Purchase_Payment') مدفوعات فاتورة مشتريات
                                        @elseif($unit->invoice_type == 'Opening_Balance') رصيد افتتاحي
                                        @endif
                                    </td>
                                    <td class="text-center">{{$unit->invoice_no}}</td>
                                    <td class="text-center">{{$unit->debit}}</td>
                                    <td class="text-center">{{$unit->credit}}</td> 
                                    <td class="text-center">
                                        {{$balance = $balance +($unit->debit - $unit->credit)}}
                                    </td>
                                    @php 
                                        if($unit->side == 1){
                                            $net += ($unit->debit - $unit->credit);
                                        }else{
                                            $net += ($unit->credit - $unit->debit);
                                        }    
    
                                    @endphp
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="background: #c3e6cb">
                                   <td class="text-center" colspan="3"></td>
                                    <td class="text-center">{{__('main.total_balance')}}</td>
                                    <td class="text-center" colspan="2"></td>
                                    <td class="text-center" >{{number_format($net,2)}}</td>  
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
{{--                    <div class="card-body px-0 pt-0 pb-2">--}}
{{--                        <div class="row">--}}
{{--                            <div class="col-6">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label>{{ __('main.from_date') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>--}}
{{--                                    <input type="checkbox" name="is_from_date" id="is_from_date"/>--}}
{{--                                    <input type="date"  id="from_date" name="from_date"--}}
{{--                                           class="form-control"--}}
{{--                                    />--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="col-6">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label>{{ __('main.to_date') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>--}}
{{--                                    <input type="checkbox" name="is_to_date" id="is_to_date"/>--}}
{{--                                    <input type="date"  id="to_date" name="to_date"--}}
{{--                                           class="form-control"--}}
{{--                                    />--}}
{{--                                </div>--}}
{{--                            </div>--}}


{{--                        </div>--}}


{{--                        <div class="row">--}}
{{--                            <div class="col-md-12 text-center">--}}
{{--                                <input type="submit" class="btn btn-primary" id="excute" tabindex="-1"--}}
{{--                                       style="width: 150px;--}}
{{--margin: 30px auto;" value="{{__('main.excute_btn')}}"></input>--}}

{{--                            </div>--}}
{{--                        </div>--}}

{{--                    </div>--}}
                </div>
            </div>
        </div> 
    </div> 

<div class="show_modal">

</div>
@endcan 
@endsection 
@section('js') 
<script>
    $(document).ready(function () {
        document.title = " {{__('main.client_movement_report')}}";
    });
</script>
@endsection 
