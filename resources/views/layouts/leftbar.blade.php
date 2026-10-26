 @inject('user', 'App\User')

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <!--<a href="index3.html" class="brand-link">-->
      <!--<img src="{{ asset('/public/themes/img/logo/leftside_logo.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">-->
      
<!--        <span class="brand-text font-weight-light">Paramesh</span>
    </a>-->

    <!-- Sidebar -->
    <div class="sidebar">
       <!--Sidebar user panel (optional)--> 
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
            <i class="fas fa-user" style="padding: 10px;color:white;"></i>
          <!--<img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">-->
        </div>
        <div class="info">
          <a href="#" class="d-block">{{Auth::user()->U_Name}}</a>
        </div>
      </div>

    

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-cog"></i>
              <p>
                Master
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
                 <li class="nav-item">
                <a href="{{route('place_master')}}" class="nav-link {{ request()->is('place_master') ? 'active' : ''}}">
                  <i class="fas fa-building nav-icon"></i>
                  <p>Branch</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('console_master')}}" class="nav-link {{ request()->is('console_master') ? 'active' : ''}}">
               <i class="nav-icon fas fa-gamepad"></i> 
                 <!-- <img src="{{ asset('/public/icons/1766490327_favicon.png')}}" alt="icon" width="20" height="20">-->
                  <p>Console</p>
                </a>
              </li>                
                 <li class="nav-item">
                              <a href="{{route('players_list')}}" class="nav-link {{ request()->is('players_list') ? 'active' : ''}}">
                                <i class="nav-icon fas fa-users"></i>  <p>No of players</p>
                              </a>
                 </li>
      
                 <li class="nav-item">
                              <a href="{{route('Hours_master')}}" class="nav-link {{ request()->is('Hours_master') ? 'active' : ''}}">
                                <i class="fas fa-clock nav-icon"></i>
                                <p>Hours</p>
                              </a>
                 </li> 
              <li class="nav-item">
                <a href="{{route('payment_type_master')}}" class="nav-link {{ request()->is('payment_type_master') ? 'active' : ''}}">
                  <i class="fas fa-credit-card nav-icon"></i>
                  <p>Payment Type</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('price_master')}}" class="nav-link {{ request()->is('price_master') ? 'active' : ''}}">
                   <i class="nav-icon fas fa-tags"></i>
                  <p>Price</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('fandb_master')}}" class="nav-link {{ request()->is('fandb_master') ? 'active' : ''}}">
                 <i class="nav-icon fas fa-hamburger"></i>
                  <p>F&B Master</p>
                </a>
              </li>
                <li class="nav-item">
                  <a href="{{route('reference_master')}}" class="nav-link {{ request()->is('reference_master') ? 'active' : ''}}">
                    <i class="fas fa-id-card nav-icon"></i>
                    <p>Reference</p>
                  </a>
                </li>
            </ul>
          </li>
        
<!--          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-list-alt"></i>
              <p>
                Submission
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('submission_list')}}" class="nav-link {{ request()->is('submission_list') ? 'active' : ''}}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>List</p>
                </a>
              </li>
               
            </ul>
          </li>-->
          <li class="nav-item">
            <a href="#" class="nav-link">
               <i class="nav-icon fas fa-play"></i>
              <p>
                Game Play
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{route('submission_report_list')}}" class="nav-link {{ request()->is('submission_report_list') ? 'active' : ''}}">
                    <i class="nav-icon fas fa-list-alt"></i>
                  <p>List</p>
                </a>
              </li>
               
            </ul>
          </li>
            </ul>
          </li>
          
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>