@extends('admin.layouts.master')
@section('content')
    @if (session('success'))
        <div class="alert alert-success  fade show">
            <button class="close" data-dismiss="alert" aria-label="Close">×</button>
            {{ session('success') }}
        </div>
    @endif
  
    @can('اضافة كمية') 
 
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0"  id="head-right" >
                        <div class="col-lg-12 margin-tb">
                            <h4  class="alert alert-primary text-center">
                            [ {{ __('main.add_update_qnt') }} ]
                            </h4>
                        </div>
                        <div class="clearfix"></div>
                    </div>
     
                    <div class="card-body">
                        <form   method="POST" action="{{ route('store_update_qnt') }}"
                                enctype="multipart/form-data" >
                            @csrf

                            <div class="row">
                               <div class="col-3">
                                    <div class="form-group">
                                        <label>{{ __('main.bill_number') }} <span class="text-danger">*</span> </label>
                                        <input type="text"  id="bill_number" name="bill_number"
                                               class="form-control" placeholder="bill_number" readonly
                                        />
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label>{{ __('main.bill_date') }} <span class="text-danger">*</span> </label>
                                        <input type="datetime-local"  id="bill_date" name="bill_date"
                                               class="form-control"
                                              />
                                    </div>
                                </div>
                                <div class="col-6 " >
                                    <div class="form-group">
                                        <label>{{ __('main.warehouse') }} <span class="text-danger">*</span> </label>
                                        <select class="js-example-basic-single w-100"
                                                name="warehouse_id" id="warehouse_id">
                                            <option  value="0" selected>حدد الاختيار</option>
                                            @foreach ($warehouses as $item)
                                                <option value="{{$item -> id}}"> {{ $item -> name}}</option>

                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div> 
                            <div class="row">
                                <div class="col-12">
                                    <div class="col-md-12" id="sticker">
                                        <div class="well well-sm" @if(Config::get('app.locale') == 'ar')style="direction: rtl;" @endif>
                                            <div class="form-group" style="margin-bottom:0;">
                                                <div class="input-group wide-tip">
                                                    <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;">
                                                        <i class="fa fa-2x fa-barcode addIcon"></i></div>
                                                    <input style="border-radius: 0 !important;padding-left: 10px;padding-right: 10px;"
                                                           type="text" name="add_item" value="" class="form-control input-lg ui-autocomplete-input" id="add_item" placeholder="{{__('main.add_item_hint')}}" autocomplete="off">

                                                </div>

                                            </div>
                                            <ul class="suggestions" id="products_suggestions" style="display: block">

                                            </ul>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card mb-4">
                                        <div class="card-header pb-0">
                                            <h4  class="alert alert-info text-center">{{__('main.items')}} </h4>
                                        </div>

                                         <div class="card-body px-0 pt-0 pb-2">
                                            <div class="table-responsive hoverable-table">
                                                <table class="display w-100  text-nowrap table-bordered" id="example2" 
                                                       style="text-align: center;">  
                                                     <thead>
                                                     <tr>
                                                         <th hidden>item_id</th>
                                                         <th class="text-center">{{__('main.item_name_code')}}</th>
                                                         <th hidden>متغير</th>
                                                         <th class="text-center">{{__('main.type')}} </th>
                                                         <th class="text-center">{{__('main.quantity')}} </th>
                                                         <th class="text-center">{{__('main.notes')}} </th>
                                                         <th style="max-width: 30px !important; text-align: center;" class="text-center">
                                                             
                                                         </th>
                                                     </tr>
                                                     </thead>
                                                     <tbody id="tbody"></tbody>
                                                     <tfoot></tfoot>
                                                 </table>
                                             </div>
                                         </div>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>{{ __('main.notes') }} <span class="text-danger">*</span> </label>
                                        <textarea name="notes" id="notes" rows="3" placeholder="{{ __('main.notes') }}" class="form-control" style="width: 100%"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <input type="submit" class="btn btn-primary" id="primary" tabindex="-1"
                                        style="width: 150px;
                                        margin: 30px auto;" value="{{__('main.save_btn')}}">
                                    </input>

                                </div>
                            </div> 
                        </form> 
                    </div>
                </div>
            </div>
        </div> 
    </div> 



