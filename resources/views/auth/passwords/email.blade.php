<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">
 <!--<link type="text/css" rel="stylesheet"  href="{{URL::asset('public/assets/login/fonts/icomoon/style.css')}}" />-->
    <link rel="stylesheet" href="{{ asset('/public/themes/login/css/owl.carousel.min.css')}}" />
    <link rel="stylesheet" href="{{ asset('/public/themes/login/css/bootstrap.min.css')}}" />
    <link rel="stylesheet" href="{{ asset('/public/themes/login/css/style.css')}}" />

 <style>
     .error{
         color:red;
     }
 </style>
    <title>{{ __('Reset Password') }}</title>
  </head>
  <body>
  

  
  <div class="content">
    <div class="container">
      <div class="row">
        <div class="col-md-6 order-md-2">
          <img src="{{ asset('/public/themes/login/images/undraw_file_sync_ot381.svg')}}" alt="Image" class="img-fluid">
        </div>
        <div class="col-md-6 contents">
          <div class="row justify-content-center">
            <div class="col-md-8">
              <div class="mb-4">
                <img src="{{ asset('/public/themes/img/logo/login_logo.png')}}" alt="Image" class="img-fluid mb-4" style="width:250px">
              <h3>Reset Password</h3>
              <p class="mb-4" style="font-size:11px">Lead | Quote | Approval | Purchase Order | GRN Inventory | Material  |  Management | Manpower Schedule | Site Work |Attendance | Payroll</p>
            </div>
                  
                   @include('layouts/alert')
                    <form method="POST" action="{{ route('password.email') }}">
              @csrf
              <div class="form-group first" style="margin-bottom: 1.5rem !important;">
                <label for="username">{{ __('Email Address') }}</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                 <span class="invalid-feedback" role="alert">
                 <strong>{{ $message }}</strong>
                   </span>
                 @enderror
              </div>
                          
                <div class="form-group last mb-4" style="margin-bottom: 1.5rem !important;">
                     <button type="submit" class="btn btn-primary">
                                    {{ __('Send Password Reset Link') }}
                                </button>
               </div>
              
            </form>
            </div>
          </div>
          
        </div>
        
      </div>
    </div>
  </div>

   
   <script src="{{ asset('/public/themes/login/js/jquery-3.3.1.min.js') }}"></script>
   <script src="{{ asset('/public/themes/login/js/popper.min.js') }}"></script>
   <script src="{{ asset('/public/themes/login/js/bootstrap.min.js') }}"></script>
   <script src="{{ asset('/public/themes/login/js/main.js') }}"></script>
    <script>
    $(".error").hide();
    </script>
  </body>
</html>