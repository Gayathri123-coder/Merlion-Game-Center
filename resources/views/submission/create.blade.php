@extends('app')
@section('title','Submission')
@section('styles')

@endsection
{{-- Existing mobile → name data --}}
<script>
    window.MOBILE_NAME_MAP = @json(
        \App\Submissiondata::select('filter_mobile_no','filter_name')
            ->orderBy('id','desc')
            ->get()
    );
</script>

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4>Add Submission </h4>
            </div>
            <div class="col-sm-6">
              <!--  <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Submission</li>
                    <li class="breadcrumb-item"><a href="{{route("submission_list")}}">List</a></li>
                    <li class="breadcrumb-item active"><a href="{{route("submission_add")}}"> Add New</a></li>
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
@if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>Whoops!</strong> There were some problems with your input.<br><br>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"></h3>
                        <div class="card-tools">
                            <a class="btn btn-info btn-md" href="{{route("submission_report_list")}}"><i class="fa fa-backward"></i>&nbsp;&nbsp;Back</a>
                        </div>

                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">

                         <form method="POST" id="new_user" name="new_user" action="{{route('submission_save')}}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                     
                                       <div class="form-group">
                                        <label for="">Console name<span class="error">*</span></label>
                                       <select class="form-control common_select2"
        name="console_id"
        id="console_id">

    <option value="">Select Console Name</option>

    @foreach($console_list as $value)
        <option value="{{ $value->id }}"
                data-limit="{{ $value->limited_person }}">
            {{ $value->console_name }}
        </option>
    @endforeach
</select>

                                        </div>
                                    <div class="form-group">
                                        <label for="">Hours <span class="error">*</span></label>
                                        <select class="form-control common_select2" name="hours_id" id="hours_id">
                                          <option value="">Select Hours</option>
                                          <?php foreach($hours_list as $key=>$value){?>
                                          <option value="{{$value->id}}" data-id="{{$value->hours}}" <?php echo (old('hours_id')==$value->hours)?"selected":""?>>{{$value->hours}}</option>
                                          <?php }?>
                                      </select>
                                    </div>
                                   
                                    <div class="form-group ">
                                      <label for="equipment" ><strong>Start Time<span class="error">*</span></strong> </label> 
                                      <div class="input-group date" id="start_time" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input" data-target="#start_time" id="start_time" name="start_time"  value="{{old('start_time')}}"/>
                                        <div class="input-group-append" data-target="#start_time" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-clock"></i></div>
                                        </div>
                                    </div>
                                    
                                  </div> 
                                  <div class="form-group">
                                    <label for="">Total Hours <span class="error">*</span></label>
                                       <input type="text"  id="total_hours" name="total_hours" class="form-control total_hours">
                                  </div> 
                                  <div class="form-group">
                                    <label for="">Discount <span class="error"></span></label>
                                       <input type="text" name="discount" class="form-control discount" value="0.00">
                                  </div> 
                                 <div class="form-group">
    <label>Payment Type <span class="error">*</span></label>
    <select class="form-control" name="payment_type" id="payment_type">
        <option value="">Select Payment Type</option>
        <?php foreach($payment_type_list as $value){ ?>
            <option value="{{ $value->id }}">
                {{ $value->payment_type }}
            </option>
        <?php } ?>
    </select>
</div>
<div class="form-group" id="split_toggle_box" style="display:none;">
    <label>
        <input type="checkbox" id="enable_split">
        Split payment (Cash + UPI)
    </label>
</div>

<div id="split_payment_box" style="display:none; border:1px solid #ddd; padding:10px; border-radius:6px;">
    <div class="form-group">
        <label>Cash Amount</label>
        <input type="number" class="form-control" name="cash_amount" id="cash_amount" value="0">
    </div>

    <div class="form-group">
        <label>UPI Amount</label>
        <input type="number" class="form-control" name="upi_amount" id="upi_amount" value="0">
    </div>

    <small class="text-muted">
        Cash + UPI = Final Cost
    </small>
</div>
                                    <div class="form-group">
    <label for="customer_place">Customer Place <span class="text-danger">*</span>
</label>
    <input type="text"
           class="form-control"
           name="customer_place"
           id="customer_place"
           placeholder="Enter customer place"
           value="{{ old('customer_place') }}">
