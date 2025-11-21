


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Gold MS</title>

    <!-- Custom fonts for this template-->
    <link href="{{asset('assets/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">

    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="{{asset('assets/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="{{asset('assets/css/sb-admin-2.css')}}" rel="stylesheet">


</head>

<body id="page-top" @if(Config::get('app.locale') == 'ar') style="direction: rtl" @endif>

<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    @include('layouts.side' , ['slag' => 16 , 'subSlag' => 166])
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            <!-- Topbar -->
            @include('layouts.header')
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            <div class="container-fluid">
                @include('flash-message')
                <div class="d-sm-flex align-items-center justify-content-between mb-4" style="padding: 8px">
                    <h1 class="h3 mb-0 text-primary-800">{{__('main.accounting')}} / {{__('main.accounts')}}</h1>
                    <a href="{{route('create_account')}}"
                       class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"
                       style="border-radius: 10px; margin:5px;: 5px;"><i style="margin: 5px ; padding: 5px;"
                                                                         class="fas fa-plus-circle fa-sm text-white-50"></i> {{__('main.add_new')}}
                    </a>
                </div>

                <div class="card-body px-0 pt-0 pb-2">

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">{{__('main.accounts')}}</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <ul id="myUL">
                                    @foreach($accounts as $account)
                                        <li>
                                            <span class="caret">{{$account -> name . ' --- ' . $account -> code}}</span>
                                                <ul class="nested">
                                                    @foreach($account -> childs as $child)
                                                        <li>{{$child -> name . ' --- ' . $child -> code}}</li>
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
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->

        <!-- Footer -->
        @include('layouts.footer')
        <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>


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


<script src="{{asset('assets/vendor/jquery/jquery.min.js')}}"></script>
<script src="{{asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

<!-- Core plugin JavaScript-->
<script src="{{asset('assets/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

<!-- Custom scripts for all pages-->
<script src="{{asset('assets/js/sb-admin-2.min.js')}}"></script>


<script src="{{asset('assets/vendor/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>

<!-- Page level custom scripts -->
<script src="{{asset('assets/js/demo/datatables-demo.js')}}"></script>

</body>

</html>
