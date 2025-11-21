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
                                {{__('main.item_list_report')}}
                                </h4> 
                                @if(isset($branch))
                                <h5 class="text-center"> [ {{$branch->branch_name}} ] </h5>
                                @else
                                <h5 class="text-center"> [ جميع الفروع ] </h5>
                                @endif
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
                                        <th >
                                            #
                                        </th>
                                        <th class="">{{__('main.code')}}</th>
                                        <th >{{__('main.name_ar')}}</th>
                                        <th > {{__('main.category')}} </th>
                                        <th > {{__('main.karat')}} </th>
                                        <th > {{__('main.weight')}} </th>
                                        <th > {{__('main.gram_made_value')}} </th>
                                        <th > {{__('main.made_Value_t')}} </th>

                                        <th > {{__('main.no_metal')}} </th>
                                        <th > {{__('main.state')}} </th>

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
                                    </tbody> 
                                    <tfoot>  
                                        <tr>
                                            <td colspan="6" class="text-center">الإجمالي</td>
                                            <td colspan="2" class="text-center">{{$sum_weight}}</td> 
                                            <td colspan="2" class="text-center">{{$sum_made}}</td> 
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
<!-- End of Page Wrapper --> 
@endsection
<script src="{{asset('assets/js/jquery.min.js')}}"></script>

<script type="text/javascript">
    let id = 0;


    $(document).ready(function () {
        $(document).on('click', '#btnPrint', function (event) {
            print();

        });

    });
</script>
 



