<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <title>{{ config('app.name', 'Laravel') }}</title>

      <!-- Favicon -->
      <link href="{{ asset('images/hrlogo-rounded.png') }}" rel="icon" type="image/png" sizes="16x16"/>

      <link rel="stylesheet" href="{{ asset('css/backend-plugin.min.css') }}">
      <link rel="stylesheet" href="{{ asset('css/backend.css?v=1.0.0') }}">
      <link rel="stylesheet" href="{{ asset('vendor/@fortawesome/fontawesome-free/css/all.min.css') }}">
      <link rel="stylesheet" href="{{ asset('vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css') }}">
      <link rel="stylesheet" href="{{ asset('vendor/remixicon/fonts/remixicon.css') }}">  </head>
  <body class=" ">
    <!-- loader Start -->
    <!-- <div id="loading">
          <div id="loading-center">
          </div>
    </div> -->
    <!-- loader END -->

      <div class="wrapper">
      <section class="login-content">
         <div class="container">
            <div class="row align-items-center justify-content-center height-self-center">
               <div class="col-lg-8">
                  <div class="card auth-card">
                     <div class="card-body p-0">
                        <div class="d-flex align-items-center auth-content">
                           <div class="col-lg-7 align-self-center">
                              <div class="p-3">

                                 @if(session("success"))
                                    <div class="alert alert-success rounded-0">{{ session("success") }}</div>
                                 @endif
                                 <div class="text-center">
                                    <img src="{{ asset('/images/appraisal_girl.png') }}" alt="appraisal_girl"  class="mx-auto" width="80px">
                                    <h1>HR Apprasial</h1>
                                 </div>
                                 <h4 class="my-4">Login</h4>
                                 <form action="{{ route('login') }}" method="POST">
                                    {{ csrf_field() }}
                                    <div class="row">
                                       <div class="col-lg-12">
                                          <div class="floating-label form-group">
                                                <input class="floating-input form-control"  name="login_value" value="{{ old('login_value') }}" placeholder="" autofocus required>
                                                <label>Email/ Ph-no/ Employee ID</label>
                                                @if($errors->has('login_value'))
                                                <div class="invalid-feedback  d-block">
                                                    <strong>{{ $errors->first('login_value') }}</strong>
                                                </div>
                                                @endif
                                          </div>

                                       </div>

                                       <div class="col-lg-12">
                                          <div class="floating-label form-group">
                                             <input class="floating-input form-control" name="password" type="password" placeholder="" required>
                                             <label>Password</label>
                                          </div>
                                          @error('password')
                                             <div class="invalid-feedback">
                                                <strong>{{ $message }}</strong>
                                             </div>
                                          @enderror
                                             
                                       </div>

                                       <div class="col-lg-6">
                                          <div class="custom-control custom-checkbox mb-3">
                                             <input type="checkbox" name="remember_me" class="custom-control-input" id="remember_me">
                                             <label class="custom-control-label control-label-1" for="remember_me">Remember Me</label>
                                          </div>
                                       </div>
                                       <div class="col-lg-6">
                                          <a href="{{ route('password.request') }}" class="text-primary float-right">Forgot Password?</a>
                                       </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Sign In</button>

                                 </form>
                              </div>
                           </div>
                           <div class="col-lg-5 content-right">
                              <img src="{{ asset('images/human-resources-illustration.jpg') }}" class="img-fluid image-right" alt="">
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </section>
      </div>

    <!-- Backend Bundle JavaScript -->
    <script src="{{ asset('js/backend-bundle.min.js') }}"></script>

    <!-- Table Treeview JavaScript -->
    <script src="{{ asset('js/table-treeview.js') }}"></script>

    <!-- Chart Custom JavaScript -->
    <script src="{{ asset('js/customizer.js') }}"></script>

    <!-- Chart Custom JavaScript -->
    <script async src="{{ asset('js/chart-custom.js') }}"></script>

    <!-- app JavaScript -->
    <script src="{{ asset('js/app.js') }}"></script>
  </body>
</html>
