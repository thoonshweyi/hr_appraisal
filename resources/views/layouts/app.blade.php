<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <title>{{ config('app.name', 'Laravel') }}</title>
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <!-- Include necessary CSS and JavaScript files for SweetAlert -->
      <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css')}}">

      <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
      {{-- <script src="{{asset('js/jquery.min.js')}}" type="text/javascript"></script> --}}

    <script src="{{asset('js/sweetalert2.min.js')}}"></script>
    {{-- flatpickr css1 js1 --}}
    <link  href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css"/>

    {{-- selectize css 1 js1 --}}
    <link  href="{{ asset('assets/libs/selectize/selectize.min.css') }}" rel="stylesheet" type="text/css"/>


      <!-- Favicon -->
      <link rel="shortcut icon" href="{{ asset('images/hrlogo-rounded.png') }}" />

      <link rel="stylesheet" href="{{ asset('css/backend-plugin.min.css') }}">
      <link rel="stylesheet" href="{{ asset('css/backend.css?v=1.0.0') }}">
      <link rel="stylesheet" href="{{ asset('css/select2.css')}}"/>
      <link rel="stylesheet" href="{{ asset('css/select2.min.css')}}"/>
      <link rel="stylesheet" href="{{ asset('css/app.css') }}">
      <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/rowReorder.dataTables.min.css') }}">
      <link rel="stylesheet" href="{{ asset('css/responsive.dataTables.min.css') }}">

      <link rel="stylesheet" href="{{ asset('vendor/@fortawesome/fontawesome-free/css/all.min.css') }}">
      <link rel="stylesheet" href="{{ asset('vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css') }}">
      <link rel="stylesheet" href="{{ asset('vendor/remixicon/fonts/remixicon.css') }}">




      @include('sweetalert::alert')
    <style>
        .deleted-row {
            background-color: #F1F5F8; /* Set the grey color for deleted rows */
        }

        .select2-container--default .select2-selection--multiple .select2-selection__rendered li {
            height: 40px;
            list-style: none;
            text-align: center;

        }

        .select2-container--default .select2-selection--multiple {
            background-color: white;
            border: 1px solid #aaa;
            border-radius: 8px;
            cursor: text;
            width:200px;
            margin-top: 10px;
        }

        .select2-selection{
          width: 100% !important;
          margin:0 !important;
        }

    </style>

    <style>
        .alert{
            font-size: 18px !important;
        }
    </style>

    </head>
  <body class="  ">
    <!-- loader Start -->
    <!-- <div id="loading">
          <div id="loading-center">
          </div>
    </div> -->
    <!-- loader END -->
    <!-- Wrapper Start -->
    <div class="wrapper">
        <div class="iq-sidebar  sidebar-default ">
            <div class="iq-sidebar-logo d-flex align-items-center justify-content-between">
                <a href="{{ route('home') }}" class="header-logo">
                    <img src="{{ asset('images/hrlogo.jpg') }}" class="img-fluid rounded-normal light-logo" alt="logo"><h5 class="logo-title light-logo ml-3">{{ config('app.name', 'Laravel') }}</h5>
                </a>
                <div class="iq-menu-bt-sidebar ml-0">
                    <i class="las la-bars wrapper-menu"></i>
                </div>
            </div>

            @includeIf('layouts.nav')
        </div>
         <!-- header -->
         @includeIf('layouts.header')
         <!-- header -->
         @yield('content')

    </div>

    <!-- Backend Bundle JavaScript -->
    <script src="{{ asset('js/backend-bundle.min.js') }}"></script>

    <!-- Table Treeview JavaScript -->
    <script src="{{ asset('js/table-treeview.js') }}"></script>

    <!-- Chart Custom JavaScript -->
    <script src="{{ asset('js/customizer.js') }}"></script>

    <!-- select 2 -->
    <script src="{{ asset('js/select2.min.js') }}"></script>

    {{-- flatpickr css1 js1 --}}
     <script src="{{ asset('assets/libs/flatpickr/flatpickr.js') }}" type="text/javascript"></script>

    {{-- selectize css 1 js1 --}}
    <script  src="{{ asset('assets/libs/selectize/selectize.min.js') }}" type="text/javascript"></script>

    <!-- Chart Custom JavaScript -->
    <script async src="{{ asset('js/chart-custom.js') }}"></script>

    <!-- app JavaScript -->
    <script src="{{ asset('js/app.js') }}"></script>

    <script src="{{ asset('js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.rowReorder.min.js') }}"></script>

    <!-- Dropzone CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">

<!-- Dropzone JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>


    <script>
      $(document).ready(function() {
        function sendRequest(){
          $.ajax({
              url: "/notifications",
              success:
              function(result){
                 if(result == 0){
                   $('#notification_count').hide();
                  }else{
                    $('#notification_count').show();
                    $('#notification_count').text(result);
                  }
                  setTimeout(function(){
                      sendRequest(); //this will send request again and again in every 10s;
                  }, 10000);
              }
          });
        }
        sendRequest();
      });


    </script>
    @yield('js')
  </body>

</html>
