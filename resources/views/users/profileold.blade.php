@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
               <div class="card car-transparent">
                  <div class="card-body p-0">
                     <div class="profile-image position-relative" style="display: flex; justify-content: center;">
                        <img src="{{ asset('images/PRO-1-Global-Logo.png') }}" class="img-fluid center rounded w-50" alt="profile-image">
                     </div>
                  </div>
               </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                    <div class="card card-block card-stretch card-height">
                        <div class="card-body">
                            <div style="display: flex; justify-content: center;">
                                @foreach ($errors->all() as $error)
                                    <p class="text-danger">{{ $error }}</p>
                                @endforeach
                            </div> 
                            <div class="d-flex align-items-center mb-3" style="display: flex; justify-content: center;">
                             
                                <div class="profile-img position-relative">
                                    <img src="{{ asset('images/user/' . Auth::user()->roles->pluck('name')->first() .'.png') }}" class="img-fluid rounded avatar-110" alt="profile-image">
                                </div>
                                <div class="ml-3">
                                    <h4 class="mb-1">{{Auth::user()->name}}</h4>
                                    <p class="mb-2">Role : {{Auth::user()->roles->pluck('name')->first()}}</p>
                                    <p class="mb-2">Emoplyee ID : {{Auth::user()->employee_id}}</p>
                                </div>
                                <div class="ml-3">
                                    <button id="show_change_password_form" class="btn btn-primary">Change Password</button>
                                </div>
                                <div class="ml-3 col-lg-4" id="change_password_form">
                                    <form action="{{ route('user.update_profile')}}"  method="POST" onsubmit="return validateForm()">
                                    @csrf
                                        <div class="form-group">
                                            <label for="cpass">Current Password:</label>
                                            <input type="Password" class="form-control" name="cpass" id="cpass" value="" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="npass">New Password:</label>
                                            <input type="Password" class="form-control" name="npass" id="npass" value="" required minlength="6">
                                        </div>
                                        <div class="form-group">
                                            <label for="vpass">Verify Password:</label>
                                            <input type="Password" class="form-control" name="vpass" id="vpass" value="" required minlength="6">
                                        </div>
                                        <button type="submit" class="btn btn-primary mr-2">Submit</button>
                                        <button type="buttom" class="btn iq-bg-danger" id="hide_change_password_form">Back</button>
                                    </form>
                                </div>
                            </div>
                            
                        </div>
                    <div>
            </div>
        </div>
        
    </div>
</div>

@endsection
@section('js')
    <script>
        $(document).ready(function () {
            $('#change_password_form').hide();
            $('#show_change_password_form').on('click', function(e) {
                $('#change_password_form').show();
                $('#show_change_password_form').hide();
            })
            $('#hide_change_password_form').on('click', function(e) {
                $('#change_password_form').hide();
                $('#show_change_password_form').show();
                event.preventDefault();
            })
        });
    </script>
@stop