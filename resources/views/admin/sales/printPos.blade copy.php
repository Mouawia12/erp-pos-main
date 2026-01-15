<!DOCTYPE html>
<html>
<head>
    <title>
    @if(empty($vendor->vat_no))
        فاتورة ضريبية مبسطة {{$data->id}}
    @else
        فاتورة ضريبية  {{$data->id}}
    @endif  
    </title>
    <meta charset="utf-8"/>
    <style type="text/css" media="screen">
        @font-face {
            font-family: 'Almarai';
            src: url("{{asset('fonts/Almarai.ttf')}}");
        } 
        * {
            color: #000 !important;
        }

        body, html {
            color: #000;
            font-family: 'Almarai' !important;
            font-size: 13px !important;
            font-weight: bold;
            margin: 0;
            padding: 10px;
            page-break-before: avoid;
            page-break-after: avoid;
            page-break-inside: avoid;
        }

        .no-print {
            position: fixed;
            bottom: 0;
            color: #fff !important;
            left: 30px;
            height: 40px !important;
            border-radius: 0;
            padding-top: 10px;
            z-index: 9999;
        }

        table thead tr, table tbody tr {
            border-bottom: 1px solid #aaa;
        }

        table {
            text-align: center;
            width: 100% !important;
            margin-top: 10px !important;
        }
    </style>
    <style type="text/css" media="print">
        .above-table {
            width: 100% !important;
        }

        table {
            text-align: center;
            width: 100% !important;
            margin-top: 10px !important;
        }

        table thead tr, table tbody tr {
            border-bottom: 1px solid #aaa;
        }

        * {
            color: #000 !important;
        }

        body, html {
            color: #000;
            padding: 0px;
            margin: 0;
            font-family: 'Almarai' !important;
            font-size: 11px !important;
            font-weight: bold !important;
            page-break-before: avoid;
            page-break-after: avoid;
            page-break-inside: avoid;
        }

        .pos_details {
            width: 100% !important;
            page-break-before: avoid;
            page-break-after: avoid;
            page-break-inside: avoid;
        }

        .no-print {
            display: none;
        }
    </style>
</head>
<body dir="rtl" style="background: #fff;
            page-break-before: avoid;
            page-break-after: avoid;
            page-break-inside: avoid;" class="text-center">
<header style="width: 95% ; display: block; margin: auto ; height: 3cm;" >

