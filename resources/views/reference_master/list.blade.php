@extends('app')
@section('title','Reference')
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
    }table.dataTable thead .sorting {
    background-image: none !important;
    pointer-events: none;
}
</style>
@endsection
@section('content')

<!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h4> Reference</h4>
          </div>
          <div class="col-sm-6">
            {{-- <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">Reference</li>
              <li class="breadcrumb-item active"><a href="{{route("reference_master")}}">Reference Master</a></li>
            </ol> --}}
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
                                 <th align="left" valign="middle">Reference</th>
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
        <form id="saveTrade" action="{{route('reference_save')}}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" id="tid" name="tid"  />
      <div class="modal-header">
      <h4 class="modal-title">Reference</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
          <div class="form-group ">
              <label for="equipment" ><strong>Reference<span class="error">*</span></strong> </label> 
              <input type="text" class="form-control custom-trade"  id="name" name="name" placeholder="" required="" minlength="1" maxlength="50">               
              <label class="error"> @if($errors->any())
                {{ $errors->first('equipment') }}
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
var dataTable =$('#trade_table').DataTable({

             processing: true,
             serverSide: true,
             'ajax': {
                   'url':'{{route('master_ajax_reference_list')}}',
                    "type": "GET",
                   'data': function(data){
                  
                    }
       },
             columns: [
                
                 { data: 'name' },
                 { data: 'status' },
                 { data: 'action' },
             ],
              "columnDefs": [
                           { "orderable": false, "targets": "_all" }    
                            ]
         });
 

 
 $('#saveTrade').validate({ // initialize the plugin

                    rules: {
                        trade: { 
                            required: true,
                            minlength:1,
                            maxlength: 255,
                            
                        } 
                    },
                    messages: {

                      trade: {
                        required: 'Category is Required',
                        minlength:'Minimum Lenth 1 Character',
                        maxlength: 'Maximum Length 255 Character'
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
                $('.modal-title').html('Reference');

            })
             $(document).on('click', '.edit-feature, .change-status', function(e){
            var fid = $(this).data('fid'); 
            var action = $(this).data('action');
            var status = $(this).data('status');
            var APP_URL = {!! json_encode(url('/')) !!};
            var url = (action == 'edit') ? APP_URL+'/reference_edit' : APP_URL+'/reference_delete'; 
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
                            $('#name').val(res.name); 
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







