<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Gold MS</title>

    <!-- Custom fonts for this template-->
    <link href="{{asset('assets/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="{{asset('assets/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="{{asset('assets/css/sb-admin-2.css')}}" rel="stylesheet">

</head>

<body id="page-top" @if(Config::get('app.locale') == 'ar') style="direction: rtl" @endif>

<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    @include('layouts.side' , ['slag' => 16 , 'subSlag' => 162])
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            <!-- Topbar -->
            @include('layouts.header')
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            <div class="container-fluid">
                @include('flash-message')
                <div class="d-sm-flex align-items-center justify-content-between mb-4" style="padding: 8px">
                    <h1 class="h3 mb-0 text-primary-800">{{__('main.account_settings')}} / {{__('main.account_settings_create')}}</h1>
                </div>

                <div class="card-body px-0 pt-0 pb-2">
                    <form   method="POST" action="{{ route('update_account_settings' , $setting -> id) }}">
                        @csrf

                        <div class="row" style="padding: 20px">
                            <div class="col-md-12 col-sm-12 row">

                                <div class="row col-6">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('main.warehouse') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                            <select class="form-control @error('brand') is-invalid @enderror" id="parent_id" name="warehouse_id">

                                                @foreach($warehouses as $brand)
                                                    <option value="{{$brand->id}}" @if($brand -> id == $setting -> warehouse_id) selected @endif>{{$brand->name}}</option>
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



                                <div class="row col-6">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('main.safe_account') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                            <select class="form-control @error('brand') is-invalid @enderror" id="parent_id" name="safe_account">

                                                @foreach($accounts as $brand)
                                                    <option value="{{$brand->id}}" @if($brand -> id == $setting -> safe_account) selected @endif>{{$brand->name}}</option>
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

                                <div class="row col-6">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('main.sales_account') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                            <select class="form-control @error('brand') is-invalid @enderror" id="parent_id" name="sales_account">

                                                @foreach($accounts as $brand)
                                                    <option value="{{$brand->id}}" @if($brand -> id == $setting -> sales_account) selected @endif>{{$brand->name}}</option>
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


                                <div class="row col-6">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('main.purchase_account') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                            <select class="form-control @error('brand') is-invalid @enderror" id="parent_id" name="purchase_account">

                                                @foreach($accounts as $brand)
                                                    <option value="{{$brand->id}}"  @if($brand -> id == $setting -> purchase_account) selected @endif>{{$brand->name}}</option>
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


                                <div class="row col-6">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('main.return_sales_account') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                            <select class="form-control @error('brand') is-invalid @enderror" id="parent_id" name="return_sales_account">

                                                @foreach($accounts as $brand)
                                                    <option value="{{$brand->id}}" @if($brand -> id == $setting -> return_sales_account) selected @endif>{{$brand->name}}</option>
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


                                <div class="row col-6">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('main.return_purchase_account') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                            <select class="form-control @error('brand') is-invalid @enderror" id="parent_id" name="return_purchase_account">

                                                @foreach($accounts as $brand)
                                                    <option value="{{$brand->id}}" @if($brand -> id == $setting -> return_purchase_account) selected @endif>{{$brand->name}}</option>
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


                                <div class="row col-6">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('main.stock_account') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                            <select class="form-control @error('brand') is-invalid @enderror" id="parent_id" name="">

                                                @foreach($accounts as $brand)
                                                    <option value="{{$brand->id}}" @if($brand -> id == $setting -> stock_account) selected @endif>{{$brand->name}}</option>
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


                                <div class="row col-6">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('main.sales_discount_account') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                            <select class="form-control @error('brand') is-invalid @enderror" id="parent_id" name="sales_discount_account">

                                                @foreach($accounts as $brand)
                                                    <option value="{{$brand->id}}" @if($brand -> id == $setting -> sales_discount_account) selected @endif>{{$brand->name}}</option>
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


                                <div class="row col-6">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('main.sales_tax_account') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                            <select class="form-control @error('brand') is-invalid @enderror" id="parent_id" name="sales_tax_account">

                                                @foreach($accounts as $brand)
                                                    <option value="{{$brand->id}}" @if($brand -> id == $setting -> sales_tax_account) selected @endif>{{$brand->name}}</option>
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


                                <div class="row col-6">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('main.purchase_discount_account') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                            <select class="form-control @error('brand') is-invalid @enderror" id="parent_id" name="purchase_discount_account">

                                                @foreach($accounts as $brand)
                                                    <option value="{{$brand->id}}" @if($brand -> id == $setting -> purchase_discount_account) selected @endif>{{$brand->name}}</option>
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


                                <div class="row col-6">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('main.purchase_tax_account') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                            <select class="form-control @error('brand') is-invalid @enderror" id="parent_id" name="purchase_tax_account">

                                                @foreach($accounts as $brand)
                                                    <option value="{{$brand->id}}" @if($brand -> id == $setting -> purchase_tax_account) selected @endif>{{$brand->name}}</option>
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


                                <div class="row col-6">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('main.cost_account') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                            <select class="form-control @error('brand') is-invalid @enderror" id="parent_id" name="cost_account">

                                                @foreach($accounts as $brand)
                                                    <option value="{{$brand->id}}" @if($brand -> id == $setting -> cost_account) selected @endif>{{$brand->name}}</option>
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


                                <div class="row col-6">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('main.profit_account') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                            <select class="form-control @error('brand') is-invalid @enderror" id="parent_id" name="profit_account">

                                                @foreach($accounts as $brand)
                                                    <option value="{{$brand->id}}" @if($brand -> id == $setting -> profit_account) selected @endif>{{$brand->name}}</option>
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


                                <div class="row col-6">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>{{ __('main.reverse_profit_account') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                            <select class="form-control @error('brand') is-invalid @enderror" id="parent_id" name="reverse_profit_account">

                                                @foreach($accounts as $brand)
                                                    <option value="{{$brand->id}}" @if($brand -> id == $setting -> reverse_profit_account) selected @endif>{{$brand->name}}</option>
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
            <!-- /.container-fluid -->
            <input id="local" value="{{Config::get('app.locale')}}" hidden>
        </div>
        <!-- End of Main Content -->

        <!-- Footer -->
        @include('layouts.footer')
        <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>


<!-- Bootstrap core JavaScript-->
<script src="{{asset('assets/vendor/jquery/jquery.min.js')}}"></script>
<script src="{{asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

<!-- Core plugin JavaScript-->
<script src="{{asset('assets/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

<!-- Custom scripts for all pages-->
<script src="{{asset('assets/js/sb-admin-2.min.js')}}"></script>


<script src="{{asset('assets/vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>

<!-- Page level custom scripts -->
<script src="{{asset('assets/js/demo/datatables-demo.js')}}"></script>

<script src="{{asset('assets/js/soft-ui-dashboard.min.js?v=1.0.7')}}"></script>

<!-- Page level custom scripts -->
<script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = {
            damping: '0.5'
        }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }


    $('#account_type').change(function () {
        var t = $(this).val();
        $('#parent_id').val(0).trigger('change');
        $('#account_level').val(1);
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

</script>
<!-- Github buttons -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
<!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->

</body>

</html>
