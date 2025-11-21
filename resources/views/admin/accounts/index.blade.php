@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
    <style>
        ul, #myUL {
            list-style-type: none;
        }

        /* Remove margins and padding from the parent ul */
        #myUL {
            margin: 0;
            padding: 0;
        }

        /* Style the caret/arrow */
        .caret {
            cursor: pointer;
            user-select: none; /* Prevent text selection */
        }

        /* Create the caret/arrow with a unicode, and style it */
        .caret::before {
            content: "\25B6";
            color: black;
            display: inline-block;
            margin-right: 6px;
        }

        /* Rotate the caret/arrow icon when clicked on (using JavaScript) */
        .caret-down::before {
            transform: rotate(90deg);
        }

        /* Hide the nested list */
        .nested {
            display: none;
        }

        /* Show the nested list when the user clicks on the caret/arrow (with JavaScript) */
        .active {
            display: block;
        }
    </style>
<!-- row opened -->
<div class="row row-sm">
    <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0 text-center">
                    <div class="col-lg-12 margin-tb ">
                        <h4  class="alert alert-primary text-center"> 
                        {{__('main.accounts')}}
                        </h4>
                    </div>
                    <div class="clearfix"></div>
                    @can('اضافة حسابات')  
                        <a href="{{route('create_account')}}"
                           class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"
                           style="border-radius: 10px; margin:5px;: 5px;">
                           <i style="margin: 5px ; padding: 5px;"  class="fas fa-plus-circle fa-sm text-white-50"></i> 
                           {{__('main.add_new')}}
                        </a>
                    @endcan 
                    </div>  
            </div>  
                <div class="card-body px-0 pt-0 pb-2"> 
                    <div class="card shadow mb-4"> 
                        <div class="card-body">
                            <div class="row">
                                <div class="col-8">
                                    <div class="table-responsive">
                                        <table class="display w-100 table-bordered" id="example1" 
                                               style="text-align: center;">
                                            <thead>
                                                <tr>
                                                    <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">
                                                        #
                                                    </th>
                                                    <th class="text-uppercase text-secondary text-md-center font-weight-bolder opacity-7 ps-2">{{__('main.code')}}</th>
                                                    <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">{{__('main.name')}}</th>
                                                    <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.account_type')}} </th>
                                                    <th class="text-center text-uppercase text-secondary text-md-center font-weight-bolder opacity-7"> {{__('main.parent_account')}} </th>
                                                    <th class="text-end text-uppercase text-secondary text-md-center font-weight-bolder opacity-7">{{__('main.actions')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($accounts as $unit)
                                                <tr>
                                                    <td class="text-center">{{$unit->id}}</td>
                                                    <td class="text-center">{{$unit->code}}</td>
                                                    <td class="text-center">{{$unit->name}}</td>
                                                    <td class="text-center">
                                                        @if($unit->type == 0)
                                                            {{__('main.Root')}}
                                                        @elseif($unit->type == 1)
                                                            {{__('main.General')}}
                                                        @elseif($unit->type == 2)
                                                            {{__('main.Branch')}}
                                                        @elseif($unit->type == 3)
                                                            {{__('main.Branch_Ledger')}}
                                                        @endif


                                                    </td>
                                                    <td class="text-center">{{$unit->parent_code}}</td>
                                                    <td class="text-center">
                                                    @can('تعديل حسابات') 
                                                        <a href="{{route('edit_account' , $unit -> id)}}">
                                                            <button type="button" class="btn btn-labeled btn-info ">
                                                                <i class="fa fa-pen"></i></button>
                                                        </a>
                                                    @endcan 
                                                    @can('حذف الحسابات') 
                                                        <button type="button" class="btn btn-labeled btn-danger deleteBtn "  id="{{$unit->id}}">
                                                            <i class="fa fa-trash"></i></button>
                                                    @endcan 
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>

                                        </table>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <ul id="myUL" class="myUL text-left">
                                        @foreach($roots as $root)
                                            <li>
                                                <span class="caret">{{$root -> name . ' --- ' . $root -> code}}</span>
                                                <?php $childs = [];  ?>
                                                <?php $childs2 = [];  ?>
                                                <?php $childs3 = [];  ?>
                                                <?php $childs4 = [];  ?>
                                                <?php $childs5 = [];  ?>
                                                <?php $childs = \App\Models\AccountsTree::where('parent_id' , '=' , $root -> id ) -> get()   ?>
                                                <ul class="nested">
                                                    @foreach($childs as $child)
                                                        <?php $childs2 = \App\Models\AccountsTree::where('parent_id' , '=' , $child -> id ) -> get()   ?>
                                                    @if( count($childs2) > 0 )
                                                                <li>
                                                                    <span class="caret"> {{$child -> name . ' --- ' . $child -> code}}  </span>
                                                                    <ul class="nested">
                                                                        @foreach($childs2 as $child2)
                                                                            <?php $childs3 = \App\Models\AccountsTree::where('parent_id' , '=' , $child2 -> id ) -> get()   ?>
                                                                                @if( count($childs3) > 0 )
                                                                               <li>
                                                                                   <span class="caret"> {{$child2 -> name . ' --- ' . $child2 -> code}}  </span>
                                                                                   <ul class="nested">
                                                                                       @foreach($childs3 as $child3)
                                                                                           <?php $childs4 = \App\Models\AccountsTree::where('parent_id' , '=' , $child3 -> id ) -> get()   ?>
                                                                                               @if( count($childs4) > 0 )
                                                                                                   <li>
                                                                                                       <span class="caret"> {{$child3 -> name . ' --- ' . $child3 -> code}}  </span>
                                                                                                       <ul class="nested">
                                                                                                           @foreach($childs4 as $child4)
                                                                                                       <?php $childs5 = \App\Models\AccountsTree::where('parent_id' , '=' , $child4 -> id ) -> get()   ?>
                                                                                                           @if( count($childs5) > 0 )
                                                                                                               <li>
                                                                                                                   <span class="caret"> {{$child4 -> name . ' --- ' . $child4 -> code}}  </span>

                                                                                                               </li>
                                                                                                               @else
                                                                                                               <li> {{$child4 -> name . ' --- ' . $child4 -> code}}

                                                                                                               </li>
                                                                                                           @endif
                                                                                                           @endforeach
                                                                                                       </ul>
                                                                                                   </li>
                                                                                                   @else
                                                                                                   <li> {{$child3 -> name . ' --- ' . $child3 -> code}}</li>
                                                                                               @endif
                                                                                       @endforeach
                                                                                   </ul>

                                                                               </li>
                                                                                @else
                                                                                    <li> {{$child2 -> name . ' --- ' . $child2 -> code}}</li>
                                                                                @endif

                                                                        @endforeach
                                                                    </ul>
                                                                </li>
                                                        @else
                                                                <li> {{$child -> name . ' --- ' . $child -> code}}</li>
                                                            @endif

                                                    @endforeach

                                                </ul>

                                            </li> 
                                        @endforeach 
                                    </ul>
                                </div>
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
        var toggler = document.getElementsByClassName("caret");
        var i;

        for (i = 0; i < toggler.length; i++) {
            toggler[i].addEventListener("click", function() {
                this.parentElement.querySelector(".nested").classList.toggle("active");
                this.classList.toggle("caret-down");
            });
        }

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
        let url = "{{ route('delete_account', ':id') }}";
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

 
