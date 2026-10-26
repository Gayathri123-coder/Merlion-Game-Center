@extends('app')
@section('title','Submission')
@section('styles')
{{-- SAME AS CREATE --}}

@endsection
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4>Edit Submission </h4>
            </div>
            <div class="col-sm-6">
               <!-- <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">Submission Data</li>
                    <li class="breadcrumb-item"><a href="{{route("submission_list")}}">Submission</a></li>
                    <li class="breadcrumb-item active"><a href=""> Edit</a></li>
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

                         <form method="POST" id="new_user" name="new_user" action="{{route('submission_update',[$edit_id])}}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="card-body">
                              <div class="row">
                                  <div class="col-md-6">
                                   
                                  
                                        <div class="form-group">
                                      <label for="">Console name<span class="error">*</span></label>
                                     <select class="form-control common_select2"
        name="console_id"
        id="console_id"
        data-old="{{ $submission_details->console_id }}">

    <option value="">Select Console Name</option>

    @foreach($console_list as $value)
        <option value="{{ $value->id }}"
                data-limit="{{ $value->limited_person }}"
                {{ $submission_details->console_id == $value->id ? 'selected' : '' }}>
            {{ $value->console_name }}
        </option>
    @endforeach
</select>

                                      </div>
                                  <div class="form-group">
                                      <label for="">Hours <span class="error">*</span></label>
                                      <select class="form-control" name="hours_id" id="hours_id">
                                        <option value="">Select Hours</option>
                                        <?php foreach($hours_list as $key=>$value){?>
                                        <option value="{{$value->id}}" data-id="{{$value->hours}}" <?php echo isset($submission_details) && ($submission_details->hours_id == $value->id) ? 'selected' : ""; ?> >{{$value->hours}}</option>
                                        <?php }?>
                                    </select>
                                  </div>
                                 
                                  <div class="form-group ">
                                    <label for="equipment" ><strong>Start Time<span class="error">*</span></strong> </label> 
                                    <div class="input-group date" id="start_time_picker" data-target-input="nearest">
                                      <input type="text" class="form-control datetimepicker-input" data-target="#start_time_picker" id="start_time" name="start_time"  value="{{ \Carbon\Carbon::parse($submission_details->start_time)->format('h:i A') }}"
