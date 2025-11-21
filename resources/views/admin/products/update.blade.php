@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
  
    @can('تعديل صنف') 
 
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0"  id="head-right" >
                        <div class="col-lg-12 margin-tb">
                            <h4  class="alert alert-primary text-center">
                            [ {{ __('main.products_list'). ' / '. __('main.update_product') }} ]
                            </h4>
                        </div>
                        <div class="clearfix"></div>
                    </div>
 
                    <div class="modal-body" id="paymentBody">
                        <form method="POST" action="{{ route('updateProduct' , $product -> id) }}"
                            enctype="multipart/form-data">
                            @csrf  
                            <div class="row" style="padding: 20px">
                                <div class="col-md-4 col-sm-4">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{ __('main.Product_Type') }} <span class="text-danger">*</span> </label>
                                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type">
                                                    <option @if($product -> type == 1) selected @endif   value="1">{{__('main.General')}}</option>
                                                    <option @if($product -> type == 2) selected @endif   value="2">{{__('main.Collection')}}</option>
                                                    <option @if($product -> type == 3) selected @endif   value="3">{{__('main.Service')}}</option>
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
                                        <div class="col-12 " >
                                            <div class="form-group">
                                                <label>{{ __('main.name') }} <span class="text-danger">*</span>  </label>
                                                <input type="text"  id="name" name="name"
                                                       class="form-control @error('name') is-invalid @enderror"
                                                       placeholder="{{ __('main.name') }}"  value="{{$product -> name}}"/>
                                                @error('name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{ __('main.code') }} <span class="text-danger">*</span> </label>
                                                <input type="text"  id="code" name="code"
                                                       class="form-control @error('code') is-invalid @enderror"
                                                       placeholder="{{ __('main.code') }}"  value="{{$product -> code}}" />
                                                @error('code')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{ __('main.Slug') }}   <span class="text-danger">*</span> </label>
                                                <input type="text"  id="slug" name="slug"
                                                       class="form-control @error('slug') is-invalid @enderror"
                                                       placeholder="{{ __('main.Slug') }}"  value="{{$product -> slug}}"/>
                                                @error('slug')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{ __('main.brand') }} <span class="text-danger">*</span> </label>
                                                <select class="js-example-basic-single w-100 @error('brand') is-invalid @enderror" name="brand">
                                                    @foreach($brands as $brand)
                                                        <option  @if($product -> brand == $brand->id) selected @endif  value="{{$brand->id}}">{{$brand->name}}</option>
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
                                </div> 
                                <div class="col-md-4 col-sm-4">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{ __('main.Product_Tax') }}   <span class="text-danger">*</span></label>
                                                <select id="tax_rate" name="tax_rate" class="form-control @error('tax_rate') is-invalid @enderror" >
                                                    @foreach($taxRages as $tax)
                                                        <option  @if($product -> tax_rate == $tax->id) selected @endif  value="{{$tax->id}}">{{$tax->rate}}</option>
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
                                    </div>
                
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{ __('main.Product_Tax_Type') }} <span class="text-danger">*</span>  </label>
                                                <select class="form-control @error('tax_method') is-invalid @enderror" name="tax_method">
                                                    @foreach($taxTypes as $brand)
                                                        <option @if($product -> tax_method == $brand['id']) selected @endif   value="{{$brand['id']}}">{{$brand['name']}}</option>
                                                    @endforeach
                                                </select>
                                                @error('tax_method')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{ __('main.categories') }}   <span class="text-danger">*</span> </label>
                                                <select class="js-example-basic-single w-100 @error('category_id') is-invalid @enderror" name="category_id">
                                                    @foreach($categories as $cat)
                                                        @if($cat -> isGold == 0)
                                                            <option  @if($product -> category_id == $cat->id) selected @endif  value="{{$cat->id}}">{{$cat->name}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                @error('category_id')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                                    
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{ __('main.Track_Quantity') }}  </label>
                                                <select id="track_quantity" name="track_quantity"
                                                        class="form-control" >
                                                    <option @if($product -> track_quantity == 1)  selected @endif value="1">{{__('main.status1')}}</option>
                                                    <option @if($product -> track_quantity == 0)  selected @endif  value="0">{{__('main.status2')}}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div> 

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{ __('main.Alert_Quantity') }}  </label>
                                                <input type="number"  id="alert_quantity" name="alert_quantity"
                                                       class="form-control" step="0.01"
                                                       placeholder="{{ __('main.Alert_Quantity') }}"  value="{{$product ->alert_quantity }}"/>
                                            </div>
                                        </div>
                                    </div>
                
                                </div>

                                <div class="col-md-4 col-sm-4">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{ __('main.Cost') }}  <span class="text-danger">*</span>  </label>
                                                <input type="number"  id="cost" name="cost"
                                                       class="form-control @error('cost') is-invalid @enderror" step="0.01"
                                                       placeholder="{{ __('main.Cost') }}"  value="{{$product -> cost}}"/>
                                                @error('cost')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{ __('main.Sale_Price') }}  <span class="text-danger">*</span>  </label>
                                                <input type="number"  id="price" name="price"
                                                       class="form-control @error('price') is-invalid @enderror" step="0.01"
                                                       placeholder="{{ __('main.Sale_Price') }}"  value="{{$product -> price}}"/>
                                                @error('price')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>  
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{ __('main.units') }} <span class="text-danger">*</span>  </label>
                                                <select class="js-example-basic-single w-100 @error('unit') is-invalid @enderror"     name="unit">
                                                    @foreach($units as $unit) 
                                                        <option  @if($product -> unit == $unit->id) selected @endif value="{{$unit->id}}">{{$unit->name}}</option>
                                                    @endforeach
                                                </select>
                                                @error('unit')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{ __('main.Max_Order') }}  </label>
                                                <input type="number"  id="max_order" name="max_order"
                                                       class="form-control" step="0.01"
                                                       placeholder="{{ __('main.Max_Order') }}"  value="{{$product -> max_order}}" />
                                            </div>
                                        </div>
                                    </div> 
                
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>{{ __('main.Lista') }}  </label>
                                                <input type="number"  id="lista" name="lista"
                                                       class="form-control" step="0.01"
                                                       placeholder="{{ __('main.Lista') }}"  value="{{$product ->lista }}"/>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <input type="hidden"  name="featured" value="{{$product ->featured }}">
                                                <input type="hidden"  name="city_tax" value="{{$product ->city_tax }}">
                                                <input type="hidden"  name="quantity" value="{{$product ->quantity }}">
                                                <label>{{ __('main.status') }}  </label>
                                                <select id="status" name="status"
                                                        class="form-control" >
                                                    <option @if($product -> status == 1) selected @endif value="1">{{__('main.status1')}}</option>
                                                    <option @if($product -> status == 0) selected @endif value="0">{{__('main.status2')}}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>  
                                </div> 
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>{{ __('main.img') }}</label>
                                        <div class="row"> 
                                            <div class="col-6">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="img" name="img"
                                                           accept="image/png, image/jpeg"  value="{{$product->img}}" >
                                                    <label class="custom-file-label" for="img"
                                                           id="path">{{__('main.img_choose')}} 
                                                    </label>
                                                </div>
                                                <br> 
                                                <span style="font-size: 9pt ; color:gray;">{{ __('main.img_hint') }}</span>
                                            </div>
                                            <div class="col-6 text-right">
                                                @if($product->img<>'')
                                                <img src="{{asset('uploads/items/images/'.$product->img)}}" id="profile-img-tag" width="150px"
                                                     height="150px" class="profile-img"/>
                                                @endif
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
    </div>
    @endcan 
@endsection 
@section('js')
<script type="text/javascript">
 
    $(document).ready(function () {

        document.title = "{{ __('main.update_product')}}";

        $('#tax_rate').change(function (){
            const tax = $('#tax_rate  option:selected').text();
            $("#tax").val(tax);
        });
        
        if($('#tax_rate').prop('selectedIndex') >= 0){
            const tax = $('#tax_rate  option:selected').text();
            $("#tax").val(tax);
        }

    }); 

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#profile-img-tag').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
            document.getElementById('path').innerHTML = input.files[0].name;
        }
    }

    $("#img").change(function () {
        readURL(this);
    });
</script>
@endsection 
 