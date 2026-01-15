<!DOCTYPE html>
<html>
<head>
    <title>
         مستند قبض نقدية {{$bill -> docNumber}}
    </title>
    <meta charset="utf-8"/>
	<link href="{{asset('css/all.min.css')}}" rel="stylesheet" />
    <style type="text/css" media="screen">
  

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
<div class="pos_details  justify-content-center text-center"> 
    <div class="above-table w-50 text-center mt-3  justify-content-center" style="margin: 10px auto!important;">
            <!-- Main content -->
            <section class="invoice"> 
              <!-- info row -->
              <div class="row invoice-info">
                <div class="col-4 invoice-col  text-right">  
                  <address>
                    
                    <b>ر . ض :</b>  {{$company ->taxNumber}}<br>
                    <b> س . ت :</b>  {{$company ->registrationNumber}}<br>
                    <b> الهاتف:</b> {{$company -> phone}}<br> 
                  </address>
                </div>
                <!-- /.col -->
                <div class="col-4 invoice-col">  
                  <address> 
                  <h5><strong>{{$company -> name_ar }}</strong></h5>
     
                  
                  </address>
                </div>
                <!-- /.col -->
                <div class="col-4 invoice-col text-left">
                  <b>المستند رقم : {{$bill -> docNumber}}</b><br> 
                  <b>تاريخ :</b> {{\Carbon\Carbon::parse($bill -> date) -> format('d- m -Y') }}<br>
                  <b> قيمة المستند :<b>{{$bill -> amount }}<br> 
                  <b> نوع الدفع : </b> {{ $bill -> payment_type == 0 ? 'كاش' : 'شبكة' }}  
                

                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
              <div class="col-12">
                  <hr>
                  <h2 class="page-header">
                     مستند قبض نقدية
                  </h2>
                  <hr>
                </div>
          </section>          
 

            <div class="row" style="width: 100%; margin: 10px auto !important; direction:rtl;"> 
                <ol class="list-group" style="width: 100%; text-align:right;">
                    <li class="list-group-item">
                      <div class="ms-2 me-auto">
                        <div class="fw-bold">إستلمنا من المكرم :   /  {{ $account -> name }}</div> 
                      </div> 
                    </li>
                    <li class="list-group-item">
                      <div class="ms-2 me-auto">
                        <div class="fw-bold">مبلغ وقدره : /  {{ $bill -> amount  }} ---  {{ $valAr }} </div> 
                      </div> 
                    </li>
                @if($bill -> payment_type == 0)
                    <li class="list-group-item">
                      <div class="ms-2 me-auto">
                        <div class="fw-bold"> نقدا</div> 
                      </div> 
                    </li>
                    <li class="list-group-item">
                      <div class="ms-2 me-auto">
                        <div class="fw-bold"> بتاريخ :  {{ $bill -> date }}</div> 
                      </div> 
                    </li>
                @else  
                    <li class="list-group-item">
                      <div class="ms-2 me-auto">
                        <div class="fw-bold">  شيك رقم  :  ........</div> 
                      </div> 
                    </li>
                    <li class="list-group-item">
                      <div class="ms-2 me-auto">
                        <div class="fw-bold">  علي بنك  :  ........</div> 
                      </div> 
                    </li>
                    <li class="list-group-item">
                      <div class="ms-2 me-auto">
                        <div class="fw-bold"> بتاريخ :  {{ $bill -> date }}</div> 
                      </div> 
                    </li> 
                    @endif 
                    <li class="list-group-item">
                      <div class="ms-2 me-auto">
                        <div class="fw-bold"> و ذلك مقابل  :</div> 
                          {{$bill -> notes}}
                      </div> 
                    </li>  
                </ol >   
            </div >   
        <div class="row" style="direction:rtl">
           
            <div class="col-4 text-center">
                <span>المستلم</span> <br>
                <span>........</span>
            </div>

            <div class="col-4 text-center">
                <span>المحاسب</span> <br>
                <span>........</span>
            </div>
            <div class="col-4 text-center">
                <span>  مدير الفرع</span> <br>
                <span>........</span>
            </div>
        </div> 
    </div>    

</div>
<button onclick="window.print();" class="no-print btn btn-md btn-success">اضغط للطباعة</button>
<a href="{{route('catches')}}" class="no-print btn btn-md btn-danger"
style="left:150px!important;">
العودة الى النظام
</a>

<script src="{{asset('assets/js/jquery.min.js')}}"></script>
<script>
$(document).ready(function () {
window.print();
});
</script>
</body>
</html> 
 
 
 