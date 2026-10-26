@extends('app')
@section('title','No of players')
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
</style>
@endsection
@section('content')

<!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h4>No of players</h4>
          </div>
          <div class="col-sm-6">
<!--            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">Master</li>
              <li class="breadcrumb-item active"><a href="{{route("players_list")}}">No of players Master</a></li>
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
                  
                  
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table  class="table table-striped" id="property_table">
                <thead>
                                <tr>
                                 <th align="left" valign="middle">S.No</th>
                                 <th align="left" valign="middle">No of players</th>
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
        <form id="savegst" action="{{route('Players_save')}}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" id="tid" name="tid"  />
      <div class="modal-header">
      <h4 class="modal-title">Edit GST</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
          <div class="row">
          <div class="form-group col-md-12">
              <label for="property_name" ><strong>No of players<span class="text-red">*</span></strong> </label> 
              <input type="number" class="form-control custom-property"  id="player_number" name="player_number" placeholder="" required="" minlength="1" maxlength="2">               
              <label class="error"> @if($errors->any())
                {{ $errors->first('gst') }}
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
  $(document).ready(function () {
 var dataTable =$('#property_table').DataTable({
             processing: true,
             serverSide: true,
               'ajax': {
                   'url':'{{route('master_ajax_noofplayers_list')}}',
                    "type": "GET",
                   'data': function(data){
                  
                    }
       },
             columns: [
                 { data: 'id' },
                 { data: 'player_number' },
                 { data: 'action' }
             ], 
             "columnDefs": [
                                { "orderable": false, "targets": [2] },
                                { "orderable": true, "targets": [0] }
                            ]
           
         });
  
 
 $('#savegst').validate({ // initialize the plugin

                    rules: {
                      player_number: { 
                            required: true,
                            minlength:1,
                            maxlength: 2,
                            
                        },
                    },
                    messages: {

                      player_number: {
                        required: 'No of Players is Required',
                        minlength:'Minimum Lenth 1 Character',
                        maxlength: 'Maximum Length 2 Character'
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
            
             $(document).on('click', '.edit-feature', function(e){
            var fid = $(this).data('fid'); 
            var APP_URL = {!! json_encode(url('/')) !!};
            var url =APP_URL+'/edit_players';
            $.ajax({
               type:'POST',
               url: url,
               data:'_token = <?php echo csrf_token() ?>'+'&pid='+fid,
               success:function(data) {
                    var Objres = JSON.parse(data);
                      e.preventDefault();
                        if(Objres.status){
                            var res = Objres.data;
                            console.log(res);
                            $('#player_number').val(res.player_number); 
                            $('#tid').val(res.pid);
                            $('.modal-title').html(res.title);
                            $('.featuresubmit').html(res.action);
                            $('#AddFeature').modal('show'); 
                        }else{ 
                           swal("No Record Found");
                        }
               }
            });
          
         
        })
            
        });

</script>
@endsection







