<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Twitter -->
    <meta name="twitter:site" content="@">
    <meta name="twitter:creator" content="@">
    <meta name="twitter:card" content="">
    <meta name="twitter:title" content="">
    <meta name="twitter:description" content="">
    <meta name="twitter:image" content="">

    <!-- Facebook -->
    <meta property="og:url" content=" ">
    <meta property="og:title" content=" ">
    <meta property="og:description" content=" ">

    <meta property="og:image" content=" ">
    <meta property="og:image:secure_url" content=" ">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="600">

    <!-- Meta -->
    <meta name="description" content=" ">
    <meta name="author" content=" ">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->


    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('img/proaxive-x.ico')}}" rel="shortcut icon" type="icon/proaxive-x" sizes="16x16">

    <!-- vendor css -->
    <link href="{{ asset('lib/font-awesome/css/font-awesome.css')}}" rel="stylesheet">
    <link href="{{ asset('lib/Ionicons/css/ionicons.css')}}" rel="stylesheet">
    <link href="{{ asset('lib/perfect-scrollbar/css/perfect-scrollbar.css')}}" rel="stylesheet">
    <link href="{{ asset('lib/jquery-toggles/toggles-full.css')}}" rel="stylesheet">
    <link href="{{ asset('lib/rickshaw/rickshaw.min.css')}}" rel="stylesheet">

    <!-- Amanda CSS -->

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="{{ asset('css/jquery.datetimepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css')}}">


    <style>
        .mdl-grid{
            width:100%;
        }
        .dt-table{
            width:100%;
        }
        th, td {
            white-space: nowrap;
        }

        table.dataTable tbody th, table.dataTable tbody td {
   padding: 5px;
}

.table th,
.table td 
{
   
 font-size:12px;
  padding: 0.75rem;
  vertical-align: top;
  border-top: 1px solid #dee2e6;


}

table.dataTable tbody td
   padding: 5px;
}


    </style>