</div>

                                  <div class="form-group">
                                    <table class="table table-striped sorstardynamic">
                                      <thead>
                                          <tr>
                                              <th style="width:30%;text-align:center">Player</th>
                                              <th style="width:30%;text-align:center">Name</th>
                                              <th style="text-align:center">Mobile No.</th>
                                          </tr>
                                      </thead>
                                      <tbody>
                                           <?php
                                          if ($sor_settings && $sor_settings->settings) {
                                              $settings = $sor_settings->settings ? unserialize($sor_settings->settings) : [];
                                              foreach ($settings as $key => $set) {
                                                  ?>
                                                  <tr class="calrow_<?php echo $key; ?>">
                                                      <td><input type="number" max="0" step="any" value="<?php echo $set['sor'] ?>" class="form-control sor_<?php echo $key; ?>" name="calcpercent[<?php echo $key; ?>][sor]" style="text-align:center"/></td>
                                                      <td><input type="number" min="0" step="any"  value="<?php echo $set['star'] ?>" class="form-control star_<?php echo $key; ?>" name="calcpercent[<?php echo $key; ?>][star]" style="text-align:center"/></td>
                                                      <td><input type="text" readonly="" value="<?php echo $set['effective'] ?>" class="form-control effective_<?php echo $key; ?> highlighttext" name="calcpercent[<?php echo $key; ?>][effective]" style="text-align:center"/></td>
                                                  </tr>
                                                  <?php
                                              }
                                              ?> 
                                          <?php } ?>                                               
                                      </tbody>
                                  </table>
                                  </div>
                                    </div>
                                    <div class="col-md-6">                                 
                                       
                                         <div class="form-group">
                                        <label for="">No of players <span class="error">*</span></label>
                                       <select class="form-control common_select2"
        name="no_of_players"
        id="no_of_players">
    <option value="">Select No of players</option>
</select>
                                   
                                     </div>
                                        <div class="form-group">
                                          <!--<label for="">Start Date</label>-->
                                          <!--<input type="text" class="form-control datepicker" id="" placeholder="Please Select Date" >-->
                                          <label>Date <span class="error">*</span></label>
                                          <div class="input-group date" id="start_date" data-target-input="nearest">
                                              <input type="text" class="form-control datetimepicker-input" data-target="#start_date" id="date" name="date"  value="{{old('date')}}"/>
                                              <div class="input-group-append" data-target="#start_date" data-toggle="datetimepicker">
                                                  <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                              </div>
                                          </div>
                                      </div>
                                      
                                      <div class="form-group ">
                                        <label for="equipment" ><strong>End Time<span class="error">*</span></strong> </label> 
                                        <div class="input-group date" id="end_time" data-target-input="nearest">
                                          <input type="text" class="form-control datetimepicker-input" data-target="#end_time" id="end_time" name="end_time"  value="{{old('end_time')}}"/>
                                          <div class="input-group-append" data-target="#end_time" data-toggle="datetimepicker">
                                              <div class="input-group-text"><i class="fa fa-clock"></i></div>
                                          </div>
                                      </div>
                                      
                                    </div> 
                                    <div class="form-group">
                                      <label for="">Console Cost <span class="error">*</span></label>
                                         <input type="text" name="total_cost" class="form-control total_cost">
                                    </div>
                                        <div class="form-group">
                                      <label for="">F&B Cost <span class="error"></span></label>
                                         <input type="text" name="fandb_cost" class="form-control fandb_cost">
                                    </div>
                                    <div class="form-group">
                                      <label for="">Final Cost <span class="error">*</span></label>
                                         <input type="text" name="final_cost" class="form-control final_cost">
                                    </div>
                                    <div class="form-group">
                                      <label for="">Reference</label>
                                      <select class="form-control " name="reference_id" id="reference_id">
                                        <option value="">Select Reference</option>
                                        <?php foreach($reference_list as $key=>$value){?>
                                        <option value="{{$value->id}}" <?php echo (old('reference_id')==$value->id)?"selected":""?>>{{$value->name}}</option>
                                        <?php }?>
                                    </select> 
                                      </div>
                                         <div class="form-group">
                                             <table id="myTable" class="table table-striped">
                                                <thead>
                                                  <tr>
                                                    <th>F&B</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                    <th>Action</th>
                                                  </tr>
                                                </thead>
                                                <tbody>
                                                  <tr class="data-row">
                                                    <td>
                                                  <select class="form-control fandb-select" name="name[]">

                                                        <option value="">Select F&B</option>  
                                                        @if(count($FandBMaster)>0)
                                                        @foreach($FandBMaster as $ky=>$val)
                                                        <option data-value="{{$val->price}}" value="{{$val->id}}">{{$val->name}}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                    </td>
                                                    <td><input class="form-control" type="number" id="qty" name="qty[]" value="1" /></td>
                                                    <td><input class="form-control" type="text" name="price[]"  readonly /></td>
                                                    <td><span class="add-row" style="cursor: pointer;">➕</span>  
