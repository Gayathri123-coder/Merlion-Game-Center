@extends('app')
@section('title','Game Play List')
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
      background-color: red;

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
 .status-dot {
    position: relative;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    display: inline-block;
}

/* 🟢 GREEN – Ongoing (Ripple Pulse) */
.status-green {
    background-color: #28a745;
}

.status-green::after {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: 50%;
    background: rgba(40, 167, 69, 0.6);
    animation: greenPulse 1.6s infinite;
}

/*  YELLOW – Upcoming (Soft Blink) */
.status-yellow {
    background-color: #ebb71c;
  /*  animation: yellowBlink 1.4s ease-in-out infinite;*/
}
.status-yellow::after {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: 50%;
    background: rgba(248, 184, 9, 0.973);
    animation: greenPulse 1.6s infinite;
}
/*  RED – Completed (No animation) */
.status-red {
    background-color: #dc3545;
}

/* ---------- Animations ---------- */

@keyframes greenPulse {
    0% {
        transform: scale(1);
        opacity: 0.7;
    }
    100% {
        transform: scale(2.4);
        opacity: 0;
    }
}

@keyframes yellowBlink {
   0% {
        transform: scale(1);
        opacity: 0.7;
    }
    100% {
        transform: scale(2.4);
        opacity: 0;
    }
}


</style>
@endsection
@section('content')
<!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h4>Game Play List</h4>
          </div>
          <div class="col-sm-6">
<!--            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">Master</li>
              <li class="breadcrumb-item active"><a href="{{route("submission_report_list")}}">Submission Report</a></li>
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
                <h3 class="card-title row w-100">
   <!-- ROW 1 -->
<div class="col-lg-3 mb-2">
    <input type="text" class="form-control" id="name" placeholder="Enter Customer name">
</div>

<div class="col-lg-3 mb-2">
    <input type="text" class="form-control" id="mobile_no" placeholder="Enter Mobile Number">
</div>

<div class="col-lg-3 mb-2">
    <select class="form-control" id="console_id">
        <option value="">All Console</option>
        @foreach($console_list as $val)
            <option value="{{ $val->id }}">{{ $val->console_name }}</option>
        @endforeach
    </select>
</div>

<div class="col-lg-2 mb-2">
    <button class="btn btn-danger w-80 h-100" id="allBranchesBtn">
        All Branches
    </button>
</div>

          <div class="col-lg-3 mt-1">
              <div class="input-group date" id="start_date" data-target-input="nearest">
                                              <input type="text" class="form-control datetimepicker-input custom_work_start" data-target="#start_date" id="work_start" name="start_date"  />
                                            <div class="input-group-append" data-target="#start_date" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div> 
          </div>
         <div class="col-lg-3 mt-1">
                     <div class="input-group date" id="delivery_date" data-target-input="nearest">
                                              <input type="text" class="form-control datetimepicker-input custom_work_end" data-target="#delivery_date" id="work_end" name="end_date" />
                                            <div class="input-group-append" data-target="#delivery_date" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                  </div>
                <!--  <div class="col-lg-4">
                    <select class="form-control col-lg-12" id="no_of_players">
                      <option value="" >All Player</option>
                      @if(count($no_of_players_list)>0)
                      @foreach($no_of_players_list as $ky=>$val)
                      <option value="{{$val}}" >{{$val}}</option>
                      @endforeach
                      @endif
                  </select>
                  </div>
                <br> <br>  <br> 
                 <div class="col-lg-4">
                    <select class="form-control col-lg-12" id="reference_id">
                      <option value="" >All Reference</option>
                      @if(count($reference_list)>0)
                      @foreach($reference_list as $ky=>$val)
                      <option value="{{$val->id}}" >{{$val->name}}</option>
                      @endforeach
                      @endif
                  </select>
                  </div>
                  <br> <br><br> -->
               

                 <div class="col-lg-3 mt-1">
                      <button class="btn btn-info refresh" style="margin-left:60px"><i class="fa fa-refresh"></i> Clear</button>
                  </div>
              </h3>

                  <div class="card-tools">
                      <a class="btn btn-info btn-md" style="margin-right:40px" href="{{route("submission_add")}}"><i class="fa fa-plus"></i>&nbsp;&nbsp;Create</a>
                  </div>
                  
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table  class="table table-striped" id="trade_table">
                <thead>
                                <tr>
                                 <th align="left" valign="middle">No of Players</th>
                                   <th align="left" valign="middle">Player Name</th>
                                 <th align="left" valign="middle">Console Name</th>
                                 <th align="left" valign="middle">Hours</th>
                                 <th align="left" valign="middle">Date</th>
                                 <th align="left" valign="middle">Start Time</th>
                                 <th align="left" valign="middle">End Time</th>
                                 <th align="left" valign="middle">Total Hours</th>
                                 <th align="left" valign="middle">Console Cost</th>
                                 <th align="left" valign="middle">Discount</th>
                                 <th align="left" valign="middle">F&B Cost</th>
                                 <th align="left" valign="middle">Final Cost</th>
                                 <th align="left" valign="middle">Payment Type</th>
