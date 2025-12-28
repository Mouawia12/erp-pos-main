<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>
        ERP System Dashboard
    </title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.bootstrap5.min.css">


    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>


    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <link id="pagestyle" href="../assets/css/soft-ui-dashboard.css?v=1.0.7" rel="stylesheet" />
</head>

<body @if(Config::get('app.locale') == 'en') class="g-sidenav-show  bg-gray-100" @else  class="g-sidenav-show rtl bg-gray-100" @endif>
@include('layouts.side' , ['slag' => 6 , 'subSlag' => 130])


<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    @include('flash-message')
    <!-- Navbar -->
    @include('layouts.nav' , ['page_title' => __('main.representatives') ])
    <!-- End Navbar -->
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="row">
                            <div class="col-6 text-start">
                                <h6>{{ __('main.representatives')}}</h6>
                            </div>
                            <div class="col-6 text-end">
                                <button type="button" class="btn btn-labeled btn-primary " id="createButton">
                                    <span class="btn-label" style="margin-right: 10px;"><i class="fa fa-plus"></i></span>{{__('main.add_new')}}</button>
                            </div>
                        </div>

                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0 border">
                                <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">#</th>
                                    <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">{{__('main.code')}}</th>
                                    <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7 ps-2">{{__('main.name')}}</th>
                                    <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">{{__('main.user_name')}}</th>
                                    <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">{{__('main.rep_warehouse')}}</th>
                                    <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">{{__('main.rep_price_level')}}</th>
                                    <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">{{__('main.rep_discount_percent')}}</th>
                                    <th class="text-end text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">{{__('main.actions')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($representatives as $user)

                                <tr>
                                    <td class="text-center">{{$user -> id}}</td>
                                    <td class="text-center">{{$user -> code}}</td>
                                    <td class="text-center">{{$user -> name}} </td>
                                    <td class="text-center">{{$user -> user_name}}</td>
                                    <td class="text-center">{{ $user->warehouse?->name ?? '-' }}</td>
                                    <td class="text-center">{{ $user->price_level_id ? __('main.price_level').' '.$user->price_level_id : '-' }}</td>
                                    <td class="text-center">{{ $user->discount_percent ?? '-' }}</td>


                                    <td class="text-center">
                                        <button type="button" class="btn btn-labeled btn-secondary " value="{{$user -> id}}" onclick="EditModal({{$user -> id}})">
                                            <span class="btn-label" style="margin-right: 10px;"><i class="fa fa-pen"></i></span>{{__('main.edit')}}</button>

                                        <button type="button" class="btn btn-labeled btn-danger deleteBtn "  id="{{$user -> id}}">
                                            <span class="btn-label" style="margin-right: 10px;"><i class="fa fa-trash"></i></span>{{__('main.delete')}}</button>
                                        <br>
                                        <button type="button" class="btn btn-labeled btn-warning resetButton "  value="{{$user -> id}}">
                                            <span class="btn-label" style="margin-right: 10px;"></span>{{__('main.connect_with_client')}}</button>
                                        <button type="button" class="btn btn-labeled btn-info" data-bs-toggle="modal" data-bs-target="#docModal-{{$user->id}}">
                                            <span class="btn-label" style="margin-right: 10px;"><i class="fa fa-folder-open"></i></span>{{__('main.rep_documents')}}</button>

                                    </td>
                                </tr>

                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.footer')
    </div>
</main>
    @include('layouts.fixed')
<!--   Core JS Files   -->


<!--   Create Modal   -->
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <label class="modelTitle"> {{ __('main.representatives')}}</label>
                <button type="button" class="close modal-close-btn"  data-bs-dismiss="modal"  aria-label="Close" >
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="paymentBody">
                <form   method="POST" action="{{ route('storeRepresentative') }}"
                        enctype="multipart/form-data" >
                    @csrf

                    <div class="row">
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.code') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="text"  id="code" name="code"
                                       class="form-control"
                                       placeholder="{{ __('main.code') }}"  />
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>{{ __('main.name') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="text"  id="name" name="name"
                                       class="form-control"
                                       placeholder="{{ __('main.name') }}"  />
                                <input type="text"  id="id" name="id"
                                       class="form-control"
                                       placeholder="{{ __('main.code') }}"  hidden=""/>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.user_name') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="text"  id="user_name" name="user_name"
                                       class="form-control"
                                       placeholder="{{ __('main.user_name') }}"  />
                            </div>
                        </div>
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.password') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="password"  id="password" name="password"
                                       class="form-control"
                                       placeholder="{{ __('main.password') }}"  />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label>{{ __('main.rep_document_name') }}</label>
                                <input type="text" id="document_name" name="document_name"
                                       class="form-control"
                                       placeholder="{{ __('main.rep_document_name') }}"  />
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>{{ __('main.rep_document_number') }}</label>
                                <input type="text" id="document_number" name="document_number"
                                       class="form-control"
                                       placeholder="{{ __('main.rep_document_number') }}"  />
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label>{{ __('main.rep_document_expiry') }}</label>
                                <input type="date" id="document_expiry_date" name="document_expiry_date"
                                       class="form-control"
                                       placeholder="{{ __('main.rep_document_expiry') }}"  />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>{{ __('main.rep_warehouse') }}</label>
                                <select class="form-select" id="warehouse_id" name="warehouse_id">
                                    <option value="">{{ __('main.choose') }}</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                    @endforeach
                                </select>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" value="1" id="create_warehouse" name="create_warehouse">
                                    <label class="form-check-label" for="create_warehouse">
                                        {{ __('main.rep_create_warehouse') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>{{ __('main.rep_price_level') }}</label>
                                <select class="form-select" id="price_level_id" name="price_level_id">
                                    <option value="">{{ __('main.choose') }}</option>
                                    @for($i = 1; $i <= 6; $i++)
                                        <option value="{{$i}}">{{ __('main.price_level') }} {{$i}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>{{ __('main.rep_profit_margin') }}</label>
                                <input type="number" step="0.01" id="profit_margin" name="profit_margin"
                                       class="form-control"
                                       placeholder="{{ __('main.rep_profit_margin') }}"  />
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>{{ __('main.rep_discount_percent') }}</label>
                                <input type="number" step="0.01" id="discount_percent" name="discount_percent"
                                       class="form-control"
                                       placeholder="{{ __('main.rep_discount_percent') }}"  />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6" style="display: block; margin: 20px auto; text-align: center;">
                            <button type="submit" class="btn btn-labeled btn-primary"  >
                                {{__('main.save_btn')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="resetModal" tabindex="-1" role="dialog" aria-labelledby="resetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <label class="modelTitle"> {{ __('main.reset_pass')}}</label>
                <button type="button" class="close modal-close-btn"  data-bs-dismiss="modal"  aria-label="Close" >
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="paymentBody">
                <form   method="POST" action="{{ route('reset_password') }}"
                        enctype="multipart/form-data" >
                    @csrf

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('main.old_password') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="text"  id="original_password" name="original_password"
                                       class="form-control"
                                       placeholder="{{ __('main.old_password') }}"  />
                                <input type="text"  id="id" name="id"
                                       class="form-control"
                                       placeholder="{{ __('main.code') }}"  hidden=""/>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.password') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="text"  id="password" name="password"
                                       class="form-control"
                                       placeholder="{{ __('main.password') }}"  />
                            </div>
                        </div>
                        <div class="col-6 " >
                            <div class="form-group">
                                <label>{{ __('main.conf_password') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                                <input type="text"  id="confirm_password" name="confirm_password"
                                       class="form-control"
                                       placeholder="{{ __('main.conf_password') }}"  />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6" style="display: block; margin: 20px auto; text-align: center;">
                            <button type="submit" class="btn btn-labeled btn-primary"  >
                                {{__('main.reset_pass')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"  data-bs-dismiss="modal"  aria-label="Close" style="color: red; font-size: 20px; font-weight: bold;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="smallBody">
                <img src=assets/img/warning.png" class="alertImage">
                <label class="alertTitle">{{__('main.delete_alert')}}</label>
                <br> <label  class="alertSubTitle" id="modal_table_bill"></label>
                <div class="row">
                    <div class="col-6 text-center">
                        <button type="button" class="btn btn-labeled btn-primary" onclick="confirmDelete()">
                            <span class="btn-label" style="margin-right: 10px;"><i class="fa fa-check"></i></span>{{__('main.confirm_btn')}}</button>
                    </div>
                    <div class="col-6 text-center">
                        <button type="button" class="btn btn-labeled btn-secondary cancel-modal"  >
                            <span class="btn-label" style="margin-right: 10px;"><i class="fa fa-close"></i></span>{{__('main.cancel_btn')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="clientsModal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"  data-bs-dismiss="modal"  aria-label="Close" style="color: red; font-size: 20px; font-weight: bold;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="smallBody">
              <form  method="POST" action="{{ route('connect_to_client') }}"
                     enctype="multipart/form-data" >
                  @csrf
                  <div class="row" style="display: flex;align-items: end;">
                      <div class="col-6">
                          <div class="form-group">
                              <label>{{ __('main.client') }} <span style="color:red; font-size:20px; font-weight:bold;">*</span> </label>
                              <select name="client" id="client" class="form-select">

                              </select>
                              <input type="hidden" name="rep" id="rep">
                          </div>
                      </div>
                      <div class="col-6 text-center">
                          <button type="submit" class="btn btn-labeled btn-primary">
                              <span class="btn-label" style="margin-right: 10px;"><i class="fa fa-check"></i></span>{{__('main.connect_with_client')}}</button>
                      </div>
                  </div>
              </form>

               <table class="table-bordered" id="clients_table" style="width: 100%">
                   <thead>
                   <tr>
                       <th class="text-center">{{__('main.client')}}</th>
                       <th class="text-center">{{__('main.Actions')}}</th>
                   </tr>
                   </thead>
                   <tbody id="client_tbody">

                   </tbody>
               </table>
            </div>
        </div>
    </div>
</div>

@foreach($representatives as $user)
<div class="modal fade" id="docModal-{{$user->id}}" tabindex="-1" role="dialog" aria-labelledby="docModalLabel-{{$user->id}}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <label class="modelTitle">{{ __('main.rep_documents') }} - {{$user->name}}</label>
                <button type="button" class="close modal-close-btn" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('representatives.documents.store', $user) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{ __('main.rep_document_title') }}</label>
                                <input type="text" name="title" class="form-control" placeholder="{{ __('main.rep_document_title') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{ __('main.rep_document_type') }}</label>
                                <input type="text" name="document_type" class="form-control" placeholder="{{ __('main.rep_document_type') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>{{ __('main.rep_document_expiry') }}</label>
                                <input type="date" name="expiry_date" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>{{ __('main.rep_document_file') }}</label>
                                <input type="file" name="document" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">{{ __('main.add_new') }}</button>
                        </div>
                    </div>
                </form>

                <hr>
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead>
                        <tr>
                            <th class="text-center">{{ __('main.rep_document_title') }}</th>
                            <th class="text-center">{{ __('main.rep_document_type') }}</th>
                            <th class="text-center">{{ __('main.rep_document_expiry') }}</th>
                            <th class="text-center">{{ __('main.actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($user->documents as $doc)
                            <tr>
                                <td class="text-center">{{ $doc->title }}</td>
                                <td class="text-center">{{ $doc->document_type ?? '-' }}</td>
                                <td class="text-center">{{ $doc->expiry_date ?? '-' }}</td>
                                <td class="text-center">
                                    @if($doc->file_path)
                                        <a href="{{ asset('storage/'.$doc->file_path) }}" target="_blank" class="btn btn-sm btn-secondary">
                                            {{ __('main.open') }}
                                        </a>
                                    @endif
                                    <form method="POST" action="{{ route('representatives.documents.delete', $doc) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">{{ __('main.delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">{{ __('main.no_data') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<script type="text/javascript">
    let id = 0 ;
    $(document).ready(function()
    {
        id = 0 ;
        $(document).on('click', '#createButton', function(event) {
            id = 0 ;
            event.preventDefault();
            let href = $(this).attr('data-attr');
            $.ajax({
                url: href,
                beforeSend: function() {
                    $('#loader').show();
                },
                // return the result
                success: function(result) {
                    $('#createModal').modal("show");
                    $(".modal-body #name").val( "" );
                    $(".modal-body #last_name").val( "" );
                    $(".modal-body #gender").val( "" );
                    $(".modal-body #company").val( "" );
                    $(".modal-body #phone").val( "" );
                    $(".modal-body #code").val( "" );
                    $(".modal-body #id").val( 0 );
                    $(".modal-body #email").val( "" );
                    $(".modal-body #password").val( "" );
                    $(".modal-body #document_name").val( "" );
                    $(".modal-body #document_number").val( "" );
                    $(".modal-body #document_expiry_date").val( "" );
                    $(".modal-body #warehouse_id").val( "" );
                    $(".modal-body #price_level_id").val( "" );
                    $(".modal-body #profit_margin").val( "" );
                    $(".modal-body #discount_percent").val( "" );
                    $(".modal-body #create_warehouse").prop('checked', false);
                    $(".modal-body #status").val( "" );
                    $(".modal-body #group").val( "" );
                    $(".modal-body #conf_password").val( "" );


                    $(".modal-body #email").prop('readonly' , false);
                    $(".modal-body #password").prop('readonly' , false);
                    $(".modal-body #status").prop('readonly' , false);
                    $(".modal-body #group").prop('readonly' , false);
                    $(".modal-body #conf_password").prop('readonly' , false);
                    $(".modal-body #password").prop('type' , 'text');
                    $(".modal-body #conf_password").prop('type' , 'text');
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

        $(document).on('click', '.deleteClientModalBtn', function(event) {
            var dd = event.currentTarget.value ;

            let url = "{{ route('disconnectClientRep', ':id') }}";
            url = url.replace(':id', dd);
            document.location.href=url;

        });


        $(document).on('click', '.resetButton', function(event) {
            id = event.currentTarget.value ;
            $.ajax({
                type:'get',
                url:'/getRepresentativeClients' + '/' + id,
                dataType: 'json',

                success:function(response){
                    console.log(response);
                    event.preventDefault();
                    if(response){
                        let href = $(this).attr('data-attr');

                        $.ajax({
                            url: href,
                            beforeSend: function() {
                                $('#loader').show();
                            },
                            // return the result
                            success: function(result) {
                                $('#clientsModal').modal("show");

                                $(".modal-body #clients_table tbody").empty();


                                $('.modal-body #client')
                                    .empty()
                                    .append('<option selected="selected" value="">select</option>');
                                $('.modal-body #rep').val(id);



                                for(let i = 0 ; i < response.length ; i++){
                                    if(response[i].representative_id_ == id){
                                        var newTr = $('<tr data-item-id="'+response[i].id+'">');
                                        var tr_html ='<td class="text-center"> <span>'+response[i].name +'</span> </td>';
                                        tr_html += `<td class="text-center">      <button type="button" class="btn btn-labeled btn-danger deleteClientModalBtn "
                                          value= "  `+response[i].id + ` ">
                                            <span class="btn-label" style="margin-right: 10px;"><i class="fa fa-trash"></i></span></button> </td>`;
                                        newTr.html(tr_html);
                                        newTr.appendTo('#clients_table');
                                    }  if(response[i].representative_id_ == 0){
                                        $('.modal-body #client').append($("<option />").val(response[i].id).text(response[i].name));
                                    }

                                }
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


        });

        $(document).on('click' , '.cancel-modal' , function (event) {
            $('#resetModal').modal("hide");
            id = 0 ;
        });



    });
    function confirmDelete(){
        let url = "{{ route('deleteRepresentative', ':id') }}";
        url = url.replace(':id', id);
        document.location.href=url;
    }
    function EditModal(id){
        console.log(id);
        $.ajax({
            type:'get',
            url:'/getRepresentative' + '/' + id,
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
                            $(".modal-body #name").val( response.name  );
                            $(".modal-body #code").val( response.code  );
                            $(".modal-body #user_name").val( response.user_name  );
                            $(".modal-body #password").val( response.password  );
                            $(".modal-body #document_name").val( response.document_name  );
                            $(".modal-body #document_number").val( response.document_number  );
                            $(".modal-body #document_expiry_date").val( response.document_expiry_date  );
                            $(".modal-body #warehouse_id").val( response.warehouse_id  );
                            $(".modal-body #price_level_id").val( response.price_level_id  );
                            $(".modal-body #profit_margin").val( response.profit_margin  );
                            $(".modal-body #discount_percent").val( response.discount_percent  );
                            $(".modal-body #create_warehouse").prop('checked', false);
                            $(".modal-body #id").val( response.id  );
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


<script src="../assets/js/core/popper.min.js"></script>
<script src="../assets/js/core/bootstrap.min.js"></script>
<script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
<script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
<script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = {
            damping: '0.5'
        }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
</script>
<!-- Github buttons -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
<!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
<script src="../assets/js/soft-ui-dashboard.min.js?v=1.0.7"></script>
</body>

</html>