<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                Alert!
                <button type="button" class="close"  data-bs-dismiss="modal"  aria-label="Close" style="color: red; font-size: 20px; font-weight: bold; background: white;
                height: 35px; width: 35px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="smallBody">
                <img src="../assets/img/warning.png" class="alertImage">
                <label class="alertTitle">{{__('main.notfound')}}</label>
                <br> <label  class="alertSubTitle" id="modal_table_bill"></label>
                <div class="row text-center">
                    <div class="col-6 text-center" style="display: block;margin: auto">
                        <button type="button" class="btn btn-labeled btn-primary cancel-modal"  >
                            <span class="btn-label" style="margin-right: 10px;"><i class="fa fa-check"></i></span>{{__('main.ok_btn')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endcan 
@endsection 
@section('js')
<script type="text/javascript">

    var suggestionItems = {};

    $(document).ready(function() {
        getBillNo();
        var now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());

        /* remove second/millisecond if needed - credit ref. https://stackoverflow.com/questions/24468518/html5-input-datetime-local-default-value-of-today-and-current-time#comment112871765_60884408 */
        now.setMilliseconds(null);
        now.setSeconds(null);

        document.getElementById('bill_date').value = now.toISOString().slice(0, -1);
        $('input[name=add_item]').change(function() {
            console.log($('#add_item').val());
        });
        $('#add_item').on('input',function(e){
            searchProduct($('#add_item').val());
        });
        // $('#add_item').keypress(function(event){
        //     var keycode = (event.keyCode ? event.keyCode : event.which);
        //     console.log($('#add_item').val());
        //     if(keycode == '13'){
        //         event.preventDefault();
        //         const code = event.target.value ;
        //         searchProduct(code);
        //     }
        // });
        $(document).on('click' , '.cancel-modal' , function (event) {
            $('#deleteModal').modal("hide");
            id = 0 ;
        });

        $(document).on('click' , '.deleteBtn' , function (event) {
            var row = $(this).parent().parent().index();
            console.log(row);
            var table = document.getElementById('tbody');
            table.deleteRow(row);
        });

        $(document).on('click', '.select_product', function () {
            var row = $(this).closest('li');
            var item_id = row.attr('data-item-id');
            addItemToTable(suggestionItems[item_id]);
            document.getElementById('products_suggestions').innerHTML = '';
            suggestionItems = {};
        });

    });


  function getBillNo(){
      let bill_number = document.getElementById('bill_number');
      $.ajax({
          type:'get',
          url:'getUpdateQntBillNo',
          dataType: 'json',

          success:function(response){
              console.log(response);

              if(response){
                  bill_number.value = response ;
              } else {
                  bill_number.value = '' ;
              }
          }
      });
  }
  function searchProduct(code){
      $.ajax({
          type:'get',
          url:'getProduct' + '/' + code,
          dataType: 'json',

          success:function(response){

              document.getElementById('products_suggestions').innerHTML = '';
              if(response){
                  if(response.length == 1){
                      //addItemToTable
                      addItemToTable(response[0]);
                  }else if(response.length > 1){
                      showSuggestions(response);
                  } else {
                      //showNotFoundAlert
                      openDialog();
                      document.getElementById('add_item').value = '' ;
                  }
              } else {
                  //showNotFoundAlert
                  openDialog();
                  document.getElementById('add_item').value = '' ;
              }
          }
      });
  }

    function showSuggestions(response) {
  
          $data = '';
          $.each(response,function (i,item) {
              suggestionItems[item.id] = item;
              $data +='<li class="select_product" data-item-id="'+item.id+'">'+item.name+'</li>';
          });
        document.getElementById('products_suggestions').innerHTML = $data;
    } 
    
    function openDialog(){
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
  }
  function addItemToTable(item){
      var table = document.getElementById("tbody");
      var repeate = document.getElementById( 'tbody-tr' + item.id);

      if(!repeate) {
          console.log(repeate);
          var row = table.insertRow(-1);
          row.id = 'tbody-tr' + item.id;
          row.className = "text-center";
          var cell0 = row.insertCell(0);
          var cell1 = row.insertCell(1);
          var cell2 = row.insertCell(2);
          var cell3 = row.insertCell(3);
          var cell4 = row.insertCell(4);
          var cell5 = row.insertCell(5);
          var cell6 = row.insertCell(6);
          cell0.hidden = true ;
          cell2.hidden = true ;
          cell0.innerHTML = item.id +'<input name="item_id[]" value="'+item.id+'" hidden>';
          cell1.innerHTML ='<td>'+ item.name + '---' + (item.code)+'</td>';
          cell2.innerHTML = "";
          cell3.innerHTML = `<td>
                                <select class="form-control" name="type[]" >
                                    <option id="1" value="1">{{__('main.add')}}</option>
                                    <option id="2" value="2">{{__('main.sub')}}</option>
                                </select> 
                           </td>`;
          cell4.innerHTML = `<td><input class="form-control" type="number" name="qnt[]" value="1" /> </td>`;
          cell5.innerHTML = `<td><input class="form-control" type="text" name="notes[]" /> </td>`;
          cell6.innerHTML = `<td>   
                                <button type="button" class="btn btn-labeled btn-danger deleteBtn " value=" '+item.id+' ">
                                    <span class="btn-label" style="margin-right: 10px;">
                                    <i class="fa fa-close"></i></span>
                                </button> 
                            </td>`;
      }else {
          var tds = repeate.getElementsByTagName('td');
          var qntTd = tds[4];
          var qntInp = qntTd.getElementsByTagName("input")[0];
          var oldQnt = qntInp.value ;
          var qnt = Number(oldQnt) + 1 ;
          qntInp.value = qnt ;
          //increaseQnt
      }
      document.getElementById('add_item').value = '' ;
  }
 
</script>
@endsection 