</header>            
    <div class="pos_details  justify-content-center text-center"> 
        <div class="text-center">
            <img class="text-center" src="{{asset('uploads/profiles/'.$company->logo)}}"
                 style="width:80px!important;height:80px!important;" />
            <h3 class="text-center" style="font-weight: bold;">
                {{$company->name_ar}}
            </h3>
            <h6 class="text-center" style="font-weight: bold;"> 
                {{$company->faild_ar}}
            </h6>
            <h6 class="text-center mt-1" style="font-weight: bold;">
                {{$data->branch_address}}
            </h6>
            <h6 class="text-center mt-1" style="font-weight: bold;">
                {{$data->branch_name}}
            </h6>
            <h6 class="text-center mt-1" style="font-weight: bold;">
                هاتف :
                {{$data->branch_phone}}
            </h6> 
            <h2 class="text-center mt-1" style="font-weight: bold;">
                فاتورة ضريبية مبسطة
            </h2>
            <div class="clearfix"></div>
            <div class="visible-print text-center mt-1">
                <?php
                use Salla\ZATCA\GenerateQrCode;
                use Salla\ZATCA\Tags\InvoiceDate;
                use Salla\ZATCA\Tags\InvoiceTaxAmount;
                use Salla\ZATCA\Tags\InvoiceTotalAmount;
                use Salla\ZATCA\Tags\Seller;
                use Salla\ZATCA\Tags\TaxNumber;
                $displayQRCodeAsBase64 = GenerateQrCode::fromArray([
                    new Seller($company->name_ar), // seller name
                    new TaxNumber($company->taxNumber), // seller tax number
                    new InvoiceDate($data->created_at), // invoice date as Zulu ISO8601 @see https://en.wikipedia.org/wiki/ISO_8601
                    new InvoiceTotalAmount($data->net), // invoice total amount
                    new InvoiceTaxAmount($data -> tax) // invoice tax amount
                    // TODO :: Support others tags
                ])->render();
                ?>
                <img src="{{$displayQRCodeAsBase64}}" style="width: 150px; height: 150px;" alt="QR Code"/>
            </div>
            <h6 class="text-center mt-1" style="font-weight: bold;">
                الرقم الضريبى :
                 {{$company->taxNumber}}
            </h6>
            <h6 class="text-center mt-1" style="font-weight: bold;">
                س . ت :
                {{$company->registrationNumber}}
            </h6> 
            <h6 class="text-center mt-1" style="font-weight: bold;">
                رقم الفاتورة :
                <span dir="ltr">
                {{$data->invoice_no}}
                </span>
            </h6>
            <h6 class="text-center mt-1" style="font-weight: bold;">
                التاريخ :
                <span dir="ltr"> 
                    {{\Carbon\Carbon::parse($data->created_at) }} 
                </span>
            </h6>
            <h6 class="text-center mt-1" style="font-weight: bold;">
                 {{__('main.client')}}  :
                <span dir="ltr">
                 {{$vendor->name}}  
                </span>
            </h6>
        </div>
        <div class="above-table w-25 text-center mt-3  justify-content-center" style="margin: 10px auto!important;">
            <table class="table-bordered text-center" style="width: 100% ; direction: rtl">
                <thead>
                <tr> 
                    <th class="text-center">{{__('main.item')}}<br>Item</th> 
                    <th class="text-center">{{__('main.quantity')}}<br>Qty</th>  
                    <th class="text-center">{{__('main.Amount')}}<br>Amount</th>
                    <th class="text-center">{{__('main.tax')}}<br>Vat</th>
                    <th class="text-center">{{__('main.total_without_tax')}}<br>Amount With Vat</th>

                </tr>
                </thead>
                <tbody>
                    @php
                       $qty = 0;
                    @endphp
                    @foreach($details as $detail)
                        <tr> 
                            <td>{{$detail ->name }} -- {{$detail ->code }}</td> 
                            <td>{{$detail ->quantity }}</td>  
                            <td>{{$detail ->total }}</td>
                            <td>{{$detail ->tax }}</td>
                            <td>{{$detail ->total + $detail ->tax }}</td>
                        </tr>
                    @php
                       $qty = $qty + $detail ->quantity;
                    @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th class="alert alert text-center">
                            {{__('main.sum')}}
                        </th>
                        <th class="text-center">  
                           {{$qty}}
                        </th> 
                        <th class="text-center">  
                            {{$data->total}}
                        </th>  
                        <th class="text-center">  
                            {{$data->tax}} 
                        </th>  
                        <th class="text-center">  
                            {{$data->total + $data->tax}}
                        </th>  
                    </tr>
                    <tr>
                        <th colspan="4" class="alert alert text-center">
                            {{__('main.invoice.total')}}
                        </th>
                        <th colspan="1" class="alert alert text-center">
                            {{$data->net}}
                        </th>
                    </tr>
                </tfoot>
            </table>   
            <hr style="border-top: 1px solid #000;">
            <div class="row" style="direction:rtl">
                <div class="col-6 text-center">
                    <span> اسم البائع</span> <br>
                    <span>{{auth() -> user() -> name}}</span>
                </div>
                <div class="col-6 text-center">
                    <span>  مدير الفرع</span> <br>
                    <span>........</span>
                </div>
            </div>
        </div> 
    </div> 

<a href="@if($data->pos){{route('pos')}}@else{{route('sales')}}@endif" class="no-print btn btn-md btn-danger"
   style="left:20px!important;">
    العودة الى النظام
</a> 

<button onclick="window.print();" class="no-print btn btn-md btn-success"
    style="left:150px!important;">
    <i class="fa fa-print text-white"></i> اضغط للطباعة 
</button>

<script src="{{asset('assets/js/jquery.min.js')}}"></script>
<script>
    $(document).ready(function () {
        window.print();
    });
</script>

</body>
</html>
