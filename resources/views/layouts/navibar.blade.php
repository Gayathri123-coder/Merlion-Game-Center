<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block" style="font-weight:bold;font-size:25px;">
       <!--  <img src="{{ asset('/public/themes/img/logo/logo.png')}}" alt="Logo" title="Logo" class="brand-image" style="opacity: .8;width: 10%;">-->
       Merlion Game Station
      </li>
      <!--
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Contact</a>
      </li>-->
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      <li class="nav-item">
<!--        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
          <i class="fas fa-search"></i>
        </a>
        <div class="navbar-search-block">
          <form class="form-inline">
            <div class="input-group input-group-sm">
              <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
              <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                  <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </form>
        </div>-->
      </li>

      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
<!--        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-comments"></i>
          <span class="badge badge-danger navbar-badge">3</span>
        </a>-->
<!--        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <a href="#" class="dropdown-item">
             Message Start 
            <div class="media">
              <img src="{{asset('public/themes/img/user1-128x128.jpg')}}" alt="User Avatar" class="img-size-50 mr-3 img-circle">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Brad Diesel
                  <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">Call me whenever you can...</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
             Message End 
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
             Message Start 
            <div class="media">
              <img src="dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  John Pierce
                  <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">I got your message bro</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
             Message End 
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
             Message Start 
            <div class="media">
              <img src="dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Nora Silvester
                  <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">The subject goes here</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
             Message End 
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
        </div>-->
      </li>
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown" style="padding-bottom: 12px;">
        <li class="nav-item" style="min-width:200px;">
<form method="POST" action="{{ route('set.place') }}">
    @csrf
    <select name="place_id"
            class="form-control form-control-sm"
            onchange="this.form.submit()" style="font-size:16px;font-weight:bold">
       <!-- <option value="">Select Branch</option>-->

        @foreach($places as $place)
            <option value="{{ $place->id }}"
                {{ session('place_id') == $place->id ? 'selected' : '' }}>
                {{ $place->place_name }}
            </option>
        @endforeach
    </select>
</form>
</li>
        <a class="nav-link" data-toggle="dropdown" href="#" style="font-size: 14px;font-weight: bold;padding-bottom: 10px;">
                Admin <!--    {{auth::user()->U_Name}}<br>  
            <span style="font-size: 10px;"></span>
           {{auth::user()->Company_Name}}-->  
        </a>
        
      </li>
      <!--  <li class="nav-item">
            <a class="nav-link"  href="{{route("logout")}}" role="button" title="My Profile">
              <i class="fas fa-user"></i>
            </a>
        </li>-->
      <li class="nav-item">
        <a class="nav-link"  href="{{route("logout")}}" role="button"  title="Logout">
          <i class="fas fa-sign-out-alt"></i>
        </a>
      </li>
<!--      <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-controlsidebar-slide="true" href="#" role="button">
          <i class="fas fa-th-large"></i>
        </a>
      </li>-->
    </ul>
  </nav>