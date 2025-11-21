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
                <div class="card-header pb-0" id="card-header">  
                </div>  
                <div class="card-body px-0 pt-0 pb-2"> 
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 " style="border:solid 1px gray">
                            <header>
                                    <div class="row" style="direction: ltr;">
                                        <div class="col-4 text-left">   
                                            <br> 
                                            <button type="button" class="btn btn-primary btnPrint" id="btnPrint"><i class="fa fa-print"></i></button>
                                        </div>
                                        <div class="col-4 c">
                                            <h4  class="alert alert-primary text-center">
                                               {{__('main.incoming_list')}}
                                              
                                            </h4> 
                                            <h5>[ {{ Config::get('app.locale') == 'ar' ? $period_ar : $period}} ]   </h5>
                                        </div>
                                        <div class="col-4 c">
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
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0 border">
                                <thead>
                                <tr>
                                    <th style="font-size:18px;">الحساب</th>
                                    <th style="font-size:18px;">القيمة</th>
                                </tr> 
                                </thead>
                                <?php
                                    $sales_net = 0;
                                    $sales_cost = 0;
                                    $rabeh_total = 0;
                                    $masrof_net = 0;
                                    $irads_net = 0;
                                    $rabeh_net = 0;
                                ?>
                                <tbody> 
                                    <tr>
                                        @if(isset($sales->debit))
                                        <td class="text-center" style="text-align: right !important;">صافي المبيعات</td>
                                         <?php 
                                            
                                                $issales = $sales->debit >0 ? $sales->debit - $sales->credit : $sales->credit;
                                                if($issales < 0){
                                                     $issales =  $issales * -1;
                                                }
                                               
                                                if(isset($return_sales->debit) and $return_sales->debit > 0){
                                                    $isreturn = $return_sales->debit - $return_sales->credit ;
                                                }elseif(isset($return_sales->credit)){
                                                    $isreturn = $return_sales->credit;
                                                }else{
                                                    $isreturn = 0;
                                                }
                                                
                                                $sales_net =  $issales - $isreturn; 
                                         ?>
                                        <td class="text-center" style="text-align: right !important;">{{number_format($sales_net,2)}}</td>
                                        @endif
                                    </tr> 
                                    <tr>
                                        
                                        @if(isset($period_start)) 
                                        <td class="text-center" style="text-align: right !important;">تكلفة المبيعات</td>
                                        <?php
                                            $iscost = $cost * $pricings -> price_21; 
                                            $sale_weight = $sale_weight * $pricings -> price_21; 
                                            $isperiod_start = ($period_start - $cost_sale) * $pricings -> price_21;
                                            //$isperiod_end =  ($period_end->debit -  $period_end->credit)* $pricings ->price_21; 
                                            $purchases = $purchase ;
                                            $isperiod_end = ($isperiod_start + $iscost) - $sale_weight; 
                                            $sales_cost = ($isperiod_start + $iscost) - $isperiod_end;
                                         
                                        ?>
                                        <td class="text-center" style="text-align: right !important;"> {{ number_format($sales_cost,2) }} = مخزون اول المده ({{  number_format($isperiod_start,2) }}) + بضاعة مشتراه ({{ number_format($iscost,2) }}) - مخزون اخر المده ({{ number_format($isperiod_end,2) }})</td>
                                         
                                        @endif
                                    </tr> 
                                    <tr>
                                        <td class="text-center" style="text-align: right !important;"> مجمل الربح</td>
                                        <?php
                                            $rabeh_total = $sales_net - $sales_cost;
                                        ?>
                                        <td class="text-center" style="text-align: right !important;">{{number_format($rabeh_total,2)}}</td>
                                    </tr> 
                                    <tr>
                                        @if($masrof->debit)
                                        <td class="text-center" style="text-align: right !important;"> المصروفات</td>
                                        <?php
                                            $masrof_net = $masrof->debit > 0 ?  $masrof->debit - $masrof->credit : $masrof->credit;
                                        ?>
                                        <td class="text-center" style="text-align: right !important;">{{number_format($masrof_net,2)}}</td>
                                        @endif
                                    </tr> 
                                    <tr>
                                        <td class="text-center" style="text-align: right !important;"> هامش الربح</td>
                                        <?php
                                          $rabeh_net = $rabeh_total - $masrof_net;
                                        ?>
                                        <td class="text-center" style="text-align: right !important;">{{number_format($rabeh_net,2)}}</td>
                                    </tr> 
                                    <tr>
                                        <td class="text-center" style="text-align: right !important;"> ايرادات اخرى</td>
                                        <?php
                                            if(isset($irad->debit))
                                               $irads_net = $irad->debit > 0 ?  $irad->debit - $irad->credit : $irad->credit;
                                            else if(isset($irad->credit))
                                               $irads_net = $irad->credit;    
                                        ?>
                                        <td class="text-center" style="text-align: right !important;">{{ $irads_net }}</td>
                                    </tr> 
                                    <tr>
                                        <td class="text-center" style="text-align: right !important;">  صافي الربح </td> 
                                        <td class="text-center" style="text-align: right !important;">{{number_format($rabeh_net + $irads_net,2) }}</td>
                                    </tr> 
                                </tbody>
                            </table>
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
 
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <label class="modelTitle"> {{__('main.deleteModal')}}</label>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"
                        style="color: red; font-size: 20px; font-weight: bold;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="smallBody">
                <img src="../assets/img/warning.png" class="alertImage">
                <label class="alertTitle">{{__('main.delete_alert')}}</label>
                <br> <label class="alertSubTitle" id="modal_table_bill"></label>
                <div class="row">
                    <div class="col-6 text-center">
                        <button type="button" class="btn btn-labeled btn-primary" onclick="confirmDelete()">
                            <span class="btn-label" style="margin-right: 10px;"><i
                                    class="fa fa-check"></i></span>{{__('main.confirm_btn')}}</button>
                    </div>
                    <div class="col-6 text-center">
                        <button type="button" class="btn btn-labeled btn-secondary cancel-modal">
                            <span class="btn-label" style="margin-right: 10px;"><i
                                    class="fa fa-close"></i></span>{{__('main.cancel_btn')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 
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
        document.getElementById("card-header").style.display = 'none'; 
        
        window.print();
        document.getElementById("main-header").style.display = 'block';
        document.getElementById("main-footer").style.display = 'block';
        document.getElementById("card-header").style.display = 'block'; 
    }


</script>
 

 
<script type="text/javascript">
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#profile-img-tag').attr('src', e.target.result);

            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#image_url").change(function () {
        readURL(this);
    });
