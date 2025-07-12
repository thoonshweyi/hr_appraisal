@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">

            <div class="col-md-4 col-lg-3 mb-2">
                <h6>Info</h6>
                <div class="card border-0 rounded-0 shadow position-relative">
                    <button type="button" id="uploadbtn" class="btn btn-primary btn-sm text-sm rounded-0">Upload</button>
                        <div class="profile-cover">
                            <img src="{{ asset('images/PRO-1-Global-Logo.png') }}" class="img-fluid center w-100" alt="profile-image">
                        </div>

                        <div class="card-body position-relative">

                               <div class="d-flex flex-column align-items-center profileimages">

                                    <form id="empimageform" action="{{ route('employees.updateprofilepicture',$employee['id']) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="form-group col-md-12 text-center">
                                            <label for="image" class="profilegallery">
                                                @if($employee['image'])
                                                        <img src="{{ asset($employee['image']) }}" alt="{{ $employee->name }}" class="img-thumbnail" width="100" height="100"/>
                                                @else
                                                        <img src="{{ asset('images/user/default.jpg') }}" alt="{{ $employee->name }}" class="img-thumbnail" width="100" height="100"/>
                                                @endif
                                            </label>
                                            <input type="file" name="image" id="image" class="form-control form-control-sm rounded-0" hidden/>
                                        </div>

                                    </form>
                               </div>

                               <div>
                                    {{-- <a href="javascript:void(0);" id="notiunsubbtn" class="float-right "><i class="far fa-bell-slash"></i></a> --}}
                                    <h4 class="my-1">{{ $employee->employee_name }}</h4>
                                    <h3 class="my-1">({{ $employee->position->name }})</h3>
                               </div>

                               <div class="mb-5">
                                <p class="text-small text-muted text-uppercase mb-2">Employee Info</p>

                                @if($employee)
                                <div class="row g-0 mb-2">
                                     <div class="col-auto me-2">
                                          <i class="fas fa-address-card"></i>
                                     </div>
                                     <div class="col">
                                          <a href="">{{ $employee['employee_code'] }}</a>
                                     </div>
                                </div>
                                <div class="row g-0 mb-2">
                                    <div class="col-auto me-2">
                                        <i class="far fa-star"></i>
                                    </div>
                                    <div class="col">
                                        {{ $employee->positionlevel['name'] }}
                                    </div>
                               </div>
                                <div class="row g-0 mb-2">
                                    <div class="col-auto me-2">
                                        <i class="far fa-building"></i>
                                    </div>
                                    <div class="col">
                                        {{ $employee->branch['branch_name'] }}
                                    </div>
                               </div>
                               <div class="row g-0 mb-2">
                                    <div class="col-auto me-2">
                                        <i class="ri-government-line"></i>
                                    </div>
                                    <div class="col">
                                        {{ $employee->department['name'] }}
                                    </div>
                                </div>


                                <div class="row g-0 mb-2">
                                    <div class="col-auto me-2">
                                       <i class="ri-calendar-todo-line"></i>
                                    </div>
                                    <div class="col">
                                         {{ date('d M Y h:i:s A',strtotime($employee['beginning_date'])) }}
                                    </div>
                               </div>
                                <div class="row g-0 mb-2">
                                    <div class="col-auto me-2">
                                        <i class="ri-calendar-todo-line"></i>
                                    </div>
                                    <div class="col">{{ $employee['nrc'] }}</div>
                               </div>

                                <div class="row g-0 mb-2">
                                     <div class="col-auto me-2">
                                          <i class="fas fa-venus"></i>
                                     </div>
                                     <div class="col">{{ $employee['gender']['name'] }}</div>
                                </div>
                                <div class="row g-0 mb-2">
                                     <div class="col-auto me-2">
                                          <i class="fas fa-flag-checkered"></i>
                                     </div>
                                     <div class="col">{{ $employee['age'] }} years old</div>
                                </div>

                                {{-- <div class="row g-0 mb-2">
                                     <div class="col-auto me-2">
                                          <i class="fas fa-flag"></i>
                                     </div>
                                     <div class="col">{{ $employee['city']['name'] }} | {{ $employee['country']['name'] }}</div>
                                </div> --}}



                                <div class="row g-0 mb-2">
                                     <div class="col-auto me-2">
                                          <i class="fas fa-calendar-alt"></i>
                                     </div>
                                     <div class="col">
                                          {{ date('d M Y h:i:s A',strtotime($employee['updated_at'])) }}
                                     </div>
                                </div>
                                @endif


                           </div>



                        </div>
                </div>

            </div>
            <div class="col-md-8 col-lg-9">



                <h6>Additional Info</h6>
                <div class="card border-0 rounded-0 shadow mb-4">
                     {{-- <ul class="nav">

                          <li class="nav-item">
                               <button type="button" id="autoclick" class="tablinks" onclick="gettab(event,'signintab')">Sign In</button>
                          </li>

                     </ul>

                     <div class="tab-content">


                          <div id="signintab" class="tab-pane">
                               <h6>Sign-In Password</h6>
                               <div class="col-md-4 mx-auto">
                                   <form class="mt-3" action="{{ route('user.update_profile')}}" method="POST">
                                       @csrf
                                       <div class="form-group mb-3">
                                           <input type="password" id="current_password" name="cpass" class="form-control @error('cpass') is-invalid @enderror" placeholder="Current Password" value="{{ old('cpass') }}"/>
                                           @error('cpass')
                                           <span class="invalid-feedback">
                                               <strong>{{ $message }}</strong>
                                           </span>
                                           @enderror
                                       </div>

                                       <div class="form-group mb-3">
                                           <input type="password" name="npass" class="form-control @error('npass') is-invalid @enderror" placeholder="New Password" value="{{ old('npass') }}"/>
                                           @error('npass')
                                           <span class="invalid-feedback">
                                               <strong>{{ $message }}</strong>
                                           </span>
                                           @enderror
                                       </div>

                                       <div class="form-group mb-3">
                                           <input type="password" name="vpass" class="form-control @error('vpass') is-invalid @enderror" placeholder="Confirm Password" value="{{ old('vpass') }}"/>
                                           @error('vpass')
                                           <span class="invalid-feedback">
                                               <strong>{{ $message }}</strong>
                                           </span>
                                           @enderror
                                       </div>

                                       <div class="float-end mb-3">
                                           <button type="submit" class="btn btn-info btn-sm rounded-0">Save Change</button>
                                       </div>
                                   </form>
                               </div>


                          </div>



                     </div> --}}


                    <h4>Criteria Set List</h4>
                    <table class="table mb-0" id="branch_list">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>No</th>
                                <th>Name</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @foreach($employee->emppuser?->getAssFormCats() as $idx=>$assformcat)
                            <tr>
                                <td>{{ ++$idx }}</td>
                                <td>{{$assformcat->name}}</td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
           </div>



        </div>

   </div>
