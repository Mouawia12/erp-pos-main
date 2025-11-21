
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}">
    <script src="{{asset('assets/js/print.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="{{asset('assets/css/printA4Landscape.css')}}" rel="stylesheet">
    <style>
        .c{

            display: flex;
            justify-content: center;
            margin: 0;
            flex-direction: column;
            padding: 6px;
        }
    </style>
</head>
<body>

        <header>
            <div class="container">
                <div class="row">
                    <div class="col-4 ">
                        <span style="text-align: left;">{{$company ? $company -> name_en : ''}}

                    <br> C.R :   {{$company ? $company -> registrationNumber : ''}}
                   <br>  Vat No :   {{$company ? $company -> taxNumber : ''}}
                  <br>  Tel :   {{ $company ? $company -> phone : ''}}

               </span>
                    </div>
                    <div class="col-4 ">
                        <label style="text-align: center; font-weight: bold"> تقرير قائمة الأصناف</label>
                    </div>
                    <div class="col-4 ">
                   <span style="text-align: right;">{{$company ? $company -> name_en : ''}}

                    <br>  س.ت : {{$company ? $company -> taxNumber : ''}}
                   <br>  ر.ض :  {{$company ? $company -> registrationNumber : ''}}
                  <br>  تليفون :   {{$company ? $company -> phone : ''}}
                   </span>
                    </div>
                </div>
            </div>

        </header>
        <article>
            <div class="container">
                <div class="row" style="border: solid 1px black">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">
                                #
                            </th>
                            <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7 ps-2">{{__('main.code')}}</th>
                            <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">{{__('main.name_ar')}}</th>
                            <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.category')}} </th>
                            <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.karat')}} </th>
                            <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.weight')}} </th>
                            <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.gram_made_value')}} </th>
                            <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.made_Value_t')}} </th>

                            <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.no_metal')}} </th>
                            <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.state')}} </th>

                        </tr>
                        </thead>
                        <tbody>
                        <?php $sum_weight = 0 ?>
                        <?php $sum_made = 0 ?>
                        @foreach($data as $item)
                            <tr>
                                <td class="text-center">{{$loop -> index + 1}}</td>
                                <td class="text-center">{{$item -> code}}</td>
                                <td class="text-center">{{$item -> name_ar}}</td>
                                <td class="text-center">{{Config::get('app.locale') == 'ar' ? $item -> category -> name_ar : $item -> category -> name_en }}</td>
                                <td class="text-center">{{ $item -> karat ? (Config::get('app.locale') == 'ar' ? $item -> karat -> name_ar : $item -> karat -> name_en) : '' }}</td>
                                <td class="text-center">{{$item -> weight}}</td>
                                <td class="text-center">{{$item -> made_Value}}</td>
                                <td class="text-center">{{$item -> weight * $item -> made_Value}}</td>
                                <td class="text-center">{{$item -> no_metal}}</td>
                                <td class="text-center">{{$item -> state == 1  ? __('main.state1')  : __('main.state2')}}</td>

                            </tr>
                            <?php $sum_weight +=  $item -> weight?>
                            <?php $sum_made +=  ($item -> made_Value * $item -> weight)  ?>
                        @endforeach
                        <tr>
                            <td colspan="6" class="text-center">الإجمالي</td>
                            <td class="text-center">{{$sum_weight}}</td>
                            <td class="text-center"></td>
                            <td class="text-center">{{$sum_made}}</td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                        </tr>
                        </tbody>

                    </table>
                </div>


                     src="https://api.qrserver.com/v1/create-qr-code/?data=http://127.0.0.1:8000/ar/workQrcode/{{$bill -> id}}&amp;size=100x100"
                     alt=""
                     title="HELLO"
                     width="100"
                     height="100"
                     style="width: 80px; height: auto; display: block; margin: 5px auto;">
            </div>

        </article>

        <script>


            $(document).ready(function () {
                printPage();

            });

            function printPage(){


                window.print();
            }

        </script>



</body>
</html>