/>
                                      <div class="input-group-append" data-target="#start_time_picker" data-toggle="datetimepicker">
                                          <div class="input-group-text"><i class="fa fa-clock"></i></div>
                                      </div>
                                  </div>
                                </div> 
                                <div class="form-group">
                                  <label for="">Total Hours <span class="error">*</span></label>
                                     <input type="text"  id="total_hours" name="total_hours" class="form-control" value="<?php echo isset($submission_details) && ($submission_details->total_hours) ? $submission_details->total_hours : ""; ?>">
                                </div> 
                                <div class="form-group">
                                  <label for="">Discount <span class="error">*</span></label>
                                     <input type="text" name="discount" class="form-control discount" value="<?php echo isset($submission_details) && ($submission_details->discount) ? $submission_details->discount : 0.00; ?>">
                                </div> 
                                                              <div class="form-group">
    <label>Payment Type <span class="error">*</span></label>
    <select class="form-control" name="payment_type" id="payment_type">
        <option value="">Select Payment Type</option>
        <?php foreach($payment_type_list as $value){ ?>
           <option value="{{ $value->id }}"
    {{ isset($submission_details) && $submission_details->payment_type == $value->id ? 'selected' : '' }}>
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
        <input type="number"
               class="form-control"
               name="cash_amount"
               id="cash_amount"
               value="{{ $payment_split['cash'] ?? 0 }}">
    </div>

    <div class="form-group">
        <label>UPI Amount</label>
        <input type="number"
               class="form-control"
               name="upi_amount"
               id="upi_amount"
               value="{{ $payment_split['upi'] ?? 0 }}">
    </div>

    <small class="text-muted">
        Cash + UPI = Final Cost
    </small>
</div>              
                                <div class="form-group">
                                  <label for="">Customer Place<span class="error">*</span></label>
                                  <input type="text"
       class="form-control"
       name="customer_place"
       value="{{ old('customer_place', $submission_details->customer_place ?? '') }}">
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
                                        $settings = $sor_settings ? (is_array($sor_settings) ? $sor_settings : @unserialize($sor_settings)) : [];
                                        if ($settings && count($settings) > 0) {
                                            foreach ($settings as $key => $set) {
                                                ?>
                                                <tr class="calrow_<?php echo $key; ?>">
                                                    <td><input type="text"  readonly="" value="<?php echo $set['player'] ?? 'Player - '.($key+1); ?>" class="form-control sor_<?php echo $key; ?>" name="calcpercent[<?php echo $key; ?>][player]" style="text-align:center"/></td>
                                                    <td><input type="text"  value="<?php echo $set['name'] ?? ''; ?>" class="form-control star_<?php echo $key; ?>" name="calcpercent[<?php echo $key; ?>][name]" style="text-align:center"/></td>
                                                    <td><input type="text" value="<?php echo $set['mobile_no'] ?? ''; ?>" class="form-control effective_<?php echo $key; ?> highlighttext" name="calcpercent[<?php echo $key; ?>][mobile_no]" style="text-align:center"/></td>
                                                </tr>
                                                <?php
                                            }
                                        } else {
                                        ?>
                                                <tr class="calrow_0">
                                                    <td><input type="text" value="Player - 1" class="form-control" name="calcpercent[0][player]" style="text-align:center"/></td>
                                                    <td><input type="text" class="form-control" name="calcpercent[0][name]" style="text-align:center"/></td>
                                                    <td><input type="text" class="form-control" name="calcpercent[0][mobile_no]" style="text-align:center"/></td>
                                                </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                </div>
                                  </div>
                                  <div class="col-md-6">
                                    
                                       <div class="form-group">
                                      <label for="">No of players <span class="error">*</span></label>
                                     <select class="form-control"
        name="no_of_players"
        id="no_of_players"
        data-old="{{ $submission_details->no_of_players }}">
    <option value="">Select No of players</option>
</select>
                                  
                                   </div>
                                      <div class="form-group">
                                        <label>Date <span class="error">*</span></label>
                                        <div class="input-group date" id="start_date" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input" data-target="#start_date" id="date" name="date"  value="<?php echo isset($submission_details) && ($submission_details->date) ? date('d-m-Y',strtotime($submission_details->date)) : ''; ?>" />
                                            <div class="input-group-append" data-target="#start_date" data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group ">
                                      <label for="equipment" ><strong>End Time<span class="error">*</span></strong> </label> 
                                      <div class="input-group date" id="end_time_picker" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input" data-target="#end_time_picker" id="end_time" name="end_time"  value="{{ \Carbon\Carbon::parse($submission_details->end_time)->format('h:i A') }}"
/>
                                        <div class="input-group-append" data-target="#end_time_picker" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-clock"></i></div>
                                        </div>
                                    </div>
                                  </div> 
                                  <div class="form-group">
                                    <label for="">Console Cost <span class="error">*</span></label>
                                       <input type="text" name="total_cost" class="form-control total_cost" value="<?php echo isset($submission_details) && ($submission_details->total_cost) ? $submission_details->total_cost: 0.00;?>">
                                  </div>
                                    <div class="form-group">
                                      <label for="">F&B Cost <span class="error">*</span></label>
                                         <input type="text" name="fandb_cost" class="form-control fandb_cost" value="<?php echo isset($submission_details) && ($submission_details->fandb_cost) ? $submission_details->fandb_cost: 0.00;?>">
                                    </div>
                                  <div class="form-group">
                                    <label for="">Final Cost <span class="error">*</span></label>
                                       <input type="text" name="final_cost" class="form-control final_cost" value="<?php echo isset($submission_details) && ($submission_details->final_cost) ? $submission_details->final_cost: 0.00;?>">
                                  </div>
                                  <div class="form-group">
                                    <label for="">Reference</label>
                                    <select class="form-control " name="reference_id" id="reference_id">
                                      <option value="">Select Reference</option>
                                      <?php foreach($reference_list as $key=>$value){?>
                                      <option value="{{$value->id}}" <?php echo (isset($submission_details) && ($submission_details->reference_id)==$value->id)?"selected":""?>>{{$value->name}}</option>
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
                                                     <?php
                                        $fsettings = $submission_details && isset($submission_details->fandb_informations) ? @unserialize($submission_details->fandb_informations) : [];
                                        if ($fsettings && count($fsettings) > 0) {
                                            foreach ($fsettings as $key => $set) {
                                                ?>
                                                  <tr class="data-row">
                                                    <td>
                                                    <select class="form-control" name="name[]" id="fandb">
                                                        <option value="">Select F&B</option>  
                                                        @if(count($FandBMaster)>0)
                                                        @foreach($FandBMaster as $ky=>$val)
                                                        <option data-value="{{$val->price}}" value="{{$val->id}}" <?php echo isset($set['name']) && $set['name']==$val->id ? 'selected':''; ?> >{{$val->name}}</option>
                                                        @endforeach
                                                        @endif
                                                    </select>
                                                    </td>
                                                    <td><input class="form-control" type="number" id="qty" name="qty[]" value="<?php echo isset($set['qty']) && $set['qty']!='' ? $set['qty']:1; ?>" /></td>
                                                    <td> <input class="form-control" type="text" name="price[]" readonly value="<?= $set['price'] ?? '' ?>"></td>
                                                    <td><span class="add-row" style="cursor: pointer;">➕</span></td>
                                                  </tr>
                                                   <?php
                                            }
                                        } else { ?>
                                                  <tr class="data-row">
                                                    <td>
                                                    <select class="form-control" name="name[]" id="fandb">
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
                                                    <td><span class="add-row" style="cursor: pointer;">➕</span></td>
                                                  </tr>
                                        <?php } ?>
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
@push('custom-script')
<script>
    window.SUBMISSION_ID = {{ $submission_details->id }};
</script>
<script>
    window.EDIT_PAYMENT_TYPE = "{{ $submission_details->payment_type }}";
    window.EDIT_PAYMENT_SPLIT = @json($payment_split ?? []);
</script>
<script>
    window.MOBILE_NAME_MAP = @json(
        \App\Submissiondata::select('filter_mobile_no','filter_name')
            ->orderBy('id','desc')
            ->get()
    );

    console.log('MAP LOADED', window.MOBILE_NAME_MAP);
</script>
<script>
$(document).on('keyup', 'input[name$="[mobile_no]"]', function () {
if (!Array.isArray(window.MOBILE_NAME_MAP)) return;
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
if (!Array.isArray(window.MOBILE_NAME_MAP)) return;

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
<script>
function buildPlayersDropdown(selectOld = false) {

    let limit = $('#console_id option:selected').data('limit');
    let oldPlayers = $('#no_of_players').data('old');
    let $players = $('#no_of_players');

    $players.empty().append('<option value="">Select No of players</option>');

    if (!limit) return;

    for (let i = 1; i <= limit; i++) {
        let selected = (selectOld && oldPlayers == i) ? 'selected' : '';
        $players.append(`<option value="${i}" ${selected}>${i}</option>`);
    }
}

// page load
$(document).ready(function () {
    buildPlayersDropdown(true); // preselect old value
});

// console change
$('#console_id').on('change', function () {

    // reset old selection
    $('#no_of_players').data('old', '');

    // rebuild dropdown
    buildPlayersDropdown(false);

    // reset price
    $('.total_cost').val('');
    $('.final_cost').val('');
});
</script>
<script>
$(function () {
    console.log('Edit Submission JS Loaded');
    $('#start_time_picker').datetimepicker({
    format: 'LT'
});

$('#end_time_picker').datetimepicker({
    format: 'LT',
    useCurrent: false
});
$('#start_date').datetimepicker({
    format: 'DD-MM-YYYY'
});


    // ===============================
    // GLOBAL
    // ===============================
    window.CURRENT_PLACE_ID = {{ session('place_id') ?? 'null' }};

    // ===============================
    // PRICE CALCULATION
    // ===============================
   function updateTotalPrice() {

    let fandb_total = 0;

    //  total_cost read only
    let total_cost = Number($('.total_cost').val());

    if (isNaN(total_cost) || total_cost <= 0) {
        return; 
    }

    let discount = Number($('.discount').val()) || 0;

    $('input[name="price[]"]').each(function () {
        fandb_total += Number($(this).val()) || 0;
    });

    $('.fandb_cost').val(fandb_total.toFixed(2));

    // discount = flat OR percent – doesn’t matter now
    let final_cost = (total_cost - discount) + fandb_total;

    $('.final_cost').val(final_cost.toFixed(2));
    rebalanceSplitIfEnabled(); 
}
    // ===============================
    // F&B EVENTS
    // ===============================
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

    $(document).on('click', '.add-row', function () {
        let $clone = $(this).closest('tr').clone();
        $clone.find('select').val('');
        $clone.find('input[name="qty[]"]').val('1');
        $clone.find('input[name="price[]"]').val('');
        $('#myTable tbody').append($clone);
    });

    // ===============================
    // FETCH CONSOLE PRICE
    // ===============================
   function fetchPrice() {

    let console_id = $('#console_id').val();
    let no_of_players = $('#no_of_players').val();
    let hours_id = $('#hours_id').val();

    if (!console_id || !no_of_players || !hours_id) return;

    $.ajax({
        url: "{{ route('getpriceinfo') }}",
        type: "POST",
        data: {
            console_id,
            no_of_players,
            hours_id,
            submission_id: window.SUBMISSION_ID,
            _token: '{{ csrf_token() }}'
        },
        success: function (res) {

            if (res.status && res.total_cost > 0) {
                $('.total_cost').val(Number(res.total_cost).toFixed(2));
                updateTotalPrice(); 
            }
        }
    });
}
function rebalanceSplitIfEnabled() {
    if (!$('#enable_split').is(':checked')) return;

    let final = parseFloat($('.final_cost').val()) || 0;
    let cash  = parseFloat($('#cash_amount').val()) || 0;

    $('#upi_amount').val(Math.max(final - cash, 0));
}

    // ===============================
    // EVENTS
    // ===============================
    $('#console_id, #hours_id, #no_of_players').on('change', fetchPrice);
    $('.discount').on('input', updateTotalPrice);

    // ===============================
    // PLAYER ROW ADJUST
    // ===============================
    $('#no_of_players').on('change', function () {
        let count = parseInt($(this).val()) || 0;
        let tbody = $('.sorstardynamic tbody');
        let existingRows = tbody.find('tr').length;

        if (count > existingRows) {
            for (let i = existingRows; i < count; i++) {
                tbody.append(`
                    <tr class="calrow_${i}">
                        <td><input type="text" readonly value="Player - ${i + 1}" class="form-control" name="calcpercent[${i}][player]" style="text-align:center"/></td>
                        <td><input type="text" class="form-control" name="calcpercent[${i}][name]" style="text-align:center"/></td>
                        <td><input type="text" class="form-control" name="calcpercent[${i}][mobile_no]" style="text-align:center"/></td>
                    </tr>
                `);
            }
        } else if (count < existingRows) {
            for (let i = existingRows; i > count; i--) {
                tbody.find('tr:last').remove();
            }
        }
    });
    // PAYMENT SPLIT
let isEditSplitApplied = false;
//  Initial load – DB split irundha auto open
if (
    window.EDIT_PAYMENT_SPLIT &&
    (window.EDIT_PAYMENT_SPLIT.cash > 0 || window.EDIT_PAYMENT_SPLIT.upi > 0)
) {
    $('#split_payment_box').show();
    $('#enable_split').prop('checked', true);

    $('#cash_amount').val(window.EDIT_PAYMENT_SPLIT.cash || 0);
    $('#upi_amount').val(window.EDIT_PAYMENT_SPLIT.upi || 0);

    isEditSplitApplied = true;
}
//  User manually enables split
$('#enable_split').on('change', function () {
    if ($(this).is(':checked')) {
        $('#split_payment_box').slideDown();

        let final = parseFloat($('.final_cost').val()) || 0;
        let cash  = parseFloat($('#cash_amount').val()) || 0;

        if (cash <= 0) {
            $('#cash_amount').val(final);
            $('#upi_amount').val(0);
        }

    } else {
        $('#split_payment_box').slideUp();
        $('#cash_amount').val(0);
        $('#upi_amount').val(0);
    }
});
//  Auto balance
$('#cash_amount').on('input', function () {
    let final = parseFloat($('.final_cost').val()) || 0;
    let cash  = parseFloat($(this).val()) || 0;
    $('#upi_amount').val(Math.max(final - cash, 0));
});
$('#upi_amount').on('input', function () {
    let final = parseFloat($('.final_cost').val()) || 0;
    let upi   = parseFloat($(this).val()) || 0;
    $('#cash_amount').val(Math.max(final - upi, 0));
});
    // ===============================
    // SWEET ALERT
    // ===============================
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
    // ===============================
    // INITIAL LOAD
 fetchPrice();
});
</script>
<script>
function autoCalculateEndTime() {
    let startTime = $('#start_time').val(); // ex: 10:30 AM
    let hoursText = $('#hours_id option:selected').text(); // ex: 2

    if (!startTime || !hoursText) return;

    // hours value extract (in case "2 Hours" format)
    let hours = parseInt(hoursText);

    if (isNaN(hours)) return;

    let start = moment(startTime, 'h:mm A');
    let end = start.clone().add(hours, 'hours');

    $('#end_time').val(end.format('h:mm A'));
}
$(document).on('change', '#start_time', autoCalculateEndTime);
$(document).on('change', '#hours_id', autoCalculateEndTime);
</script>
<script>
function autoCalculateEndTime() {

    let startTime = $('#start_time').val();               // ex: 10:00 AM
    let timeValue = $('#hours_id option:selected').data('id'); // ex: 01:30

    if (!startTime || !timeValue) return;

    let parts = timeValue.split(':');
    let hours = parseInt(parts[0], 10) || 0;
    let minutes = parseInt(parts[1], 10) || 0;

    let start = moment(startTime, 'h:mm A');
    let end   = start.clone()
                     .add(hours, 'hours')
                     .add(minutes, 'minutes');

    $('#end_time').val(end.format('h:mm A'));
}
</script>
<script>
$(document).on('change', '#hours_id', function () {
    autoCalculateEndTime();       
    updateTotalHoursFromHours(); 
});
</script>
<script>
function calculateTotalHoursFromTime() {

    let startTime = $('#start_time').val();
    let endTime   = $('#end_time').val();

    if (!startTime || !endTime) return;

    let start = moment(startTime, 'h:mm A');
    let end   = moment(endTime, 'h:mm A');

    // midnight crossing
    if (end.isBefore(start)) {
        end.add(1, 'day');
    }

    let diffMinutes = end.diff(start, 'minutes');

    let hours   = Math.floor(diffMinutes / 60);
    let minutes = diffMinutes % 60;

    $('#total_hours').val(
        String(hours).padStart(2,'0') + ':' +
        String(minutes).padStart(2,'0')
    );
}

/* IMPORTANT — datetimepicker events */
$('#start_time_picker').on('change.datetimepicker', function () {
    calculateTotalHoursFromTime();
});

$('#end_time_picker').on('change.datetimepicker', function () {
    calculateTotalHoursFromTime();
});
</script>
<script>
$(document).ready(function () {

    function toggleSplitBox() {
        let paymentTypeId = $('#payment_type').val();

        if (paymentTypeId == '3') { // ✅ Mixed
            $('#split_toggle_box').show();
        } else { // ❌ Cash / UPI
            $('#split_toggle_box').hide();
            $('#split_payment_box').hide();
            $('#enable_split').prop('checked', false);
            $('#cash_amount').val(0);
            $('#upi_amount').val(0);
        }
    }

    // change pannina
    $('#payment_type').on('change', toggleSplitBox);

    // page load-la (EDIT page important)
    toggleSplitBox();

    // checkbox logic
    $('#enable_split').on('change', function () {
        if ($(this).is(':checked')) {
            $('#split_payment_box').show();
        } else {
            $('#split_payment_box').hide();
            $('#cash_amount').val(0);
            $('#upi_amount').val(0);
        }
    });

});
</script>
<script>
let lastChangedSplit = null;

// track which field user edited last
$('#cash_amount').on('input', function () {
    lastChangedSplit = 'cash';
    autoBalanceSplit();
});

$('#upi_amount').on('input', function () {
    lastChangedSplit = 'upi';
    autoBalanceSplit();
});

// final cost change aana
$(document).on('input change', '.final_cost', function () {
    autoBalanceSplit();
});

function autoBalanceSplit() {

    if (!$('#enable_split').is(':checked')) return;

    let final = parseFloat($('.final_cost').val()) || 0;
    let cash  = parseFloat($('#cash_amount').val()) || 0;
    let upi   = parseFloat($('#upi_amount').val()) || 0;

    if (lastChangedSplit === 'cash') {
        $('#upi_amount').val(Math.max(final - cash, 0).toFixed(2));
    }
    else if (lastChangedSplit === 'upi') {
        $('#cash_amount').val(Math.max(final - upi, 0).toFixed(2));
    }
    else {
        // first time → full amount cash
        $('#cash_amount').val(final.toFixed(2));
        $('#upi_amount').val('0.00');
    }
}
</script>

@endpush
@endsection
