@extends('app')
@section('title','F&B Master')
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
    background-color: #00a65a;
}
.label-danger
{
      background-color: red;

}
.fa-thumbs-down {
    color: red;
}
.fa-thumbs-up {
    color: green;
}
.error
    {
        color: red;
    }  
#changeImage {
    background-color: #0d6efd;   
    border-color: #0d6efd;
    color: #fff;
}

#changeImage:hover {
    background-color: #0b5ed7;
    border-color: #0b5ed7;
    color: #fff;
}
</style>
@endsection
@section('content')

<!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h4> F&B Master</h4>
          </div>
          <div class="col-sm-6">
          <!--  <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">F&B Master</li>
              <li class="breadcrumb-item active"><a href="{{route("fandb_master")}}">F&B Master</a></li>
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
                                 <th align="left" valign="middle"> Name</th>
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
        <form id="saveTrade" action="{{route('fandb_save')}}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" id="tid" name="tid"  />
      <div class="modal-header">
      <h4 class="modal-title">F&B master</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
          <div class="form-group ">
              <label for="price" ><strong> Name<span class="error">*</span></strong> </label> 
              <input type="text" name="name" class="form-control" id="name">
              <label class="error"> @if($errors->any())
                {{ $errors->first('name') }}
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
      <div class="form-group">
    <label><strong>Food Image</strong></label>

    <!-- Preview Box -->
    <div id="imageBox"
         style="width:150px;height:150px;
                border:2px dashed #ccc;
                display:flex;
                align-items:center;
                justify-content:center;
                cursor:pointer;
                position:relative;">

        <img id="imagePreview"
             src=""
             style="max-width:100%;
                    max-height:100%;
                    display:none;">

        <span id="imagePlaceholder">Click to upload</span>
    </div>
    <!-- Buttons -->
    <div class="mt-2">
        <button type="button"
                class="btn btn-sm btn-info"
                id="changeImage">
            Change
        </button>

        <button type="button"
                class="btn btn-sm btn-danger"
                id="removeImage">
            Remove
        </button>
    </div>
    <!-- Hidden file input -->
    <input type="file"
           name="food_image"
           id="food_image"
           accept="image/*"
           style="display:none;">
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
                   'url':'{{route('master_ajax_fandb_list')}}',
                    "type": "GET",
                   'data': function(data){
                  
                    }
       },
             columns: [
                
                 { data: 'name' },
                 { data: 'price' },
                 { data: 'status' },
                 { data: 'action' },
             ],
              "columnDefs": [
                                                ]
         });
 $('#saveTrade').validate({ // initialize the plugin
                    rules: {
                      name: { 
                            required: true
                        } ,
                         price: { 
                            required: true
                        } 
                    },
                    messages: {
                      name: {
                        required: 'Name is Required'
                      },
                       price: {
                        required: 'Price is Required'
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
    $('.modal-title').html('F&B Master');
    // reset form
    $('#saveTrade')[0].reset();
    $('#tid').val('');
    // hide image preview
    $('#imagePreview').hide();
});
            $(document).on('click', '.edit-feature, .change-status', function(e){        
            var fid = $(this).data('fid'); 
            var action = $(this).data('action');
            var status = $(this).data('status');
            var APP_URL = {!! json_encode(url('/')) !!};
            var url = (action == 'edit') ? APP_URL+'/fandb_edit' : APP_URL+'/fandb_delete'; 
            var statusvalue = (action != 'edit') ?  '&status='+status : '';
           if(action == 'edit') { 
            $.ajax({
               type:'POST',
               url: url,
               data:'_token = <?php echo csrf_token() ?>'+'&pid='+fid+statusvalue,
               success:function(data) {
                    var Objres = JSON.parse(data);
                        console.log(Objres)
                  if(Objres.status){
    var res = Objres.data;
    $('#name').val(res.name);
    $('#price').val(res.price);
    $('#tid').val(res.pid);
    $('.modal-title').html(res.title);
    $('.featuresubmit').html(res.action);
    //  IMAGE PREVIEW FOR EDIT
   if (res.food_image) {
    $('#imagePreview')
        .attr('src', APP_URL + '/' + res.food_image)
        .show();
    $('#imagePlaceholder').hide(); 
} else {
    $('#imagePreview').hide();
    $('#imagePlaceholder').show();
}
    $('#AddFeature').modal('show');

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
       // OPEN FILE PICKER
$('#imageBox, #changeImage').on('click', function () {
    $('#food_image').click();
});

// PREVIEW IMAGE (ADD + CHANGE)
$('#food_image').on('change', function () {

    if (this.files && this.files[0]) {
        let reader = new FileReader();

        reader.onload = function (e) {
            $('#imagePreview')
                .attr('src', e.target.result)
                .show();

            $('#imagePlaceholder').hide();
        };

        reader.readAsDataURL(this.files[0]);
    }
});
// REMOVE IMAGE
$('#removeImage').on('click', function () {
    $('#food_image').val('');
    $('#imagePreview').hide().attr('src', '');
    $('#imagePlaceholder').show();
});    
        });
</script>
@endsection







