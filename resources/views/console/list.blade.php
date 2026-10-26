@extends('app')
@section('title','Console')
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
    .upload-box {
    width: 140px;
    height: 140px;
    border: 2px solid #aaa;
    border-radius: 8px;
    cursor: pointer;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background: #fafafa;
}

.upload-box img {
    display: none;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.upload-text {
    color: #777;
    font-size: 14px;
    font-weight: 600;
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
            <h4> Console</h4>
          </div>
          <div class="col-sm-6">
            {{-- <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">Master</li>
              <li class="breadcrumb-item active"><a href="{{route("console_master")}}">Console Master</a></li>
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
                                 <th align="left" valign="middle">Console Name</th>
                                 <th align="left" valign="middle">Limited Persons</th>
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
        <form id="saveTrade" action="{{route('console_save')}}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" id="tid" name="tid"  />
      <div class="modal-header">
      <h4 class="modal-title">Console</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
          <div class="form-group ">
              <label for="equipment" ><strong>Console Name<span class="error">*</span></strong> </label> 
              <input type="text" class="form-control custom-trade"  id="console_name" name="console_name" placeholder="" required="" minlength="1" maxlength="50">               
              <label class="error"> @if($errors->any())
                {{ $errors->first('equipment') }}
               @endif 
              </label>
          </div>  
           <div class="form-group ">
              <label for="equipment" ><strong>Limited Persons<span class="error">*</span></strong> </label> 
              <input type="text" class="form-control custom-trade"  id="limited_person" name="limited_person" placeholder="" required="" minlength="1" maxlength="50">               
              <label class="error"> @if($errors->any())
                {{ $errors->first('equipment') }}
               @endif 
              </label>
          </div>  
        <div class="form-group">
    <label><strong>Console Icon</strong></label>
    <input type="file" id="console_icon" name="console_icon" accept="image/*" hidden>

<div id="uploadBox" class="upload-box">
    <span class="upload-text">Upload Image</span>
    <img id="icon_preview" />
</div>
<button type="button"
        id="removeImage"
        class="btn btn-danger btn-sm mt-2"
        style="display:none;">
    Remove Image
</button>

<input type="hidden" name="remove_icon" id="remove_icon" value="0">

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
                   'url':'{{route('master_ajax_console_list')}}',
                    "type": "GET",
                   'data': function(data){
                  
                    }
       },
             columns: [
                
                 { data: 'console_name' },
                  { data: 'limited_person' }, 
                 { data: 'status' },
                 { data: 'action' },
             ],
              "columnDefs": [
                               { "orderable": false, "targets": "_all" }
                            ]
         });
 
 $('#AddFeature').on('hide.bs.modal', function () {
        document.activeElement.blur();
    });

 
 $('#saveTrade').validate({

    rules: {
        console_name: {
            required: true,
            minlength: 1,
            maxlength: 50
        },
        limited_person: {
            required: true,
            number: true,
            min: 1
        },
          console_icon: {
        extension: "jpg|jpeg|png|webp"
    }
    },

    messages: {
        console_name: {
            required: 'Console Name is Required',
            minlength: 'Minimum length 1 character',
            maxlength: 'Maximum length 50 characters'
        },
        limited_person: {
            required: 'Limited Persons is Required',
            number: 'Enter valid number',
            min: 'Minimum value is 1'
        },
        console_icon: {
        extension: "Only JPG, JPEG, PNG allowed"
    }
    },

    errorElement: 'span',
    errorPlacement: function (error, element) {
        error.addClass('invalid-feedback');
        element.closest('.form-group').append(error);
    },
    highlight: function (element) {
        $(element).addClass('is-invalid');
    },
    unhighlight: function (element) {
        $(element).removeClass('is-invalid');
    }
});
       $(document).on('click','#create', function(){

    $('#saveTrade')[0].reset();
    $('#tid').val('');

    $('#icon_preview').attr('src','').hide();
    $('.upload-text').show();
    $('#removeImage').hide();
    $('#remove_icon').val(0);

    $('.modal-title').html('Console');
    $('.featuresubmit').html('Save');

    $('#AddFeature').modal('show');
});

             $(document).on('click', '.edit-feature, .change-status', function(e){
            var fid = $(this).data('fid'); 
            var action = $(this).data('action');
            var status = $(this).data('status');
            var APP_URL = {!! json_encode(url('/')) !!};
            var url = (action == 'edit') ? APP_URL+'/console_edit' : APP_URL+'/console_delete'; 
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
                            $('#console_name').val(res.trade); 
                            $('#limited_person').val(res.limited_person);
                            $('#tid').val(res.pid);
                            $('.modal-title').html(res.title);
                            $('.featuresubmit').html(res.action);
                           // ===== IMAGE PREVIEW (EDIT TIME) =====
if (res.icon && res.icon !== '') {
    $('#icon_preview')
        .attr('src', APP_URL + '/' + res.icon)
        .show();

    $('.upload-text').hide();
    $('#removeImage').show();
} else {
    $('#icon_preview').hide();
    $('.upload-text').show();
    $('#removeImage').hide();
}

$('#remove_icon').val(0);

// finally open modal
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
       // click preview box → open file
$('#uploadBox').on('click', function () {
    $('#console_icon').click();
});

// show preview after select
$('#console_icon').on('change', function () {
    const file = this.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        $('#icon_preview')
            .attr('src', e.target.result)
            .show();

        $('.upload-text').hide(); // hide text after image
    };
    reader.readAsDataURL(file);
});

$('#removeImage').on('click', function () {
    $('#icon_preview').attr('src','').hide();
    $('.upload-text').show();
    $('#remove_icon').val(1);   // backend-ku signal
    $('#console_icon').val('');

    $(this).hide();
});

</script>
@endsection







