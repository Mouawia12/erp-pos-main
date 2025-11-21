@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
        <!-- row opened -->
        <style>
 @media print{
    @page {
        size: A4 landscape;
        margin: 0 !important;
    }

    table {
        page-break-inside: avoid;
    }
    td.text-center {
    font-size: 10px !important;
}
 

th {
    font-size: 10px !important;
}
    thead {
        display: table-header-group;
    } 
}
.c{

    display: flex;
    justify-content: center;
    margin: 0;
    flex-direction: column;
    padding: 6px;
}
</style>
    <div class="row row-sm">
        <div class="col-xl-12"> 
            <div class="card shadow mb-4">
                <div class="card-body px-0 pt-0 pb-2">  
                        <div class="card-header py-3 " style="border:solid 1px gray">
                            <header>
                                    <div class="row" style="direction: ltr;">
                                        <div class="col-3 text-left">   
                                            <br> 
                                            <button type="button" class="btn btn-primary btnPrint" id="btnPrint"><i class="fa fa-print"></i></button>
                                        </div>
                                        <div class="col-6 c" id="card-header">
                                            <h4  class="alert alert-primary text-center">
                                                {{__('main.daily_all_movements')}}
                                            </h4>
                                            @if(isset($branch))
                                            <h5 class="text-center"> [ {{$branch->branch_name}} ] </h5>
                                            @else
                                            <h5 class="text-center"> [ جميع الفروع ] </h5>
                                            @endif
                                            <h5 class="text-center">  {{Config::get('app.locale') == 'ar' ? $period_ar : $period}} </h5>

                                        </div>
                                        <div class="col-3 c">
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
                    <hr>
                    <div class="table-responsive" id="responsive">
                           <table class="display w-100  text-nowrap table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead> 
                            <tr>
                                <th  class="text-center text-white bg-success" colspan="{{(count($karats) * 2 )  + 1}}">{{__('main.enter')}}</th>
                                <th  class="text-center text-white bg-danger" colspan="{{(count($karats) * 2 )  + 1}}">{{__('main.exit')}}</th>
                            </tr>
                            <tr>
                                <th  class="text-center text-white bg-success" colspan="{{count($karats) }}">{{__('main.new_gold')}}</th>
                                <th  class="text-center text-white bg-success" colspan="{{count($karats) }}">{{__('main.old_gold')}}</th>
                                <th  class="text-center text-white bg-success" rowspan="2">{{__('main.money')}}</th>

                                <th  class="text-center text-white bg-danger" colspan="{{count($karats) }}">{{__('main.new_gold')}}</th>
                                <th  class="text-center text-white bg-danger" colspan="{{count($karats) }}">{{__('main.old_gold')}}</th>
                                <th  class="text-center text-white bg-danger" rowspan="2">{{__('main.money')}}</th>
                            </tr>
                            <tr>
                                @foreach($karats as $karat)
                                    <th class="text-center">{{Config::get('app.locale') == 'ar' ?$karat -> name_ar : $karat -> name_en}}</th>
                                @endforeach
                                @foreach($karats as $karat)
                                    <th class="text-center">{{Config::get('app.locale') == 'ar' ?$karat -> name_ar : $karat -> name_en}}</th>
                                @endforeach

                                @foreach($karats as $karat)
                                    <th class="text-center">{{Config::get('app.locale') == 'ar' ?$karat -> name_ar : $karat -> name_en}}</th>
                                @endforeach
                                @foreach($karats as $karat)
                                    <th class="text-center">{{Config::get('app.locale') == 'ar' ?$karat -> name_ar : $karat -> name_en}}</th>
                                @endforeach

                            </tr> 
                            </thead>
                            <tbody>
                                    <tr>
                                        @foreach($karats as $karat)
                                            @if( isset($work[$karat -> id]) )
                                                <td class="text-center" style="color: green;font-size: 14px;font-weight: bold;">{{$work[$karat -> id]['enter_weight']}}</td>
                                            @else
                                                <td class="text-center" style="color: green;font-size: 14px;font-weight: bold;">0.0</td>
                                            @endif

                                        @endforeach


                                        @foreach($karats as $karat)
                                            @if(isset($old[$karat -> id]))
                                                <td class="text-center" style="color: green;font-size: 14px;font-weight: bold;">{{$old[$karat -> id]['enter_weight']}}</td>
                                            @else
                                                <td class="text-center" style="color: green;font-size: 14px;font-weight: bold;"> 0.0</td>
                                            @endif

                                        @endforeach

                                            <?php $enter_money = 0  ?> 
                                            @foreach($enterMoney as $money)
                                                <?php $enter_money += $money -> amount  ?>
                                            @endforeach
                                        <td class="text-center" style="color: green;font-size: 14px;font-weight: bold;">
                                            {{$enter_money}}</td>


                                            @foreach($karats as $karat)
                                                @if( isset($work[$karat -> id]) )
                                                    <td class="text-center" style="color: red;font-size: 14px;font-weight: bold;">{{$work[$karat -> id]['out_weight']}}</td>
                                                @else
                                                    <td class="text-center" style="color: red;font-size: 14px;font-weight: bold;">0.0</td>
                                                @endif

                                            @endforeach


                                            @foreach($karats as $karat)
                                                @if(isset($old[$karat -> id]))
                                                    <td class="text-center" style="color: red;font-size: 14px;font-weight: bold;">{{$old[$karat -> id]['out_weight']}}</td>
                                                @else
                                                    <td class="text-center" style="color: red;font-size: 14px;font-weight: bold;"> 0.0</td>
                                                @endif

                                            @endforeach

                                            <?php $exit_money = 0  ?>
                                            @foreach($exitMoney as $money)
                                                <?php $exit_money += $money -> amount  ?>
                                            @endforeach

                                            <td class="text-center" style="color: red;font-size: 14px;font-weight: bold;">
                                                {{$exit_money}}</td>
                                    </tr>


                            </tbody>

                        </table>
                        <hr>
                         <h4 class="text-center" style="margin: 20px;">{{__('main.movements_net')}}</h4>
                        <hr>
                        <table class="display w-100  text-nowrap table-bordered"  id="dataTable"   width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th  class="text-center text-white bg-info" colspan="{{count($karats) }}">{{__('main.new_gold')}}</th>
                                <th  class="text-center text-white bg-primary" colspan="{{count($karats) }}">{{__('main.old_gold')}}</th>
                                <th  class="text-center text-white bg-secondary" rowspan="2">{{__('main.money')}}</th>
                            </tr>

                            @foreach($karats as $karat)
                                <th class="text-center">{{Config::get('app.locale') == 'ar' ?$karat -> name_ar : $karat -> name_en}}</th>
                            @endforeach

                            @foreach($karats as $karat)
                                <th class="text-center">{{Config::get('app.locale') == 'ar' ?$karat -> name_ar : $karat -> name_en}}</th>
                            @endforeach

                            </thead>

                            <tbody>


                            <tr style="background: antiquewhite;">
                                @foreach($karats as $karat)
                                    @if( isset($work[$karat -> id]) )
                                        <td class="text-center"
                                            @if($work[$karat -> id]['enter_weight'] - $work[$karat -> id]['out_weight'] >= 0) style="color: green; font-weight: bold; font-size: 30px;"
                                            @else style="color: red; font-weight: bold; font-size: 30px;" @endif
                                        >{{$work[$karat -> id]['enter_weight'] - $work[$karat -> id]['out_weight']}}</td>
                                    @else
                                        <td class="text-center"  style="color: green">0.0</td>
                                    @endif
                                @endforeach
                                @foreach($karats as $karat)
                                    @if( isset($old[$karat -> id]) )
                                        <td  class="text-center"
                                            @if($old[$karat -> id]['enter_weight'] - $old[$karat -> id]['out_weight'] >= 0) style="color: green; font-weight: bold; font-size: 30px;"
                                            @else style="color: red; font-weight: bold; font-size: 30px;" @endif
                                        >{{$old[$karat -> id]['enter_weight'] - $old[$karat -> id]['out_weight']}}</td>
                                    @else
                                        <td class="text-center"  style="color: green">0.0</td>
                                    @endif

                                @endforeach

                                <td class="text-center"   @if($enter_money - $exit_money >= 0) style="color: green; font-weight: bold; font-size: 30px;"
                                    @else style="color: red; font-weight: bold; font-size: 30px;" @endif>{{$enter_money - $exit_money}}</td>
                            </tr>

                            </tbody>

                        </table>



                    </div>




                        </div>
                    </div>

                </div>


            </div>
        </div>
        <!-- End of Main Content --> 
    </div>
    <!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->
@endsection
<script src="{{asset('assets/js/jquery.min.js')}}"></script>  

<script type="text/javascript">
    let id = 0;


    $(document).ready(function () {
        $(document).on('click', '#btnPrint', function (event) {
            printPage();
        });

    });


    function printPage() {
        var css = '@page { size: landscape; }',
            head = document.head || document.getElementsByTagName('head')[0],
            style = document.createElement('style');

        style.type = 'text/css';
        style.media = 'print';

        if (style.styleSheet) {
            style.styleSheet.cssText = css;
        } else {
            style.appendChild(document.createTextNode(css));
        }

        head.appendChild(style);
        document.getElementById("main-header").style.display = 'none';
        document.getElementById("main-footer").style.display = 'none'; 
        document.getElementById("back-to-top").style.display = 'none'; 
        
        window.print();
        document.getElementById("main-header").style.display = 'block';
        document.getElementById("main-footer").style.display = 'block'; 
        document.getElementById("back-to-top").style.display = 'block'; 
    }


</script>
 
