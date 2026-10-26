@extends('app')
@section('title','price Master')
@section('style')
<style type="text/css">
.label {
    display: inline;
    padding: .2em .6em .3em;
    font-size: 75%;
    font-weight: 700;
    line-height: 1;
    color: #fff;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: .25em;
  }
  .label-success, .modal-success .modal-body {
    background-color: #00a65a !important;
}
.label-danger
{
      background-color: red !important;

}
.fa-thumbs-down {
    color: red!important;
}
.fa-thumbs-up {
    color: green !important;
}
.error
    {
        color: red !important;
    }
    body{
   color: #6c757d;
  } 
</style>
@endsection
@section('content')

<!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h4> Price Master</h4>
          </div>
          <div class="col-sm-6">
           <!-- <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">Price Master</li>
              <li class="breadcrumb-item active"><a href="{{route("price_master")}}">Price Master</a></li>
            </ol>-->
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
          @if ($errors->any())
        <div class="alert alert-danger">
          @foreach ($errors->all() as $error)
                {{ $error }}
            @endforeach
        </div>     
        @endif
           @include('layouts/alert')

            <div class="card">
              <div class="card-header">
                 <h3 class="card-title  col-lg-9 row">
                   
                  </h3>
                  <div class="card-tools">
                      <a class="btn btn-info btn-md" id="create" href="#"><i class="fa fa-plus"></i>&nbsp;&nbsp;Create</a>
                  </div>
                  
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table  class="table table-striped" id="trade_table">
                <thead>
                                <tr>
                                 <th align="left" valign="middle">Console Name</th>
                                 <th align="left" valign="middle">Hours</th>
                                 <th align="left" valign="middle">No of Players</th>
                                 <th align="left" valign="middle">Price</th>
                                 <th align="left" valign="middle">Status</th>
                                 <th align="left" valign="middle">Action</th>
                                </tr>
                            </thead>
                         <tbody>
                            </tbody>
                  
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
<!-- Modal -->
<div class="modal fade" id="AddFeature" role="dialog" >
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <form id="saveTrade" action="{{route('price_save')}}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" id="tid" name="tid"  />
      <div class="modal-header">
      <h4 class="modal-title">Price master</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
          <div class="form-group ">
              <label for="price" ><strong>Console Name<span class="error">*</span></strong> </label> 
              <select class="form-control" name="console_id" id="console_id" required>
    <option value="">Select Console</option>
    @foreach ($console_list as $val)
        <option value="{{ $val->id }}"
                data-limit="{{ $val->limited_person }}">
            {{ $val->console_name }}
        </option>
    @endforeach
</select>

              <label class="error"> @if($errors->any())
                {{ $errors->first('price') }}
               @endif 
              </label>
          </div> 
          <div class="form-group ">
            <label for="price" ><strong>Hours<span class="error">*</span></strong> </label> 
            <select class="form-control" name="hours_id" id="hours_id" required>
              <option value="">Select Hours</option>
              @if(count($hours_list)>0)
              @foreach ($hours_list as $ky=>$val )
              <option value="{{$val->id}}">{{$val->hours}}</option>
              @endforeach
              @endif
            </select>            
            <label class="error"> @if($errors->any())
              {{ $errors->first('price') }}
             @endif 
            </label>
        </div> 
        <div class="form-group ">
          <label for="price" ><strong>No of Players<span class="error">*</span></strong> </label> 
         
         <select class="form-control" name="no_of_players" id="no_of_players" required>
    <option value="">Select No of players</option>
</select>
        
          <label class="error"> @if($errors->any())
            {{ $errors->first('price') }}
           @endif 
          </label>
      </div> 
        <div class="form-group ">
          <label for="price" ><strong>Price<span class="error">*</span></strong> </label> 
          <input type="text" class="form-control custom-trade"  id="price" name="price" placeholder="" required="" >               
          <label class="error"> @if($errors->any())
            {{ $errors->first('price') }}
           @endif 
          </label>
      </div>  
      </div>
            <div class="clearfix"></div>  
      <div class="modal-footer">
          <button type="submit" class="btn btn-sm btn-success featuresubmit">Save</button>
        <button type="reset" class="btn btn-sm btn-default" data-dismiss="modal">Cancel</button>
      </div>
            </form> 
    </div>
  </div>
