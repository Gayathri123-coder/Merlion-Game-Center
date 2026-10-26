
<!-- jQuery -->
<script src="{{ asset('/public/themes/plugins/jquery/jquery.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('/public/themes/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{ asset('/public/themes/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('/public/themes/plugins/select2/js/select2.full.min.js') }}"></script>

<!-- ChartJS -->
<script src="{{ asset('/public/themes/plugins/chart.js/Chart.min.js') }}"></script>
<!-- Sparkline -->
<!--<script src="{{ asset('/public/themes/plugins/sparklines/sparkline.js') }}"></script>-->
<!-- JQVMap -->
<!--<script src="{{ asset('/public/themes/plugins/jqvmap/jquery.vmap.min.js') }}"></script>-->
<!--<script src="{{ asset('/public/themes/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>-->
<!-- jQuery Knob Chart -->
<script src="{{ asset('/public/themes/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
<!-- daterangepicker -->
<script src="{{ asset('/public/themes/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('/public/themes/plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- Summernote -->
<script src="{{ asset('/public/themes/plugins/summernote/summernote-bs4.min.js') }}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('/public/themes/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<!-- DataTables  & Plugins -->
<script src="{{ asset('/public/themes/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('/public/themes/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('/public/themes/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('/public/themes/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('/public/themes/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('/public/themes/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('/public/themes/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('/public/themes/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('/public/themes/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('/public/themes/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('/public/themes/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('/public/themes/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
<!-- Bootstrap Switch -->
<script src="{{ asset('/public/themes/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
<!-- jquery-validation -->
<script src="{{ asset('/public/themes/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('/public/themes/plugins/jquery-validation/additional-methods.min.js') }}"></script>
<script src="{{ asset('/public/themes/plugins/sweetalert2/sweetalert2.min.js')}}"></script>
<!-- date-range-picker -->
<script src="{{ asset('/public/themes/plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('/public/themes/js/adminlte.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<!--<script src="{{ asset('/public/themes/js/demo.js') }}"></script>-->
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!--<script src="{{ asset('/public/themes/js/pages/dashboard.js') }}"></script>-->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.13.4/jquery.mask.min.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('/public/themes/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>

@yield('script')
@stack('custom-script')
<script>
  $(function () {
    $(".datatable_with_buttons").DataTable({
      "responsive": true,
      "lengthChange": true,
      "autoWidth": true,
      "buttons": ["csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $(".datatable").DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });

  $("input[data-bootstrap-switch]").each(function(){
      $(this).bootstrapSwitch('state', $(this).prop('checked'));
    });



$(function () {
    var url = window.location;
    // for single sidebar menu
    $('ul.nav-sidebar a').filter(function () {
        return this.href == url;
    }).addClass('active');

    // for sidebar menu and treeview
    $('ul.nav-treeview a').filter(function () {
        return this.href == url;
    }).parentsUntil(".nav-sidebar > .nav-treeview")
        .css({'display': 'block'})
        .addClass('menu-open').prev('a')
        .addClass('active');
});

 $('.common_select2').select2({
      theme: 'bootstrap4'
    });

    $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        jQuery.fn.ForceNumericOnly =
function()
{
    return this.each(function()
    {
        $(this).keydown(function(e)
        {
            var key = e.charCode || e.keyCode || 0;
            // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
            // home, end, period, and numpad decimal

            return (
                key == 8 ||
                key == 9 ||
                key == 13 ||
                key == 46 ||
                key == 110 ||
//                key == 190 || .
                (key >= 35 && key <= 40) ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105));
        });

        $(this).keyup(function(e)
        {
            var key = e.charCode || e.keyCode || 0;
            // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
            // home, end, period, and numpad decimal
            var value=$(this).val();
            if(value==""){
                return $(this).val("0");
            }
            if(value.length>1){
                value= value.replace(/^0+/, '');
                $(this).val(value)
                if($(this).val()==""){
                    $(this).val(0);
                }
            }

            return (
                key == 8 ||
                key == 9 ||
                key == 13 ||
                key == 46 ||
                key == 110 ||
//                key == 190 ||
                (key >= 35 && key <= 40) ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105));
        });
    });
};

//$(".qty").ForceNumericOnly();
function delete_unwanted_rows() {
 $('.is_header_checkbox').each(function()
                {
                  var check_box = $(this);
                  var row_id = $(this).data('id');

                  var validated=false;

                  var sor_items=$(".sor_items_"+row_id);
                  var item_unit=$(".item_units_"+row_id);
                  var item_qty=$(".item_qty_"+row_id);
                  var item_description=$(".description_"+row_id);
                  var sor_items_value=$(".sor_items_"+row_id).val();
                  var item_unit_value=$(".item_units_"+row_id).val();
                  var item_qty_value=$(".item_qty_"+row_id).val();
                   var item_description_value=$(".description_"+row_id).val();
                  if($(check_box).is(":checked")==false){
                   if(sor_items_value=="" && item_unit_value=="" && item_qty_value==0 && item_description_value==""){
                     $(".delete_" + row_id).remove();
                   }
               }
               else
               {
                 var header_text=$(".header_text_"+row_id);
                    var header_text_value=$(".header_text_"+row_id).val();
                 if(header_text_value=="")
                 {
                     $(".delete_" + row_id).remove();
                 }

               }

                });
            }
//end remove unwanted rows
$("textarea").keyup(function(){
    var value = $(this).val();
    if (value.indexOf('#') != -1) {
        $(this).val(value.replace(/\#/g, ""));
    }
    if (value.indexOf('!') != -1) {
        $(this).val(value.replace(/\!/g, ""));
    }
    if (value.indexOf('@') != -1) {
        $(this).val(value.replace(/\@/g, ""));
    }
    if (value.indexOf('~') != -1) {
        $(this).val(value.replace(/\~/g, ""));
    }
    if (value.indexOf('`') != -1) {
        $(this).val(value.replace(/\`/g, ""));
    }
    if (value.indexOf('$') != -1) {
        $(this).val(value.replace(/\$/g, ""));
    }
    if (value.indexOf('%') != -1) {
        $(this).val(value.replace(/\%/g, ""));
    }
    if (value.indexOf('^') != -1) {
        $(this).val(value.replace(/\^/g, ""));
    }
    if (value.indexOf('*') != -1) {
        $(this).val(value.replace(/\*/g, ""));
    }
    if (value.indexOf('{') != -1) {
        $(this).val(value.replace(/\{/g, ""));
    }
    if (value.indexOf('}') != -1) {
        $(this).val(value.replace(/\}/g, ""));
    }
    if (value.indexOf('|') != -1) {
        $(this).val(value.replace(/\|/g, ""));
    }
    if (value.indexOf(':') != -1) {
        $(this).val(value.replace(/\:/g, ""));
    }
    if (value.indexOf('"') != -1) {
        $(this).val(value.replace(/\"/g, ""));
    }
    if (value.indexOf("'") != -1) {
        $(this).val(value.replace(/\'/g, ""));
    }
    if (value.indexOf(';') != -1) {
        $(this).val(value.replace(/\;/g, ""));
    }
    if (value.indexOf('<') != -1) {
        $(this).val(value.replace(/\</g, ""));
    }
    if (value.indexOf('>') != -1) {
        $(this).val(value.replace(/\>/g, ""));
    }
    if (value.indexOf('.') != -1) {
       // $(this).val(value.replace(/\./g, ""));
    }
    if (value.indexOf('?') != -1) {
        $(this).val(value.replace(/\?/g, ""));
    }
    if (value.indexOf('=') != -1) {
        $(this).val(value.replace(/\=/g, ""));
    }
    if (value.indexOf('+') != -1) {
        $(this).val(value.replace(/\+/g, ""));
    }
     
});
$("textarea").keypress(
  function(event){
    if (event.which == '13') {
      event.preventDefault();
    }
});

</script>

