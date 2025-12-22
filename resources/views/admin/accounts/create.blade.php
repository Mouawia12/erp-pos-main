@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif

    <style>
        .itemCaRD{
            background: white;
            width: 90%;
            display: block;
            margin: 50px auto;
            border-radius: 25px;
        }
    </style>
<!-- row opened -->
@can('اضافة حسابات')
<div class="row row-sm">
    <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0 text-center">
                    <div class="col-lg-12 margin-tb ">
                        <h4  class="alert alert-primary text-center"> 
                            {{__('main.add_account')}}
                        </h4>
                    </div>
                    <div class="clearfix"></div> 
                </div>  
            </div>  
                <div class="card-body px-0 pt-0 pb-2">
                  <div class="card shadow mb-4"> 
                    <form   method="POST" action="{{ route('store_account') }}">
                        @csrf

                        <div class="row" style="padding: 20px">
                            <div class="col-md-12 col-sm-12">

                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{ __('main.code') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                            <input type="text"  id="code" name="code"
                                                   class="form-control @error('code') is-invalid @enderror"
                                                   placeholder="{{ __('main.code') }}"  />
                                            @error('code')
                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6 " >
                                        <div class="form-group">
                                            <label>{{ __('main.name') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span>  </label>
                                            <input type="text"  id="name" name="name"
                                                   class="form-control @error('name') is-invalid @enderror"
                                                   placeholder="{{ __('main.name') }}"  />
                                            @error('name')
                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">


                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{ __('main.account_type') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                            <select class="form-control @error('type') is-invalid @enderror" id="account_type" name="type">
                                                <option value="0">{{__('main.Root')}}</option>
                                                <option value="1">{{__('main.General')}}</option>
                                                <option value="2">{{__('main.Branch')}}</option>
                                                <option value="3">{{__('main.Branch_Ledger')}}</option>
                                            </select>
                                            @error('type')
                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{ __('main.parent_id') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                            <select class="form-control @error('brand') is-invalid @enderror" id="parent_id" name="parent_id" disabled>
                                                <option value="0"></option>
                                                @foreach($accounts as $brand)
                                                    <option value="{{$brand->id}}">{{$brand->name}}</option>
                                                @endforeach
                                            </select>
                                            @error('brand')
                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-6 " >
                                        <div class="form-group">
                                            <label>{{ __('main.account_level') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span>  </label>
                                            <input type="text"  id="level" name="level" readonly  value="1"
                                                   class="form-control @error('name') is-invalid @enderror"
                                                   placeholder="{{ __('main.account_level') }}"  />
                                            @error('name')
                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{ __('main.account_list') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                            <select class="form-control @error('type') is-invalid @enderror" id="list" name="list">
                                                <option value="1">{{__('main.Assets')}}</option>
                                                <option value="2">{{__('main.Discounts')}}</option>
                                                <option value="3">{{__('main.Income')}}</option>
                                                <option value="4">{{__('main.Expenses')}}</option>
                                            </select>
                                            @error('type')
                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{ __('main.account_department') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                            <select class="form-control @error('type') is-invalid @enderror" id="department" name="department">
                                                <option value="1">{{__('main.Balance_Sheet')}}</option>
                                                <option value="2">{{__('main.Incoming_List')}}</option>
                                            </select>
                                            @error('type')
                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{ __('main.account_side') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                            <select class="form-control @error('type') is-invalid @enderror" id="side" name="side">
                                                <option value="1">{{__('main.Debit')}}</option>
                                                <option value="2">{{__('main.Credit')}}</option>
                                            </select>
                                            @error('type')
                                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>{{ __('main.status') }}</label>
                                            <select class="form-control" id="is_active" name="is_active">
                                                <option value="1" selected>{{__('main.status1')}}</option>
                                                <option value="0">{{__('main.status2')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>


                            </div>

                        </div> 
                            <div class="col-12" style="text-align: center;">
                                <button type="submit" class="btn btn-labeled btn-primary"  >
                                    {{__('main.save_btn')}}</button>
                            </div> 
                    </form>
                </div>

                </div>


            </div>
            <!-- /.container-fluid -->
            <input id="local" value="{{Config::get('app.locale')}}" hidden>
        </div>
        <!-- End of Main Content -->
 

    </div>
    <!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper --> 
@endcan 
@endsection 
@section('js')
<script type="text/javascript">
$(document).ready(function () {
    $('#account_type').change(function () {
        var t = $(this).val();
        $('#parent_id').val(0).trigger('change');
        $('#level').val(1);
        if (t == 0) {
            $('#parent_id').attr('disabled', true);
        }
        else if (t == 1) {
            $('#parent_id').attr('disabled', false);
        }
        else if (t == 2) {
            $('#parent_id').attr('disabled', false);
        }
        else if (t == 3) {
            $('#parent_id').attr('disabled', false);
        }
    });


    $('#parent_id').change(function () {
        var parent = $(this).val();
        if(parent == 0)
            return;
        var url = '{{route('get_account_level',":id")}}';
        url = url.replace(":id",parent);
        $.ajax({
            type: "get", async: false,
            url: url,
            dataType: "json",
            success: function (data) {
                $('#level').val(+data['account']['level']+1);
                $('#list').val(+data['account']['list']).trigger('change');
                $('#department').val(+data['account']['department']).trigger('change');

            }
        });


    });
});
</script>
@endsection 
