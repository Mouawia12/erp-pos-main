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
            <div class="card-body px-0 pt-0 pb-2"> 
                    <div class="card shadow mb-3 ">
                        <div class="card-header py-3 " id="head-right"  style="direction: rtl;border:solid 1px gray"> 
                          <div class="row">
                            <div class="col-3"> 
                                {{$company ? $company -> name_ar : ''}}
                               <br>  س.ت : {{$company ? $company -> taxNumber : ''}}
                               <br>  ر.ض :  {{$company ? $company -> registrationNumber : ''}}
                               <br>  تليفون :   {{$company ? $company -> phone : ''}}  
                            </div>   
                            <div class="col-6 title text-center">
                                <h4  class="alert alert-primary text-center">
                                    {{__('main.sold_items_report')}}
                                </h4>
                                @if(isset($branch))
                                <h5 class="text-center"> [ {{$branch->branch_name}} ] </h5>
                                @else
                                <h5 class="text-center"> [ جميع الفروع ] </h5>
                                @endif
                                <h5 class="text-center">  {{Config::get('app.locale') == 'ar' ? $period_ar : $period}} </h5>
                            </div>
                            <div class="col-3"> 
                            </div>  
                          </div>
                        </div>
                        <div class="card-body" style="direction: rtl;"> 
                            <div class="table-responsive hoverable-table" style="direction: rtl;"> 
                                <table class="display w-100  text-nowrap table-bordered" id="example1" 
                                   style="text-align: center;direction: rtl;">
                                    <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">
                                            #
                                        </th>
                                        <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7 ps-2">{{__('main.date')}}</th>
                                        <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7 ps-2">{{__('main.bill_no')}}</th>
                                        <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7 ps-2">{{__('main.code')}}</th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">{{__('main.name_ar')}}</th>
                                        {{-- <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.category')}} </th> --}}
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.karat')}} </th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.weight')}} </th>
                                        <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.no_metal')}} </th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php $total = 0 ; ?>
                                    @foreach($all as $item)
                                        <tr>
                                            <td class="text-center">{{$loop -> index + 1}}</td>
                                            <td class="text-center">{{ \Carbon\Carbon::parse($item -> bill_date) -> format('d-m-Y')  }}</td>
                                            <td class="text-center">{{$item -> bill_no}}</td>
                                            <td class="text-center">{{$item -> code}}</td>
                                            <td class="text-center">{{$item -> name_ar}}</td>
                                            {{-- <td class="text-center">{{Config::get('app.locale') == 'ar' ? $item -> category_name_ar : $item -> category_name_en }}</td> --}}
                                            <td class="text-center">{{ (Config::get('app.locale') == 'ar' ? $item -> karat_name_ar : $item -> karat_name_en)  }}</td>
                                            <td class="text-center">{{$item -> weight}}</td>
                                            <td class="text-center">{{$item -> no_metal}}</td>

                                            <?php $total += $item -> weight ; ?>

                                        </tr>
                                    @endforeach 
                                    </tbody>
                                    
                                    <tfoot>  
                                        <tr>
                                            <td colspan="7" class="text-center">الإجمالي</td>
                                            <td >{{$total  }} </td>  
                                        </tr>
                                    </tfoot>  

                                </table>

                            </div>


                        </div>
                    </div>

                </div>


            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content --> 
    </div>
    <!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->
 
@endsection
<script src="{{asset('assets/js/jquery.min.js')}}"></script> 
  
<!-- Page level custom scripts -->

<script type="text/javascript">
    let id = 0;


    $(document).ready(function () {
        $(document).on('click', '#btnPrint', function (event) {
            print();

        });

    });
</script>
<script>
    $(document).ready(function () {
        document.title = "{{__('main.sold_items_report')}}";
    });
</script>
 