</script>

<script type="text/javascript">
    let id = 0;
    $(document).ready(function () {
        id = 0;
 

        $(document).on('click', '#createButton', function (event) {
            console.log('clicked');
            id = 0;
            event.preventDefault();
            let href = $(this).attr('data-attr');
            $.ajax({
                url: href,
                beforeSend: function () {
                    $('#loader').show();
                },
                // return the result
                success: function (result) {
                    $('#createModal').modal("show");
                    $(".modal-body #code").val("");
                    $(".modal-body #name_ar").val("");
                    $(".modal-body #name_en").val("");
                    $(".modal-body #item_type").val("");
                    $(".modal-body #category_id").val("");
                    $(".modal-body #karat_id").val("");
                    $(".modal-body #weight").val("");
                    $(".modal-body #no_metal").val("");
                    $(".modal-body #no_metal_type").val("");
                    $(".modal-body #tax").val("");
                    $(".modal-body #made_Value").val("");
                    $(".modal-body #state").val("");
                    $(".modal-body #id").val(0);

                    $(".modal-body #karat_id").change(function (){
                        $.ajax({
                            type: 'get',
                            url: 'getKarat' + '/' + this.value,
                            dataType: 'json',

                            success: function (response) {

                                $(".modal-body #tax").val(response.stamp_value);
                            }
                        });
                    });

                },
                complete: function () {
                    $('#loader').hide();
                },
                error: function (jqXHR, testStatus, error) {
                    console.log(error);
                    alert("Page " + href + " cannot open. Error:" + error);
                    $('#loader').hide();
                },
                timeout: 8000
            })
        });

        $(document).on('click', '.deleteBtn', function (event) {
            console.log('clicked');
            id = event.currentTarget.value;
            event.preventDefault();
            let href = $(this).attr('data-attr');
            $.ajax({
                url: href,
                beforeSend: function () {
                    $('#loader').show();
                },
                // return the result
                success: function (result) {
                    $('#deleteModal').modal("show");
                },
                complete: function () {
                    $('#loader').hide();
                },
                error: function (jqXHR, testStatus, error) {
                    console.log(error);
                    alert("Page " + href + " cannot open. Error:" + error);
                    $('#loader').hide();
                },
                timeout: 8000
            })
        });

        $(document).on('click', '.cancel-modal', function (event) {
            $('#deleteModal').modal("hide");
            id = 0;
        });
        $(document).on('click', '.close-create', function (event) {
            $('#createModal').modal("hide");
            id = 0;
        });


    });

    function confirmDelete() {
        let url = "{{ route('workEntryDelete', ':id') }}";
        url = url.replace(':id', id);
        document.location.href = url;
    }

    function EditModal(id) {
        $.ajax({
            type: 'get',
            url: 'getCategory' + '/' + id,
            dataType: 'json',

            success: function (response) {
                console.log(response);
                if (response) {
                    let href = $(this).attr('data-attr');
                    $.ajax({
                        url: href,
                        beforeSend: function () {
                            $('#loader').show();
                        },
                        // return the result
                        success: function (result) {
                            $('#createModal').modal("show");
                            var img = '../images/Category/' + response.image_url;
                            $(".modal-body #profile-img-tag").attr('src', img);
                            $(".modal-body #name").val(response.name);
                            $(".modal-body #code").val(response.code);
                            $(".modal-body #slug").val(response.slug);
                            $(".modal-body #description").val(response.description);
                            $(".modal-body #parent_id").val(response.parent_id);
                            $(".modal-body #id").val(response.id);
                            $(".modal-body #isGold").prop('checked', response.isGold);


                        },
                        complete: function () {
                            $('#loader').hide();
                        },
                        error: function (jqXHR, testStatus, error) {
                            console.log(error);
                            alert("Page " + href + " cannot open. Error:" + error);
                            $('#loader').hide();
                        },
                        timeout: 8000
                    })
                } else {

                }
            }
        });
    }

 
</script>

