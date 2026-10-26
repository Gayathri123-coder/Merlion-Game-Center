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
     body{
         background-color: #fff;
     }
 </style>
    <title>Merlion Gaming - Login</title>
  </head>
  <body>
  <div class="content">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
            {{-- <img src="{{ asset('/public/themes/login/images/logo.png')}}" alt="Image" class="img-fluid" style="margin-bottom:15%;"> --}}
          <img src="{{ asset('/public/themes/img/logo/game_login.png')}}" alt="Image" class="img-fluid" style="margin-top: -15%">
        </div>
        <div class="col-md-6 contents">
          <div class="row justify-content-center">
            <div class="col-md-8">
              <div class="mb-4">
                {{-- <img src="{{ asset('/public/themes/img/logo/login_logo.png')}}" alt="Image" class="img-fluid mb-4" style="width:200px"> --}}
              <h3>Sign In</h3>
              {{-- <p class="mb-4" style="font-size:11px;color: black;">Lead | Quote | Approval | Purchase Order | GRN Inventory | Material  |  Management | Manpower Schedule | Site Work |Attendance | Payroll</p> --}}
            </div>
                  
                   @include('layouts/alert')
            <form action="{{ route('login') }}" method = "post" class="form-signin" id="LoginForm" autocomplete="off">
                <input type = "hidden" name = "_token" value = "<?php echo csrf_token() ?>">
              <div class="form-group first field--not-empty" style="margin-bottom: 1.5rem !important;">
                <label for="username">Username</label>
                <input type="text" class="form-control @error('U_UserID') is-invalid @enderror" id="username" name="U_UserID" placeholder="" value="{{ old('U_UserID') }}">
                                  @error('U_UserID')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
              </div>
                <div class="form-group last mb-4" style="margin-bottom: 1.5rem !important;">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="">
                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
              </div>
                                
              <div class="d-flex mb-5 align-items-center">
<!--                <label class="control control--checkbox mb-0"><span class="caption">Remember me</span>
                  <input type="checkbox" checked="checked"/>
                  <div class="control__indicator"></div>
                </label>-->
              <!--    <a href="{{ route('password.request') }}" class="cr">Forgot Password?</a>
                <span class="ml-auto"><a href="#" class="forgot-pass">Forgot Password</a></span>--> 
              </div>

                <input type="submit" value="Log In" class="btn text-white btn-block btn-danger" name="MakeLogin" style="font-size: 25px;background-color:#DC2626">
      
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