</head>
<body>
<div id="app">
    <div class="am-header">
        <div class="am-header-left">
            <a id="naviconLeft" href="#" class="am-navicon d-none d-lg-flex"><i class="icon ion-navicon-round"></i></a>
            <a id="naviconLeftMobile" href="#" class="am-navicon d-lg-none"><i class="icon ion-navicon-round"></i></a>
            <a href="index.html" class="am-logo"><img src="{{ asset('img/logo_final.png')}}" /></a>
        </div><!-- am-header-left -->

        <div class="am-header-right">
            <div class="dropdown dropdown-notification">
                <a href="#" class="nav-link pd-x-7 pos-relative" data-toggle="dropdown">
                    <i class="icon ion-ios-bell-outline tx-24"></i>
                    <!-- start: if statement -->
                    <span class="square-8 bg-danger pos-absolute t-15 r-0 rounded-circle"></span>
                    <!-- end: if statement -->
                </a>
                <div class="dropdown-menu wd-300 pd-0-force">
                    <div class="dropdown-menu-header">
                        <label>Notifications</label>
                        <a href="#">Mark All as Read</a>
                    </div><!-- d-flex -->

                    <div class="media-list">
                        <!-- loop starts here -->
                        
                        <!-- loop ends here -->

                        <div class="media-list-footer">
                            <a href="#" class="tx-12"><i class="fa fa-angle-down mg-r-5"></i> Show All Notifications</a>
                        </div>
                    </div><!-- media-list -->
                </div><!-- dropdown-menu -->
            </div><!-- dropdown -->
            <div class="dropdown dropdown-profile">
                <a href="#" class="nav-link nav-link-profile" data-toggle="dropdown">
                    <img src="{{ asset('img/img3.jpg')}}" class="wd-32 rounded-circle" alt="">
                    <span class="logged-name"><span class="hidden-xs-down">{{ Auth::user()->name }}</span> <i class="fa fa-angle-down mg-l-3"></i></span>
                </a>
                <div class="dropdown-menu wd-200">
                    <ul class="list-unstyled user-profile-nav">
                        <li><a href="{{url('/customer_members')}}"><i class="icon ion-ios-person-outline"></i> Add Members</a></li>
                        <li><a href="{{url('/customer_setting')}}"><i class="icon ion-ios-gear-outline"></i> Settings</a></li>

                        <li><a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"><i class="icon ion-power"></i>
                                {{ __('Logout') }}
                            </a></li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </ul>
                </div><!-- dropdown-menu -->
            </div><!-- dropdown -->
        </div><!-- am-header-right -->
    </div><!-- am-header -->

    <div class="am-sideleft">
        <ul class="nav am-sideleft-tab" style="background: #2d3a50;">
            <li class="nav-item">
                <a href="#mainMenu" class="nav-link active"><i class="icon ion-ios-home-outline tx-24"></i></a>
            </li>

        </ul>

        <div class="tab-content">
            <div id="mainMenu" class="tab-pane active">
                <ul class="nav am-sideleft-menu">
                    <li class="nav-item">
                        <a href="index.html" class="nav-link active">
                            <i class="icon ion-ios-home-outline"></i>
                            <span>Dashboard</span>
                        </a>
                    </li><!-- nav-item -->
                           <li class="nav-item">
                        <a href="#" class="nav-link with-sub">
                            <i class="icon ion-ios-briefcase-outline"></i>
                            <span>Travel </span>
                        </a>
                        <ul class="nav-sub">
                            <li class="nav-item"><a href="{{url('/travel_request')}}" class="nav-link">Create Travel Request</a></li>
                            <li class="nav-item"><a href="{{url('/customer_travel_list')}}" class="nav-link">Travel Request List </a></li>
                        </ul>
                    </li><!-- nav-item -->
                    <li class="nav-item">
                        <a href="#" class="nav-link with-sub">
                            <i class="icon ion-ios-briefcase-outline"></i>
                            <span>Travel Expense</span>
                        </a>
                          <ul class="nav-sub">
                            <li class="nav-item"><a href="{{url('/expense/add')}}" class="nav-link">Create Travel Expense</a></li>
                            <li class="nav-item"><a href="{{url('/expense/grid')}}" class="nav-link">Travel Expense List </a></li>
                        </ul>
                    </li><!-- nav-item -->
                    <li class="nav-item">
                        <a href="#" class="nav-link with-sub">
                            <i class="icon ion-ios-chatboxes-outline tx-24"></i>
                            <span>Support</span>
                        </a>
                        <ul class="nav-sub">
                            <li class="nav-item"><a href="form-elements.html" class="nav-link">Add New Query</a></li>
                            <li class="nav-item"><a href="form-layouts.html" class="nav-link"> My Queries </a></li>
                        </ul>
                    </li><!-- nav-item -->

                </ul>
            </div><!-- #mainMenu -->

        </div><!-- tab-content -->
    </div><!-- am-sideleft -->


    <div class="am-mainpanel">
        <div class="am-pagetitle">
            <h5 class="am-title">Dashboard</h5>
            <form id="searchBar" class="search-bar" action="http://themetrace.com/template/amanda/app/index.html">
                <div class="form-control-wrapper">
                    <input type="search" class="form-control bd-0" placeholder="Search...">
                </div><!-- form-control-wrapper -->
                <button id="searchBtn" class="btn btn-orange"><i class="fa fa-search"></i></button>
            </form><!-- search-bar -->
        </div><!-- am-pagetitle -->



        <main>
            @yield('content')
        </main>

        <div class="am-footer">
            <span>Copyright &copy; 2019. All Rights Reserved. Proaxive</span>
            <span>Powered by: Deploy Private Limited</span>
        </div>
    </div><!-- am-mainpanel -->

</div>



<script src="{{ asset('lib/jquery/jquery.js')}}"></script>
<script src="{{ asset('lib/popper.js/popper.js')}}"></script>
<script src="{{ asset('lib/bootstrap/bootstrap.js')}}"></script>
<script src="{{ asset('js/jquery.datetimepicker.full.js')}}"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/dataTables.material.min.js"></script>
<script src="{{ asset('lib/perfect-scrollbar/js/perfect-scrollbar.jquery.js')}}"></script>
<script src="{{ asset('lib/jquery-toggles/toggles.min.js')}}"></script>
<script src="{{ asset('lib/d3/d3.js')}}"></script>
<script src="{{ asset('lib/rickshaw/rickshaw.min.js')}}"></script>
<script src="{{ asset('lib/gmaps/gmaps.js')}}"></script>
<script src="{{ asset('lib/Flot/jquery.flot.js')}}"></script>
<script src="{{ asset('lib/Flot/jquery.flot.pie.js')}}"></script>
<script src="{{ asset('lib/Flot/jquery.flot.resize.js')}}"></script>
<script src="{{ asset('lib/flot-spline/jquery.flot.spline.js')}}"></script>
<script src="{{ asset('js/accounting.js')}}" type="text/javascript"></script>
<script src="{{ asset('js/custom_js.js')}}" type="text/javascript"></script>

<script src="{{ asset('js/main.js')}}"></script>
<script src="{{ asset('js/ResizeSensor.js')}}"></script>
<script src="{{ asset('js/dashboard.js')}}"></script>

</body>
</html>