</div>

@endsection

<script src="https://cdn.jsdelivr.net/npm/@pusher/push-notifications-web@1.1.0/dist/push-notifications-cdn.js"></script>
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


     // Start Tag Box
     var gettablinks = document.getElementsByClassName('tablinks');  //HTMLCollection
        var gettabpanes = document.getElementsByClassName('tab-pane');
        // console.log(gettabpanes);

        var tabpanes = Array.from(gettabpanes);

        function gettab(evn,linkid){

        tabpanes.forEach(function(tabpane){
             tabpane.style.display = 'none';
        });

        for(var x = 0 ; x < gettablinks.length ; x++){
             gettablinks[x].className = gettablinks[x].className.replace(' active','');
        }


        document.getElementById(linkid).style.display = 'block';


        // evn.target.className += ' active';
        // evn.target.className = evn.target.className.replace('tablinks','tablinks active');
        // evn.target.classList.add('active');

        // evn.target = evn.currentTarget
        evn.currentTarget.className += ' active';

        }

        document.getElementById('autoclick').click();
   // End Tag Box


   // Start Single Image Preview
   var previewimages = function(input,output){

        // console.log(input.files);

        if(input.files){
             var totalfiles = input.files.length;
             // console.log(totalfiles);
             if(totalfiles > 0){
                  $('.profilegallery').addClass('removetxt');
             }else{
                  $('.profilegallery').removeClass('removetxt');
             }
             for(var i = 0 ; i < totalfiles ; i++){
                  var filereader = new FileReader();


                  filereader.onload = function(e){
                       $(output).html("");
                       $($.parseHTML('<img>')).attr('src',e.target.result).appendTo(output);
                  }

                  filereader.readAsDataURL(input.files[i]);

             }
             $('#uploadbtn').show();
        }

   }

   $('#image').change(function(){
        previewimages(this,'.profilegallery');
   });
   // End Single Image Preview


    {{-- Start Upload Btn --}}
   $('#uploadbtn').click(function(){
        $('#empimageform').submit();
   })
   {{-- End  Upload Btn --}}


   navigator.serviceWorker.register('/service-worker.js')
   .then((registration) => {
       console.log("Service Worker registered with scope:", registration.scope);
   })
   .catch(console.error);
   document.getElementById('notiunsubbtn').addEventListener('click', async (event) => {
    {{-- console.log('hay'); --}}

    event.preventDefault();
    const beamsClient = new PusherPushNotifications.Client({
        instanceId: "3c970f94-fe4f-491d-99ec-f82430cae1cb"
    });

    await beamsClient.stop(); // Stops push notifications
    await beamsClient.clearAllState(); // Clears the device subscription


    console.log("User unsubscribed from notifications");
    toastr.success('User unsubscribed from notifications', 'Successful')

});
    </script>
@stop