</td>
                                                  </tr>
                                                </tbody>
                                              </table>
                                         </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
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
    window.CURRENT_PLACE_ID = {{ session('place_id') ?? 'null' }};
</script>
<script>
$(document).on('change', '#console_id', function () {

    let limit = $('#console_id option:selected').data('limit');
    let $players = $('#no_of_players');

    // clear dropdown
    $players.empty();
    $players.append('<option value="">Select No of players</option>');

    // clear price (important)
    $('.total_cost').val('');
    $('.final_cost').val('');

    if (!limit) return;

    // create options based on limit
    for (let i = 1; i <= limit; i++) {
        $players.append('<option value="'+i+'">'+i+'</option>');
    }
});
</script>

<script>   
    function updateTotalPrice() {
     let total = 0;
     let discount = parseFloat($('.discount').val()) || 0;
     let total_cost = parseFloat($('.total_cost').val()) || 0;
    $('input[name="price[]"]').each(function () {
        let val = parseFloat($(this).val()) || 0;
        total += val;
    });
    $('.fandb_cost').val(total.toFixed(2));
    let fandb_cost = total;
    let final_cost = (total_cost + fandb_cost) - discount;
    $('.final_cost').val(final_cost.toFixed(2));
}

   $(document).on('change', 'select[name="name[]"]', function () {
    let $row = $(this).closest('tr');
    let unitPrice = $(this).find('option:selected').data('value') || 0;
    let qty = parseFloat($row.find('input[name="qty[]"]').val()) || 1;
    let totalPrice = qty * unitPrice;
    $row.find('input[name="price[]"]').val(totalPrice.toFixed(2));
    updateTotalPrice();

});

$(document).on('input', 'input[name="qty[]"]', function () {
    let $row = $(this).closest('tr');
    let qty = parseFloat($(this).val()) || 1;
    let unitPrice = $row.find('select[name="name[]"] option:selected').data('value') || 0;
    let totalPrice = qty * unitPrice;
    $row.find('input[name="price[]"]').val(totalPrice.toFixed(2));
    updateTotalPrice();

});
     $(document).on('change', '#hours_id', function(e){
  
    let selectedOption = $('#hours_id option:selected'); // Get selected option
    let timeValue = selectedOption.data('id'); // Get the "01:30" value

    // Extract hours and minutes
    let [hours, minutes] = timeValue ? timeValue.split(':').map(num => parseInt(num, 10)) : [0, 0];

    let updatedTime = moment().add(hours, 'hours').add(minutes, 'minutes'); // Add extracted hours & minutes

    // Update the datetimepicker's time dynamically
    $('#end_time').datetimepicker('date', updatedTime);

    var no_of_players = $('#no_of_players').val();
    var console_id = $('#console_id').val();

});

 $(document).on('click', '.add-row', function () {
    let $clone = $(this).closest('tr').clone();
    
     // Clear select and inputs
    $clone.find('select').val('');
    $clone.find('input[name="qty[]"]').val('1');      // Set qty to 1
    $clone.find('input[name="price[]"]').val('');     // Clear price
    
    $('#myTable tbody').append($clone);
  });
  
 $(document).on('change', '#hours_id, #no_of_players, #console_id', function(e) {  
            var hours_id = $('#hours_id').val(); 
            var no_of_players = $('#no_of_players').val(); 
            var console_id = $('#console_id').val(); 
            var APP_URL = {!! json_encode(url('/')) !!};
            var url = APP_URL+'/getpriceinfo';  
           $.ajax({
    type: 'POST',
    url: "{{ route('getpriceinfo') }}",
    data: {
        _token: "{{ csrf_token() }}",
        place_id: window.CURRENT_PLACE_ID,  
        hours_id: hours_id,
        no_of_players: no_of_players,
        console_id: console_id
    },
    success: function (data) {

        let total_Cost = parseFloat(data.total_cost) || 0;
        let fandb_cost = parseFloat($('.fandb_cost').val()) || 0;
        let discount   = parseFloat($('.discount').val()) || 0;

        let final_cost = (total_Cost + fandb_cost) - discount;

        $('.total_cost').val(total_Cost.toFixed(2));
        $('.final_cost').val(final_cost.toFixed(2));
    }
});        
       })

       $(document).on('keyup', '.discount,.total_cost', function(e) {  
             let discount = parseFloat($('.discount').val()) || 0;
             let fandb_cost = parseFloat($('.fandb_cost').val()) || 0;
             let total_cost = parseFloat($('.total_cost').val()) || 0;
             let final_cost = (total_cost + fandb_cost) - discount;
             $('.final_cost').val(final_cost.toFixed(2));           
       })     
