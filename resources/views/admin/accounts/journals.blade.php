@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
<!-- row opened -->
<div class="row row-sm">
    <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="col-lg-12 margin-tb">
                        <h4  class="alert alert-primary text-center">
                            @if($type == 1)
                                {{__('main.journals')}}
                            @else
                                {{__('main.manual_journals')}}
                            @endif
                        </h4>
                        <div class="row mt-1 mb-1 text-center justify-content-center align-content-center"> 
                        @can('اضافة حسابات')   
                        <a href="{{route('manual_journal')}}" type="button" class="btn btn-labeled btn-primary"> 
                            <i class="fa fa-plus"></i>
                            {{__('main.add_new')}}
                        </a>
                        @endcan  
                    </div>  
                </div>    

                <div class="card-body px-0 pt-0 pb-2"> 
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <form   method="POST" action="{{ route('journals_search') }}"
                                    enctype="multipart/form-data" >
                                @csrf
                                <input type="hidden" id="type" name="type" value="{{$type}}" >
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label> تاريخ البداية <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                            <input type="checkbox" id="isStartDate" name="isStartDate">
                                            <input type="date" id="StartDate" name="StartDate"  class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label> تاريخ النهاية <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                            <input type="checkbox" id="isEndDate" name="isEndDate">
                                            <input type="date" id="EndDate" name="EndDate"  class="form-control">
                                        </div>
                                    </div>  
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label> رقم القيد <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                            <input type="checkbox" id="isCode" name="isCode">
                                            <input type="text" id="code" name="code"  class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" style="display: block; margin: 20px auto; text-align: center;">
                                        <button type="submit" class="btn btn-xl btn-info"  >
                                            <i class="fa fa-search"></i> {{__('main.search_btn')}}
                                        </button>
                                    </div>
                                </div> 
                            </form> 
                        </div>
                        <div class="clearfix"><hr></div> 
                        
                        <div class="card-body">
                            <div class="table-responsive"> 
                                <table class="display w-100  text-nowrap table-bordered" id="example1" 
                                   style="text-align: center;">
                                    <thead>
                                        <tr>
                                            <th>رقم القيد</th>
                                            <th>{{__('main.date')}}</th>
                                            <th>{{__('main.basedon_text')}}</th>
                                            <th>{{__('main.basedon_no')}}</th>
                                            <th>{{__('main.total_debit')}}</th>
                                            <th>{{__('main.total_credit')}}</th>  
                                            <th>{{__('main.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($journals as $unit)
                                        @if($type == 1)
                                            @if($unit->basedon_no != '')
                                            <tr>

                                                <td class="text-center">{{ str_pad( $unit->id , 6 , '0' , STR_PAD_LEFT)}}</td>
                                                <td class="text-center">{{$unit->date}}</td>
                                                <td class="text-center">{{$unit->baseon_text}}</td>
                                                <td class="text-center">{{$unit->basedon_no}}</td>
                                                <td class="text-center">{{$unit->debit_total}}</td>
                                                <td class="text-center">{{$unit->credit_total}}</td> 
                                               
                                                <td class="text-center">

                                                    <button type="button" class="btn btn-labeled btn-success"  onclick="showPayments({{$unit->id}})">
                                                        <i class="fa fa-eye"></i>
                                                        {{__('main.preview')}}
                                                    </button>

                                                    @if($unit->basedon_no == '')
                                                        <button type="button" class="btn btn-labeled btn-danger deleteBtn "  id="{{$unit->id}}">
                                                            <i class="fa fa-trash"></i> 
                                                            {{__('main.delete')}}
                                                        </button>
                                                    @endif 
                                                </td>
                                            </tr>
                                            @endif

                                        @else
                                            @if($unit->basedon_no == '')
                                            <tr> 
                                                <td class="text-center">{{ str_pad( $unit->id , 6 , '0' , STR_PAD_LEFT)}}</td>
                                                <td class="text-center">{{$unit->date}}</td>
                                                <td class="text-center">{{$unit->baseon_text}}</td>
                                                <td class="text-center">{{$unit->basedon_no}}</td>
                                                <td class="text-center">{{$unit->debit_total}}</td>
                                                <td class="text-center">{{$unit->credit_total}}</td>
                                                <td class="text-center">{{$unit->debit_totalg}}</td>
                                                <td class="text-center">{{$unit->credit_totalg}}</td>
                                               
                                                <td class="text-center">

                                                    <button type="button" class="btn btn-labeled btn-success"  onclick="showPayments({{$unit->id}})">
                                                        <span class="btn-label" style="margin-right: 10px;"><i class="fa fa-eye"></i></span>{{__('main.preview')}}</button>

                                                    @if($unit->basedon_no == '')
                                                        <button type="button" class="btn btn-labeled btn-danger deleteBtn "  id="{{$unit->id}}">
                                                            <span class="btn-label" style="margin-right: 10px;"><i class="fa fa-trash"></i></span>{{__('main.delete')}}</button>
                                                    @endif

                                                </td>

                                            </tr>
                                            @endif
                                        @endif


                                    @endforeach
                                    </tbody>

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

<div class="show_modal">

</div>
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
                <img src="{{asset('assets/img/warning.png')}}" class="alertImage">
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

<script>
    $(document).ready(function (){
        var now = new Date();

        var day = ("0" + now.getDate()).slice(-2);
        var month = ("0" + (now.getMonth() + 1)).slice(-2);
        var today = now.getFullYear()+"-"+(month)+"-"+(day) ;
        $('#isStartDate').prop('checked', false);
        $('#isEndDate').prop('checked', false);
        $('#isCode').prop('checked', false);

        $('#StartDate').prop('disabled', true);
        $('#EndDate').prop('disabled', true);
        $('#code').prop('disabled', true);
        $('#StartDate').val(today);
        $('#EndDate').val(today);
        $('#code').val('');

        $('#isCode').change(function (){
            console.log(this.checked);
            if(this.checked){
                $('#code').prop('disabled', false);
            } else {
                $('#code').prop('disabled', true);
            }
        });

        $('#isStartDate').change(function (){
            if(this.checked){
                $('#StartDate').prop('disabled', false);
            } else {
                $('#StartDate').prop('disabled', true);
            }
        });

        $('#isEndDate').change(function (){
            if(this.checked){
                $('#EndDate').prop('disabled', false);
            } else {
                $('#EndDate').prop('disabled', true);
            }
        });
    });
</script>

<script type="text/javascript">
    let id = 0 ;

    function showPayments(id) {
        var route = '{{route('preview_journal',":id")}}';
        route = route.replace(":id",id);

        $.get( route, function( data ) {
            $( ".show_modal" ).html( data );
            $('#paymentsModal').modal('show');
        });
    }
    $(document).ready(function()
    {
        id = 0 ;

        $(document).on('click', '.deleteBtn', function(event) {
            id = event.currentTarget.id ;
            event.preventDefault();
            let href = $(this).attr('data-attr');
            $.ajax({
                url: href,
                beforeSend: function() {
                    $('#loader').show();
                },
                // return the result
                success: function(result) {
                    $('#deleteModal').modal("show");
                },
                complete: function() {
                    $('#loader').hide();
                },
                error: function(jqXHR, testStatus, error) {
                    console.log(error);
                    alert("Page " + href + " cannot open. Error:" + error);
                    $('#loader').hide();
                },
                timeout: 8000
            })
        });

        $(document).on('click' , '.cancel-modal' , function (event) {
            $('#deleteModal').modal("hide");
            id = 0 ;
        });


    });
    function confirmDelete(){
        let url = "{{ route('delete_journal', ':id') }}";
        url = url.replace(':id', id);
        document.location.href=url;
    }
    function EditModal(id){
        $.ajax({
            type:'get',
            url:'getUnit' + '/' + id,
            dataType: 'json',

            success:function(response){
                console.log(response);
                if(response){
                    let href = $(this).attr('data-attr');
                    $.ajax({
                        url: href,
                        beforeSend: function() {
                            $('#loader').show();
                        },
                        // return the result
                        success: function(result) {
                            $('#createModal').modal("show");
                            $(".modal-body #name").val( response.name );
                            $(".modal-body #code").val( response.code );
                            $(".modal-body #id").val( response.id );

                        },
                        complete: function() {
                            $('#loader').hide();
                        },
                        error: function(jqXHR, testStatus, error) {
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
 