<!--                                 <th align="left" valign="middle">Reference</th>-->
                                     <th align="left" valign="middle">Status</th>

                                 <th align="left" valign="middle">Action</th>
                                </tr>
                            </thead>
                             <tfoot>
                            <tr>
                                
                                 <th align="left" valign="middle"></th>
                                   <th align="left" valign="middle"></th>
                                 <th align="left" valign="middle"></th>
                                 <th  valign="middle" id="hours"></th>
                                 <th align="left" valign="middle"></th>
                                 <th align="left" valign="middle"></th>
                                 <th align="left" valign="middle"></th>
                                 <th align="left"  id="total_hours" valign="middle">Total Hours</th>
                                 <th id="total_cost" >Total Cost</th>
                                 <th id="discount">Discount</th>
                                 <th align="left" valign="middle" id="fandb_cost">F&B Cost</th>
                                 <th id="final_cost">Final Cost</th>
                                 <th align="left" valign="middle"></th>
<!--                                 <th align="left" valign="middle">Reference</th>-->
                                 <th align="left" valign="middle"></th>
                                
                             <th align="left" valign="middle"></th>
                                
                                </tr>
    </tfoot>
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
  
  $(document).ready(function () {
       let allBranches = false;
     $('#start_date').datetimepicker({
                                  format: 'DD-MM-YYYY',
                                   defaultDate: new Date()
                               });
   $('#delivery_date').datetimepicker({
                                  format: 'DD-MM-YYYY',
                                   defaultDate: new Date()
                               });

var viewdatatab =$('#trade_table').DataTable({
          processing: true,
            serverSide: true,
            'responsive': true,
              dom: 'Blfrtip',
                 pageLength: 50, 
            "bLengthChange" : false, //thought this line could hide the LengthMenu
            "bInfo":false, 
            "lengthMenu": [ [10,25, 50, 100, 500,1000,'-1'], [10,25, 50, 100, 500,1000,'All'] ] ,
             buttons: [
                'csvHtml5',
                 'pageLength',
            ],
             'ajax': {
                   'url':'{{route('ajax_submission_report_list')}}',
                    "type": "GET",
                   'data': function(data){
                    data.name = $("#name").val();
                    data.mobile_no = $("#mobile_no").val();
                    data.console_id = $("#console_id").val();
                    data.start_time = $('#work_start').val();
                    data.end_time = $('#work_end').val();
                    data.no_of_players = $("#no_of_players").val();
                    data.reference_id = $("#reference_id").val();
                     data.allBranches = allBranches ? 1 : 0;
                    }, 
             
                    
       },
             columns: [
                 { data: 'no_of_players' },
                 { data: 'player_name' },
                 { data: 'console_name' },
                 { data: 'hours' },
                 { data: 'date' },
                 { data: 'start_time' },
                 { data: 'end_time' },
                 { data: 'total_hours' },
                 { data: 'total_cost' },
                 { data: 'discount' },
                 { data: 'fandb_cost' },
                 { data: 'final_cost' },
                 { data: 'payment_type' },
//                 { data: 'reference_id' },
{
    data: null,
    render: function (data, type, row) {

        if (!row.date || !row.start_time || !row.end_time) return '-';

        let now = new Date();

        // ---- Parse booking DATE (dd-mm-yyyy) ----
        let dateParts = row.date.split('-'); // 14-09-2026
        let bookingDate = new Date(
            dateParts[2],            // year
            dateParts[1] - 1,        // month
            dateParts[0],            // day
            0, 0, 0, 0
        );

        let today = new Date();
        today.setHours(0,0,0,0);

        // ---- If date is past ----
        if (bookingDate < today) {
            return `<span class="status-dot status-red" title="Completed"></span>`;
        }

        // ---- If date is future ----
        if (bookingDate > today) {
            return `<span class="status-dot status-yellow" title="Upcoming"></span>`;
        }

        // ---- Same day: check TIME ----
        function parseTime(timeStr) {
            let d = new Date();
            let parts = timeStr.match(/(\d+):(\d+)\s?(AM|PM)/i);
            if (!parts) return null;

            let h = parseInt(parts[1]);
            let m = parseInt(parts[2]);
            let ap = parts[3].toUpperCase();

            if (ap === 'PM' && h < 12) h += 12;
            if (ap === 'AM' && h === 12) h = 0;

            d.setHours(h, m, 0, 0);
            return d;
        }

        let start = parseTime(row.start_time);
        let end   = parseTime(row.end_time);

        if (!start || !end) return '-';

        //  Upcoming (today but before start)
        if (now < start) {
            return `<span class="status-dot status-yellow" title="Upcoming"></span>`;
        }

        //  Ongoing
        if (now >= start && now <= end) {
            return `<span class="status-dot status-green" title="Ongoing"></span>`;
        }

        //  Completed
        return `<span class="status-dot status-red" title="Completed"></span>`;
    }
},

                 {data: 'action'}
             ],
          
              "columnDefs": [
                              
                            ],
                            
         });
         $('#allBranchesBtn').on('click', function () {
    allBranches = !allBranches;

    if (allBranches) {
        $(this).addClass('btn-success').removeClass('btn-danger');
    } else {
        $(this).addClass('btn-danger').removeClass('btn-success');
    }

    viewdatatab.ajax.reload();
});

   $("#start_date").on("change.datetimepicker",function (e) {
       viewdatatab.ajax.reload();
 });
 $("#delivery_date").on("change.datetimepicker",function (e) {
       viewdatatab.ajax.reload();
 });
    $('#reference_id').on('change', function () {
    viewdatatab.ajax.reload();
    });
    $('#no_of_players').on('change', function () {
    viewdatatab.ajax.reload();
    });
    $("#end_time").on("change.datetimepicker", () => {    
      viewdatatab.ajax.reload();
    });
    $("#start_time").on("change.datetimepicker", () => { 
      viewdatatab.ajax.reload();
    });
    $('#console_id').on('change', function () {
    viewdatatab.ajax.reload();
    });
    $('#name').on('keyup', function () {
    viewdatatab.ajax.reload();
    });
    $('#mobile_no').on('keyup', function () {
    viewdatatab.ajax.reload();
    });
    $('.refresh').on('click', function () {
    $('#reference_id').val("").trigger('change');
    $('#no_of_players').val("").trigger('change');
    $('#console_id').val("").trigger('change');
    $('#start_time').datetimepicker('date', null); // Reset datetimepicker
    $('#end_time').datetimepicker('date', null); // Reset datetimepicker
    $('#name').val("");
    $('#mobile_no').val("");
    viewdatatab.ajax.reload();
    });
    
            $('#trade_table').on('xhr.dt', function(e, settings, json, xhr) {
    console.log('DataTable AJAX response:', json); // Log full response
    if (json.aaData && json.aaData.length > 0) {
          // Update footer values dynamically
    $('#trade_table tfoot th#hours').html(json.totalHourss);
    $('#trade_table tfoot th#total_hours').html(json.totalHours);
    $('#trade_table tfoot th#total_cost').html(json.totalCostSum);
    $('#trade_table tfoot th#discount').html(json.discount);
    $('#trade_table tfoot th#final_cost').html(json.final_cost);
    $('#trade_table tfoot th#fandb_cost').html(json.fandb_cost);

    // If you need to perform additional actions with the response, do it here
    }else{
          // Update footer values dynamically
    $('#trade_table tfoot th#hours').html("");
    $('#trade_table tfoot th#total_hours').html("");
    $('#trade_table tfoot th#total_cost').html("");
    $('#trade_table tfoot th#discount').html("");
    $('#trade_table tfoot th#final_cost').html("");
    $('#trade_table tfoot th#fandb_cost').html("");
    // If you need to perform additional actions with the response, do it here
        
    }
  
});

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
            text: "Do you want to delete this record",
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







