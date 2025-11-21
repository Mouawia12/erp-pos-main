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
        table.display.w-100.text-nowrap.table-bordered.dataTable.dtr-inline {
            direction: rtl;
            text-align:center;
        }
        body{
            direction: rtl; 
        }
  
    </style>
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="col-lg-12 margin-tb">

                    </div>
                    <div class="clearfix"></div> 
                </div>  
                <div class="card-body px-0 pt-0 pb-2"> 
                    <div class="card shadow mb-3 ">
                        <div class="card-header py-3 " id="head-right"  style="direction: rtl;border:solid 1px gray"> 
                          <div class="row">
                            <div class="col-4" style=""> 
                                {{$company ? $company -> name_ar : ''}}
                               <br>  س.ت : {{$company ? $company -> taxNumber : ''}}
                               <br>  ر.ض :  {{$company ? $company -> registrationNumber : ''}}
                               <br>  تليفون :   {{$company ? $company -> phone : ''}}  
                            </div>   
                            <div class="col-4 title">   
                                <h4  class="alert alert-primary text-center">
                                    {{__('main.bank_movement_report')}}
                                </h4>
                                @if(isset($branch))
                                <h5 class="text-center"> [ {{$branch->branch_name}} ] </h5>
                                @else
                                <h5 class="text-center"> [ جميع الفروع ] </h5>
                                @endif

                                <h5 class="text-center">  {{Config::get('app.locale') == 'ar' ? $period_ar : $period}} </h5>
                            </div>
                            <div class="col-4"> 
                            </div>  
                          </div>
                        </div> 
                    </div>
                </div>   
 
                <div class="card-body">
                            <h4 class="text-center">  {{Config::get('app.locale') == 'ar' ? $period_ar : $period}} </h4>

                            <div class="table-responsive hoverable-table" style="direction: rtl;"> 
                                <table class="display w-100  text-nowrap table-bordered" id="example1" 
                                   style="text-align: center;direction: rtl;">
                                    <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">{{__('main.date')}}</th>
                                        <th class="text-center">{{__('main.basedon_no')}}</th>
                                        <th class="text-center">{{__('main.document_type')}}</th>
                                        <th class="text-center">{{__('main.Debit')}}</th>
                                        <th class="text-center">{{__('main.Credit')}}</th>
                                        <th class="text-center">{{__('main.balance')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php  $net = 0  ?>
                                    @foreach($holders as $holder )
                                        <tr>
                                            <td class="text-center">{{$loop -> index + 1}}</td>
                                            <td class="text-center">{{\Carbon\Carbon::parse($holder -> date) -> format('d-m-Y')}}</td>
                                            <td class="text-center">{{$holder -> docNumber}}</td>
                                            <td class="text-center">{{$holder -> docType}}</td>
                                            <td class="text-center">{{$holder -> debit}}</td>
                                            <td class="text-center">{{$holder -> credit}}</td>
                                            <td class="text-center">{{$holder -> debit - $holder -> credit }}</td>
                                            <?php  $net += ( $holder -> debit - $holder -> credit)  ?>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td class="text-center" colspan="6">{{__('main.total_balance')}}</td>
                                        <td class="text-center" >{{$net}}</td>
                                    </tr> 
                                    </tfoot>
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
        window.print();
    }


</script>
<script>
    $(document).ready(function () {
        document.title = "{{__('main.bank_movement_report')}}";
    });
</script>