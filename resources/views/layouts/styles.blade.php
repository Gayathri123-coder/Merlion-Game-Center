
<!-- Google Font: Source Sans Pro -->

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('/public/themes/plugins/fontawesome-free/css/all.min.css') }}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
   <link rel="stylesheet" href="{{ asset('/public/themes/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
  <!-- iCheck -->
   <link rel="stylesheet" href="{{ asset('/public/themes/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
  <!-- JQVMap -->
   <link rel="stylesheet" href="{{ asset('/public/themes/plugins/jqvmap/jqvmap.min.css') }}">
  <!-- Theme style -->
   <link rel="stylesheet" href="{{ asset('/public/themes/css/adminlte.min.css') }}">
   <link rel="stylesheet" href="{{ asset('/public/themes/css/themecustomize.css') }}">
  <!-- overlayScrollbars -->
   <link rel="stylesheet" href="{{ asset('/public/themes/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
  <!-- Daterange picker -->
   <link rel="stylesheet" href="{{ asset('/public/themes/plugins/daterangepicker/daterangepicker.css') }}">
  <!-- summernote -->
   <link rel="stylesheet" href="{{ asset('/public/themes/plugins/summernote/summernote-bs4.min.css') }}">
     <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('/public/themes/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/public/themes/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/public/themes/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('/public/themes/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/public/themes/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ asset('/public/themes/plugins/daterangepicker/daterangepicker.css') }}">
 <!-- csrf token -->
 <!-- sweet alert -->
     <link rel="stylesheet" href="{{ asset('/public/themes/plugins/sweetalert2/sweetalert2.min.css') }}">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

     <meta name="csrf-token" content="{{ csrf_token() }}">
@yield('style')