@extends('app')
@section('title','Submission')
@section('style')
<style>
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
            <h4>Submission</h4>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">Master</li>
              <li class="breadcrumb-item active"><a href="{{route("submission_list")}}">Submission List</a></li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
           @include('layouts/alert')


            <div class="card">
              <div class="card-header">
                   <h3 class="card-title  col-lg-9 row">
                        
                  </h3>
                  <div class="card-tools">
                      <a class="btn btn-info btn-md" href="{{route("submission_add")}}"><i class="fa fa-plus"></i>&nbsp;&nbsp;Create</a>
                  </div>
                  
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table  class="table table-striped user_table">
                <thead>
                                <tr>
                                    <th align="left" valign="middle" style="width:10%;">S.No.</th>
                                    <th align="left" valign="middle"  style="width:10%;">Date</th>
                                    <th align="left" valign="middle" style="width:20%;">Console Name</th>
                                    <th align="left" valign="middle" style="width:20%;">Hours</th>
                                    <th align="left" valign="middle" style="width:20%;">No of Players</th>
                                    <th align="left" valign="middle" style="width:20%;">Status</th>
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

@endsection
@section('script')
                    <script>
                        $('.select2bs4').select2({
                            theme: 'bootstrap4',
                        });




                        var viewdatatab = $('.user_table').DataTable({
                            processing: true,
                            serverSide: true,
                            'ajax': {
                                'url': '{{route('ajax_submission_list')}}',
                                "type": "GET",
                                'data': function (data) {
                                  
                                }
                            },
                            columns: [
                                {data: 'Pkey'},
                                {data: 'date'},
                                {data: 'console_name'},
                                {data: 'hours'},
                                {data: 'no_of_players'},
                                {data: 'status'},
                                {data: 'action'},
                            ],
                            "columnDefs": [
                                
                            ]
                        });
                        $(document).on('click', '.edit-feature, .change-status', function(e){
            var fid = $(this).data('fid'); 
            var status = $(this).data('status');
            var APP_URL = {!! json_encode(url('/')) !!};
            var url = APP_URL+'/submission_delete'; 
            var statusvalue = '&status='+status;
            
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
              
        })
                        

                    </script>
                    @endsection









