@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
  
    @can('اضافة صنف') 
 
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0"  id="head-right" >
                        <div class="col-lg-12 margin-tb">
                            <h4  class="alert alert-primary text-center">
                                [ {{ __('main.add_product')}} ]
                            </h4>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="modal-body itemCaRD" id="paymentBody">
                        <form  method="POST" action="{{ route('storeProduct')}}" enctype="multipart/form-data">
                            @csrf
                
                            <div class="row" style="padding: 20px">  
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.Product_Type') }}<span class="text-danger">*</span> </label>
                                        <select class="form-control @error('type') is-invalid @enderror" id="type" name="type">
                                            @php $defType = $settings->default_product_type ?? '1'; @endphp
                                            <option value="1" @if($defType=='1') selected @endif>{{__('main.General')}}</option>
                                            <option value="2" @if($defType=='2') selected @endif>{{__('main.Collection')}}</option>
                                            <option value="3" @if($defType=='3') selected @endif>{{__('main.Service')}}</option>
                                        </select>
                                        @error('type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div> 
                                <div class="col-md-3">
                                     <div class="form-group">
                                         <label>{{ __('main.code') }}<span class="text-danger">*</span> </label>
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
                                <div class="col-md-6" >
                                    <div class="form-group">
                                        <label>{{ __('main.name') }}<span class="text-danger">*</span>  </label>
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
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.brand') }}<span class="text-danger">*</span> </label>
                                        <select class="js-example-basic-single w-100 @error('brand') is-invalid @enderror" name="brand">
                                            @foreach($brands as $brand)
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
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.categories') }}  <span class="text-danger">*</span> </label>
                                        <select class="js-example-basic-single w-100 @error('category_id') is-invalid @enderror" name="category_id">
                                            @foreach($categories as $cat) 
                                                <option value="{{$cat->id}}">{{$cat->name}}</option> 
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>  
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.units') }}<span class="text-danger">*</span>  </label>
                                        <select class="js-example-basic-single w-100 @error('unit') is-invalid @enderror"     name="unit" id="unit_base">
                                            @foreach($units as $unit) 
                                                <option value="{{$unit->id}}">{{$unit->name}}</option> 
                                            @endforeach
                                        </select>
                                        @error('unit')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div> 
                                <div class="col-md-3">  
                                    <div class="form-group">
                                        <label>{{ __('main.Product_Tax') }}  <span class="text-danger">*</span></label>
                                        <select id="tax_rate" name="tax_rate" class="form-control @error('tax_rate') is-invalid @enderror" >
                                            @foreach($taxRages as $tax)
                                                <option value="{{$tax->id}}">{{$tax->rate}}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden"  id="tax" name="tax"/>
                                        @error('tax_rate')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>  
                            
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.Product_Tax_Type') }}<span class="text-danger">*</span>  </label>
                                        <select class="form-control @error('tax_method') is-invalid @enderror" name="tax_method">
                                            @foreach($taxTypes as $taxType)
                                                <option value="{{$taxType['id']}}">{{$taxType['name']}}</option>
                                            @endforeach
                                        </select>
                                        @error('tax_method')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>   
                                <div class="col-md-3">  
                                    <div class="form-group">
                                        <label>{{ __('main.Cost') }} <span class="text-danger">*</span>  </label>
                                        <input type="number"  id="cost" name="cost"
                                               class="form-control @error('cost') is-invalid @enderror" step="0.01"
                                               placeholder="{{ __('main.Cost') }}"  />
                                        @error('cost')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>  
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.Sale_Price') }} <span class="text-danger">*</span>  </label>
                                        <input type="number"  id="price" name="price"
                                               class="form-control @error('price') is-invalid @enderror" step="0.01"
                                               placeholder="{{ __('main.Sale_Price') }}"  />
                                        @error('price')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div> 
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label>{{ __('main.price_level') }} (1-6)</label>
                                        <div class="row">
                                            @for($i=1;$i<=6;$i++)
                                                <div class="col-md-2 mb-2">
                                                    <input type="number" step="0.01" class="form-control" name="price_level_{{$i}}" placeholder="Level {{$i}}">
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.Slug') }}  <span class="text-danger">*</span> </label>
                                        <input type="text"  id="slug" name="slug"
                                               class="form-control @error('slug') is-invalid @enderror"
                                               placeholder="{{ __('main.Slug') }}"  />
                                        @error('slug')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>    

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.Lista') }}  </label>
                                        <input type="number"  id="lista" name="lista"
                                               class="form-control" step="0.01"
                                               placeholder="{{ __('main.Lista') }}"  />
                                    </div>
                                </div> 

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.Max_Order') }}</label>
                                        <input type="number"  id="max_order" name="max_order"
                                               class="form-control" step="0.01"
                                               placeholder="{{ __('main.Max_Order') }}"  />
                                    </div>
                                </div>  
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.Track_Quantity') }}</label>
                                        <select id="track_quantity" name="track_quantity"
                                               class="form-control" >
                                            <option value="1">{{__('main.status1')}}</option>
                                            <option value="0">{{__('main.status2')}}</option>
                                        </select>
                                    </div>
                                </div>  
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{ __('main.Alert_Quantity') }}</label>
                                        <input type="number"  id="alert_quantity" name="alert_quantity"
                                               class="form-control" step="0.01"
                                               placeholder="{{ __('main.Alert_Quantity') }}"  />
                                    </div>
                                </div> 
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{ __('main.img') }}</label>
                                        <div class="row"> 
                                            <div class="col-6">
                                                <div class="custom-file"> 
                                                    <input accept="image/*" type="file"
                                                        oninput="pic.src=window.URL.createObjectURL(this.files[0])" id="img"
                                                        name="img" class="form-control"> 
                                                    <label class="custom-file-label" for="img"
                                                        id="path">{{__('main.img_choose')}} 
                                                    </label>
                                                </div>
                                                <br> 
                                                <span style="font-size: 9pt ; color:gray;">{{ __('main.img_hint') }}</span>
                                            </div>
                                            <div class="col-6 text-right">  
                                                <img id="pic" src=""
                                                    style="width: 100px; height:100px;"/>
                                            </div>
                                        </div>
                                        @error('printer')
                                           <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>      
                            </div> 
                
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-labeled btn-primary"  >
                                        {{__('main.save_btn')}}
                                    </button>
                                </div>
                            </div>
                        </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label>الوحدات الإضافية</label>
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="addUnitRowBtn">+ إضافة وحدة</button>
                                        </div>
                                        <table class="table table-bordered mt-2" id="unitRowsTable">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('main.units') }}</th>
                                                    <th>{{ __('main.price') }}</th>
                                                    <th>معامل التحويل</th>
                                                    <th>{{ __('main.barcode') ?? 'باركود' }}</th>
                                                    <th>{{ __('main.actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div> 
    </div>
@endcan 
@endsection 
@section('js')
<script type="text/javascript">

    $(document).ready(function () {
        
        // وحدات متعددة
        const unitOptionsHtml = `@foreach($units as $unit)<option value="{{$unit->id}}">{{$unit->name}}</option>@endforeach`;
        let unitRowIndex = 0;
        function addUnitRow(unitId, price, factor, barcode, canDelete=true){
            const row = `<tr data-index="${unitRowIndex}">
                <td><select name="product_units[${unitRowIndex}][unit]" class="form-control">${unitOptionsHtml}</select></td>
                <td><input type="number" step="0.01" name="product_units[${unitRowIndex}][price]" class="form-control" value="${price ?? ''}"></td>
                <td><input type="number" step="0.01" name="product_units[${unitRowIndex}][conversion_factor]" class="form-control" value="${factor ?? 1}"></td>
                <td><input type="text" name="product_units[${unitRowIndex}][barcode]" class="form-control" value="${barcode ?? ''}"></td>
                <td class="d-flex gap-1">
                    <button type="button" class="btn btn-sm btn-outline-secondary generateBarcode">{{ __('main.generate') ?? 'توليد' }}</button>
                    ${canDelete ? '<button type="button" class="btn btn-sm btn-danger removeUnitRow">-</button>' : ''}
                </td>
            </tr>`;
            $('#unitRowsTable tbody').append(row);
            const $lastRow = $('#unitRowsTable tbody tr').last();
            if(unitId){ $lastRow.find('select').val(unitId); }
            unitRowIndex++;
        }
        addUnitRow($('#unit_base').val(), $('#price').val(), 1, '', false);
        $('#addUnitRowBtn').on('click', function(){
            addUnitRow('', '', 1, '', true);
        });
        $(document).on('click','.removeUnitRow', function(){
            $(this).closest('tr').remove();
        });
        $(document).on('click','.generateBarcode', function(){
            const code = '9' + Math.floor(100000000000 + Math.random() * 900000000000).toString().slice(0,12);
            $(this).closest('tr').find('input[name*="[barcode]"]').val(code);
        });
        $('#price').on('change', function(){
            const val = $(this).val();
            const firstRow = $('#unitRowsTable tbody tr').first();
            if(firstRow.length){
                firstRow.find('input[name*="[price]"]').val(val);
            }
        });

        document.title = "{{ __('main.add_product')}}";
        
        $('#tax_rate').change(function (){
            const tax = $('#tax_rate  option:selected').text();
            $("#tax").val(tax);
        });
        if($('#tax_rate').prop('selectedIndex') == 0){
            const tax = $('#tax_rate  option:selected').text();
            $("#tax").val(tax);
        }
    }); 
</script>
@endsection 