$(document).on('click', '.remove-row', function () {
    // Only remove if more than 1 row remains
    if ($('#myTable tbody tr').length > 1) {
        $(this).closest('tr').remove();
        updateTotalPrice(); // Recalculate after removal
    } else {
        alert('At least one row is required.');
    }
});
 $('#start_time').datetimepicker({
        format: 'LT',  // 24-hour format (Use 'LT' for AM/PM)
        useCurrent: true, // Prevents default selection
        ignoreReadonly: true,
        allowInputToggle: true,
        defaultDate: moment(),
        icons: {
            up: "fa fa-chevron-up",
            down: "fa fa-chevron-down"
        }
    });
    $('#end_time').datetimepicker({
        format: 'LT',  // 'LT' for localized AM/PM format
       // stepping: 30,  // 30-minute intervals
        useCurrent: false, // Prevents it from selecting the same as start_time
       // defaultDate: oneHourLater, // Sets default time 1 hour ahead
        ignoreReadonly: true,
        allowInputToggle: true,
        icons: {
            up: "fa fa-chevron-up",
            down: "fa fa-chevron-down"
        }
    });
    $("#start_time").on("change.datetimepicker", () => { 
              
      calculateTimeDifference();
     
  })
  $("#end_time").on("change.datetimepicker", () => {    
    calculateTimeDifference();
    })
  function calculateTimeDifference() {
  
       let startTime = $('#start_time').datetimepicker('date'); 
       let endTime = $('#end_time').datetimepicker('date');  

        if (startTime && endTime) {
            let duration = moment.duration(endTime.diff(startTime));
            let hours = String(duration.hours()).padStart(2, '0');   // Ensure two-digit hours
            let minutes = String(duration.minutes()).padStart(2, '0'); // Ensure two-digit minutes
            let total_hrs = hours+':'+minutes;
            console.log(total_hrs,'dsd');
            $('#total_hours').val(total_hrs);
        }
       
    }
    function updateEndTime() {
        let selectedOption = $('#hours_id option:selected'); // Get selected option
        let timeValue = selectedOption.data('id'); // Get the "01:30" value

        // Extract hours and minutes
        let [hours, minutes] = timeValue ? timeValue.split(':').map(num => parseInt(num, 10)) : [0, 0];

        // Get selected start time
        let startTime = $('#start_time').data('DateTimePicker').date(); // Get start time

        if (startTime) {
            let updatedTime = moment(startTime).add(hours, 'hours').add(minutes, 'minutes'); // Add extracted hours & minutes

            // Update the end time picker
            $('#end_time').datetimepicker('date', updatedTime);

            console.log("Updated End Time:", updatedTime.format('LT'));
        }
    }
    
    $('#start_date').datetimepicker({
        format: 'DD-MM-YYYY',
        useCurrent: true,  // Ensure it defaults to the current date
        defaultDate: moment() // Set the default date to today
    });

    $(document).on('keyup change', '#no_of_players', function (event) {
        var tlen = $('#no_of_players').val();
        tlen = parseInt(tlen);
        

        for (var i = 0; i < tlen; i++) {

            if ($('.sorstardynamic tbody tr.calrow_' + i).length > 0) {
                var sor = " ";
                var star = " ";
                var Yrset = " ";

                $('.sorstardynamic tbody tr.calrow_' + i).replaceWith('<tr class="calrow_' + i + '"><td><input type="text"  readonly="" value="Player - ' + (i+1) + '" class="form-control sor_' + i + '" name="calcpercent[' + i + '][player]" style="text-align:center" /></td><td><input type="text" value="' + star + '" class="form-control star_' + i + '" name="calcpercent[' + i + '][name]" style="text-align:center" /></td><td><input type="text"  value="' + Yrset + '" class="form-control effective_' + i + ' highlighttext" name="calcpercent[' + i + '][mobile_no]" style="text-align:center" /></td></tr>');
            } else {
                var sor = " ";
                var star = " ";
                var Yrset = " ";
                $('.sorstardynamic tbody').append('<tr class="calrow_' + i + '"><td><input type="text" value="Player - ' + (i+1) + '" class="form-control sor_' + i + '" name="calcpercent[' + i + '][player]" style="text-align:center" /></td><td><input type="text" value="' + star + '" class="form-control star_' + i + '" name="calcpercent[' + i + '][name]" style="text-align:center" /></td><td><input type="text"  value="' + Yrset + '" class="form-control effective_' + i + ' highlighttext" name="calcpercent[' + i + '][mobile_no]" style="text-align:center"/></td></tr>');
            }
            if ($('.sorstardynamic tbody tr').length > tlen) {
                var rlen = $('.sorstardynamic tbody tr').length;
                for (var i = tlen; i < rlen; i++) {
                    $('.sorstardynamic tbody tr.calrow_' + i).remove()
                }
            }
        }
        $('#no_of_players').val(tlen)
    })

    $(function () {
  $('#new_user').validate({
    rules: {
      no_of_players: {
        required: true
      },
     
      console_id: {
        required: true
      },
      hours_id:{
        required: true
      },
      date:{
        required: true
      },
      start_time:{
        required: true
      },
      end_time:{
        required: true
      },
     
      total_hours:{
          required: true,
      },
      total_cost:{
        required: true,
      },
      discount:{
        required: true,
      },
      final_cost:{
        required: true,
      },
      payment_type:{
        required: true,
      },
      customer_place: {
    required: true
},

    /*  reference_id:{
        required: true,
      },
      calcpercent:{
        required: true,
      }
    */
    },
    messages: {
      no_of_players : {
        required: "Please Select No of players"
     
      },
      console_id: {
        required: "Please Select Console",
      },
      hours_id:{
        required: "Please Select Hours."
      },
      date:{
        required: "Please Select Date"
      },
      start_time:{
        required: "Please Select Start time"
      },
      end_time:{
          required: "Please select End Time",
      },
      total_hours:
      {
        required: "Please enter Total hours",
      },
      total_cost:{
        required: "Please enter Total Cost",
      },
      discount:{
        required: "Please enter discount",
      },
      final_cost:{
        required: "Please enter Final Cost",
      },
      payment_type:{
        required: "Please select Payment Type",
      },
      customer_place: {
    required: "Please enter Customer Place"
},
/*
      reference_id:{
        required: "Please select Reference",
      },*/
      calcpercent:{
        required:  "Please Enter Player Informations",
      }
     
    },
    errorElement: 'span',
   errorPlacement: function (error, element) {
    error.addClass('invalid-feedback');

    if (element.parent('.input-group').length) {
        error.insertAfter(element.parent());
    } else {
        error.insertAfter(element);
    }
},

    highlight: function (element, errorClass, validClass) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass('is-invalid');
    },
    submitHandler: function (form) {
      form.submit();
    }
  });
});
</script>
<!-- SWEETALERT SECTION ADDED BELOW -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    @if(session('sweet_error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: {!! json_encode(session('sweet_error')) !!},
            confirmButtonColor: '#d33',
            confirmButtonText: 'OK'
        });
    @endif

    @if(session('sweet_success') || session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: {!! json_encode(session('sweet_success') ?? session('success')) !!},
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
    @endif
});
</script>
<script>
$(document).ready(function () {

    $('#payment_type').on('change', function () {

        let paymentTypeId = $(this).val(); 

        if (paymentTypeId == '3') {  
            $('#split_toggle_box').slideDown();
        } else {
            $('#split_toggle_box').slideUp();
            $('#split_payment_box').slideUp();
            $('#enable_split').prop('checked', false);
            $('#cash_amount').val(0);
            $('#upi_amount').val(0);
        }
    });

    // checkbox click pannina
    $('#enable_split').on('change', function () {
        if ($(this).is(':checked')) {
            $('#split_payment_box').slideDown();
        } else {
            $('#split_payment_box').slideUp();
            $('#cash_amount').val(0);
            $('#upi_amount').val(0);
        }
    });

});
</script>
<script>
$(document).ready(function () {

    function updateSplit(changed) {

        let finalCost = parseFloat($('.final_cost').val()) || 0;
        let cash = parseFloat($('#cash_amount').val()) || 0;
        let upi  = parseFloat($('#upi_amount').val()) || 0;

        if (finalCost <= 0) return;

        if (changed === 'cash') {
            let balance = finalCost - cash;
            $('#upi_amount').val(balance >= 0 ? balance.toFixed(2) : 0);
        }

        if (changed === 'upi') {
            let balance = finalCost - upi;
            $('#cash_amount').val(balance >= 0 ? balance.toFixed(2) : 0);
        }
    }

    // Cash type pannina → UPI auto update
    $('#cash_amount').on('input', function () {
        updateSplit('cash');
    });

    // UPI type pannina → Cash auto update
    $('#upi_amount').on('input', function () {
        updateSplit('upi');
    });

});
$('.final_cost').on('input', function () {
    $('#cash_amount').trigger('input');
});

