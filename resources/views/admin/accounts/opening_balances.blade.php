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

    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0 text-center">
                    <div class="col-lg-12 margin-tb ">
                        <h4 class="alert alert-primary text-center">
                            [ {{ __('main.opening_balances') ?? 'الأرصدة الافتتاحية' }} ]
                        </h4>
                    </div>
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <form method="POST" action="{{ route('opening_balances.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <label>{{ __('main.date') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                    <input type="date" id="date" name="date" class="form-control" value="{{ old('date', now()->toDateString()) }}" required/>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>{{ __('main.notes') }} </label>
                                        <textarea name="notes" id="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" id="sticker">
                                    <div class="well well-sm" @if(Config::get('app.locale') == 'ar')style="direction: rtl;" @endif>
                                        <div class="form-group" style="margin-bottom:0;">
                                            <div class="input-group wide-tip">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-3x fa-barcode addIcon"></i>
                                                </div>
                                                <input style="border-radius: 0 !important;padding-left: 10px;padding-right: 10px;"
                                                       type="text" name="add_item" value="" class="form-control input-lg ui-autocomplete-input" id="add_item" placeholder="{{__('main.search_journal')}}" autocomplete="off">
                                            </div>
                                        </div>
                                        <ul class="suggestions" id="products_suggestions" style="display: block"></ul>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card mb-4">
                                        <div class="card-header pb-0">
                                            <h4 class="alert alert-info text-center">{{ __('main.accounts') }}</h4>
                                        </div>
                                        <div class="card-body px-0 pt-0 pb-2">
                                            <div class="table-responsive p-0">
                                                <table id="sTable" style="width:100%" class="table align-items-center mb-0">
                                                    <thead>
                                                    <tr>
                                                        <th class="text-center">{{ __('main.account_code') }}</th>
                                                        <th class="text-center">{{ __('main.account_name') }}</th>
                                                        <th class="text-center">{{ __('main.Debit') }}</th>
                                                        <th class="text-center">{{ __('main.Credit') }}</th>
                                                        <th style="max-width: 30px !important; text-align: center;" class="text-center">
                                                            <i class="fa fa-trash-o" style="opacity:0.5; filter:alpha(opacity=50);"></i>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="tbody"></tbody>
                                                    <tfoot></tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <input type="submit" class="btn btn-primary" id="primary" tabindex="-1"
                                           style="width: 150px; margin: 30px auto;" value="{{__('main.save_btn')}}">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow">
                    <div class="card-header pb-0 text-center">
                        <h5 class="alert alert-secondary text-center">{{ __('main.opening_balances_list') ?? 'سجلات الأرصدة الافتتاحية' }}</h5>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('main.date') }}</th>
                                    <th>{{ __('main.notes') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($journals as $journal)
                                    <tr>
                                        <td>{{ $journal->id }}</td>
                                        <td>{{ $journal->date }}</td>
                                        <td>{{ $journal->notes }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">{{ __('main.no_data') ?? 'لا توجد بيانات' }}</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script src="{{asset('assets/js/jquery.min.js')}}"></script>
<script type="text/javascript">
    var suggestionItems = {};
    var sItems = {};
    var count = 1;

    $(document).ready(function() {
        $('#add_item').on('input',function(){
            searchAccount($('#add_item').val());
        });

        $(document).on('click', '.select_product', function () {
            var row = $(this).closest('li');
            var item_id = row.attr('data-item-id');
            addItemToTable(suggestionItems[item_id]);
            document.getElementById('products_suggestions').innerHTML = '';
            suggestionItems = {};
        });

        $(document).on('click' , '.deleteBtn' , function () {
            var row1 = $(this).closest('tr');
            var item_id = row1.attr('data-item-id');
            delete sItems[item_id];
            loadItems();
        });
    });

    function searchAccount(code){
        if(!code){
            document.getElementById('products_suggestions').innerHTML = '';
            return;
        }
        var url = '{{route('opening_balances.accounts',":id")}}';
        url = url.replace(":id",code);
        $.ajax({
            type:'get',
            url:url,
            dataType: 'json',
            success:function(response){
                document.getElementById('products_suggestions').innerHTML = '';
                if(response){
                    if(response.length == 1){
                        addItemToTable(response[0]);
                    }else if(response.length > 1){
                        showSuggestions(response);
                    }
                }
            }
        });
    }

    function showSuggestions(response) {
        $data = '';
        $.each(response, function (i, item) {
            suggestionItems[item.id] = item;
            $data += '<li class="select_product" data-item-id="' + item.id + '">' + item.name + '--' + item.code + '</li>';
        });
        document.getElementById('products_suggestions').innerHTML = $data;
    }

    function addItemToTable(item) {
        if(!item){ return; }
        if (sItems[item.id]) {
            alert('{{ __('main.account_already_added') ?? 'هذا الحساب مضاف مسبقًا' }}');
            return;
        }
        sItems[item.id] = item;
        loadItems();
    }

    function loadItems(){
        var tbody = document.getElementById('tbody');
        tbody.innerHTML = '';
        var index = 0;
        Object.keys(sItems).forEach(function(key){
            var item = sItems[key];
            var tr = document.createElement('tr');
            tr.setAttribute('data-item-id', item.id);
            tr.innerHTML =
                '<td class="text-center"><input type="hidden" name="account_id['+index+']" value="'+item.id+'"> '+item.code+'</td>'+
                '<td class="text-center">'+item.name+'</td>'+
                '<td class="text-center"><input type="number" step="0.01" class="form-control" name="debit['+index+']" value="0"></td>'+
                '<td class="text-center"><input type="number" step="0.01" class="form-control" name="credit['+index+']" value="0"></td>'+
                '<td class="text-center"><button type="button" class="btn btn-labeled btn-danger deleteBtn">-</button></td>';
            tbody.appendChild(tr);
            index++;
        });
    }
</script>