</div>
@endsection
@section('script')
<script>
$('#console_id').on('change', function () {

    let limit = $('#console_id option:selected').data('limit');
    let $players = $('#no_of_players');

    $players.empty();
    $players.append('<option value="">Select No of players</option>');

    if (!limit) return;

    for (let i = 1; i <= limit; i++) {
        $players.append(
            '<option value="'+i+'">'+i+'</option>'
        );
    }
});
</script>

<script>
  $(document).ready(function () {
var dataTable =$('#trade_table').DataTable({

             processing: true,
             serverSide: true,
             'ajax': {
                   'url':'{{route('master_ajax_price_list')}}',
                    "type": "GET",
                   'data': function(data){
                  
                    }
       },
             columns: [
                
                 { data: 'console_name' },
                 { data: 'hours' },
                 { data: 'players' },
                 { data: 'price' },
                 { data: 'status' },
                 { data: 'action' },
             ],
              "columnDefs": [
                              
                            ]
         });
 

 
 $('#saveTrade').validate({ // initialize the plugin

                    rules: {
                      console_id: { 
                            required: true
                        } 
                    },
                    messages: {

                      console_id: {
                        required: 'Console name is Required'
                      },
                      errorElement: 'span',
                        errorPlacement: function (error, element) {
                          error.addClass('invalid-feedback');
                          element.closest('.form-group').append(error);
                        },
                        highlight: function (element, errorClass, validClass) {
                          $(element).addClass('is-invalid');
                        },
                        unhighlight: function (element, errorClass, validClass) {
                          $(element).removeClass('is-invalid');
                        }
                    }

                });
            
          
            $(document).on('click','#create', function(){
                $('#AddFeature').modal('show'); 
                $('.modal-title').html('Price Master');

            })
             $(document).on('click', '.edit-feature, .change-status', function(e){
            var fid = $(this).data('fid'); 
            var action = $(this).data('action');
            var status = $(this).data('status');
            var APP_URL = {!! json_encode(url('/')) !!};
            var url = (action == 'edit') ? APP_URL+'/price_edit' : APP_URL+'/price_delete'; 
            var statusvalue = (action != 'edit') ?  '&status='+status : '';
           if(action == 'edit') { 
            $.ajax({
               type:'POST',
               url: url,
               data:'_token = <?php echo csrf_token() ?>'+'&pid='+fid+statusvalue,
               success:function(data) {
                    var Objres = JSON.parse(data);
                        console.log(Objres)
                   if(action == 'edit') { 
                        if(Objres.status){
                            var res = Objres.data;
                            //console set + trigger change (players dropdown create aagum)
$('#console_id').val(res.console_id).trigger('change');

// hours set
$('#hours_id').val(res.hours_id);

//players dropdown ready aana apram old value set
setTimeout(function () {
    $('#no_of_players').val(res.no_of_players);
}, 150);

//  other fields
$('#price').val(res.price);
$('#tid').val(res.pid);

                         //   $('#console_id').val(res.console_id); 
                            $('#hours_id').val(res.hours_id); 
                         //   $('#no_of_players').val(res.no_of_players); 
                            $('#price').val(res.price); 
                            $('#tid').val(res.pid);
                            $('.modal-title').html(res.title);
                            $('.featuresubmit').html(res.action);
                            $('#AddFeature').modal('show'); 
                        }else{ 
                           swal("No Record Found");
                        }
                        
                    }else{ 
                      Swal.fire(Objres.message)
                        setTimeout(function(){ 
                          location.reload();
                        }, 1000);
                      
                      
                    }
                  
               }
            });
          }
          else
          {
             e.preventDefault();

             Swal.fire({
               title: "Are you sure??",
            text: "Do you want to Update this Status",
            icon: "warning",
            confirmButtonText: "Yes!",
            showDenyButton: true,
            denyButtonText: `Cancel`,

        }).then((result) => {

            if (result.isConfirmed) {
            $.ajax({
               type:'POST',
               url: url,
               data:'_token = <?php echo csrf_token() ?>'+'&pid='+fid+statusvalue,
               success:function(data) {
                    var Objres = JSON.parse(data);
                        console.log(Objres)
             
                      Swal.fire(Objres.message, '', 'success')
                        setTimeout(function(){ 
                          location.reload();
                        }, 1000);
                      
               }
            });
          }
          else if (result.isDenied) {
    Swal.fire('Changes are not saved', '', 'info')
  }
              });
          }
        })
            
        });

</script>
@endsection