</script>

<script>

$(document).on('keyup', 'input[name$="[mobile_no]"]', function () {

    let typed = $(this).val().trim();
    let $row = $(this).closest('tr');
    let $nameInput = $row.find('input[name$="[name]"]');

    $('.suggestion-box').remove();
    if (typed.length < 2) return;

    let $box = createSuggestionBox($(this));
    let shown = new Set();

    window.MOBILE_NAME_MAP.forEach(row => {
        if (!row.filter_mobile_no || !row.filter_name) return;

        let mobiles = row.filter_mobile_no.split(',').map(m => m.trim());
        let names   = row.filter_name.split(',').map(n => n.trim());

        mobiles.forEach((m, i) => {

            let key = m + '|' + names[i];
            if (m.startsWith(typed) && !shown.has(key)) {

                shown.add(key);

                $('<div>')
                    .text(`${names[i]} - ${m}`)
                    .css({ padding: '6px', cursor: 'pointer' })
                    .on('click', function () {
                        $nameInput.val(names[i]);
                        $row.find('input[name$="[mobile_no]"]').val(m);
                        $('.suggestion-box').remove();
                    })
                    .appendTo($box);
            }
        });
    });

    if ($box.children().length === 0) $box.remove();
});

 $(document).on('keyup', 'input[name$="[name]"]', function () {

    let typed = $(this).val().trim().toLowerCase();
    let $row = $(this).closest('tr');
    let $mobileInput = $row.find('input[name$="[mobile_no]"]');

    $('.suggestion-box').remove();
    if (typed.length < 2) return;

    let $box = createSuggestionBox($(this));
    let shown = new Set();

    window.MOBILE_NAME_MAP.forEach(row => {
        if (!row.filter_mobile_no || !row.filter_name) return;

        let mobiles = row.filter_mobile_no.split(',').map(m => m.trim());
        let names   = row.filter_name.split(',').map(n => n.trim().toLowerCase());

        names.forEach((n, i) => {

            let key = n + '|' + mobiles[i];
            if (n.startsWith(typed) && !shown.has(key)) {

                shown.add(key);

                $('<div>')
                    .text(`${row.filter_name.split(',')[i]} - ${mobiles[i]}`)
                    .css({ padding: '6px', cursor: 'pointer' })
                    .on('click', function () {
                        $row.find('input[name$="[name]"]').val(row.filter_name.split(',')[i]);
                        $mobileInput.val(mobiles[i]);
                        $('.suggestion-box').remove();
                    })
                    .appendTo($box);
            }
        });
    });

    if ($box.children().length === 0) $box.remove();
});

function createSuggestionBox($input) {
    $('.suggestion-box').remove();

    let offset = $input.offset();

    let $box = $('<div class="suggestion-box"></div>').css({
        position: 'absolute',
        top: offset.top + $input.outerHeight(),
        left: offset.left,
        width: $input.outerWidth(),
        background: '#fff',
        border: '1px solid #ccc',
        zIndex: 9999,
        maxHeight: '180px',
        overflowY: 'auto'
    });

    $('body').append($box);
    return $box;
}
$(document).on('click', function (e) {
    if (!$(e.target).closest('.suggestion-box, input').length) {
        $('.suggestion-box').remove();
    }
});

</script>
@endsection


