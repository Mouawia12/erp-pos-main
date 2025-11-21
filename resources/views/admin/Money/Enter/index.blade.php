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
            <div class="card-header pb-0" id="head-right" >
                <div class="col-lg-12 margin-tb">
                    <h4  class="alert alert-primary text-center">
                        {{__('main.money.input')}}
                    </h4>
                </div>
                <div class="clearfix"></div>
            </div>  

            <div class="card-body px-0 pt-0 pb-2"> 
                <div class="card shadow mb-4"> 
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="display w-100  text-nowrap table-bordered" id="ItemTable" 
                               style="text-align: center;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{__('main.date')}}</th>
                                        <th>{{__('main.bill_no')}}</th>
                                        <th>{{__('main.branche')}}</th>  
                                        <th> {{__('main.client')}} </th>
                                        <th> {{__('main.paid_money')}} </th>
                                        <th> {{__('main.payment_method')}} </th> 
                                        <th> {{__('main.based_on')}} </th>
                                        <th>{{__('main.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody></tbody> 
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <!--/div--> 

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
@section('js')  
<script type="text/javascript">
          $(document).ready(function () {
    
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            document.title = "{{__('main.money.input')}}";

            var table = $('#ItemTable').DataTable({
                processing: true,
                //serverSide: true,
                responsive: true,

                ajax: "{{ route('money.entry.list') }}",
                columns: [
                    {
                        data: 'id', 
                        name: 'id'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'doc_number',
                        name: 'doc_number'
                    },
                    {
                        data: 'branch_name',
                        name: 'branch_name'
                    }, 
                    {
                        data: 'vendor_name',
                        name: 'vendor_name'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    }, 
                    {
                        
                        data: 'payment_method',
                        name: 'payment_method',
                        orderable: false,
                        searchable: false 
                    },
                    {
                        
                        data: 'based_on',
                        name: 'based_on',
                        orderable: false,
                        searchable: false 
                    },
                    {
                        
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false 
                    },
                ],
                dom: 'lBfrtip',
                
                buttons: [
                    "copy","excel", "print", "colvis"
                ],
                
                "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
                order: [[0, 'desc']]
            }).buttons().container().appendTo('#ItemTable_wrapper .col-md-6:eq(0)');

            
        });
</script> 
 
@endsection
 