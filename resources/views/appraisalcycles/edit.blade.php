@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Appraisal Cycle Edit</h4>
                    </div>
                </div>
            </div>



            <div class="col-lg-12">

            <div class="card border-0 rounded-0 shadow mb-4">
                <ul class="nav">
                    <li class="nav-item">
                        <button type="button" class="tablinks" onclick="gettab(event,'appraisalcycle')">Peroid</button>
                    </li>
                    <li class="nav-item">
                        <button type="button" id="autoclick"  class="tablinks" onclick="gettab(event,'peer_to_peer')">Peer-to-Peer</button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="tablinks" onclick="gettab(event,'appraisal')">Appraisal</button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="tablinks" onclick="gettab(event,'assesseesummary')">Assessee Summary</button>
                    </li>
                </ul>
                <h4 id="tab-title" class="tab-title"></h4>
                <div class="tab-content">

                        <div id="appraisalcycle" class="tab-pane">
                            {{-- <div class="col-lg-12 my-2 "> --}}
                                <form id="" action="{{route('appraisalcycles.update',$appraisalcycle->id)}}" method="POST">
                                    {{ csrf_field() }}
                                    @method('PUT')
                                    <div class="row align-items-start">
                                        <div class="col-md-3">
                                            <label for="name">Name <span class="text-danger">*</span></label>
                                            @error("name")
                                                    <span class="text-danger">{{ $message }}<span>
                                            @enderror
                                            <input type="text" name="name" id="name" class="form-control form-control-sm rounded-0" placeholder="Enter Employee Name" value="{{ old('name',$appraisalcycle->name) }}"/>
                                        </div>




                                        <div class="col-md-3">
                                            <label for="status_id">Status</label>
                                            <select name="status_id" id="status_id" class="form-control form-control-sm rounded-0">
                                                @foreach($statuses as $status)
                                                    <option value="{{$status['id']}}" {{ $status['id'] == old('status_id',$appraisalcycle->status_id) ? "selected" : "" }}>{{$status['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>


                                        <div class="col-md-3">
                                            <label for="start_date">Period Start Date <span class="text-danger">*</span></label>
                                            @error("start_date")
                                                    <span class="text-danger">{{ $message }}<span>
                                            @enderror
                                            <input type="date" name="start_date" id="start_date" class="form-control form-control-sm rounded-0" placeholder="Choose Start Date" value="{{ old('start_date',$appraisalcycle->start_date) }}"/>
                                        </div>


                                        <div class="col-md-3">
                                            <label for="end_date">Period End Date <span class="text-danger">*</span></label>
                                            @error("end_date")
                                                    <span class="text-danger">{{ $message }}<span>
                                            @enderror
                                            <input type="date" name="end_date" id="end_date" class="form-control form-control-sm rounded-0" placeholder="Choose Start Date" value="{{ old('start_date',$appraisalcycle->end_date) }}"/>
                                        </div>


                                        <div class="col-md-3">
                                            <label for="action_start_date">Action Start Date <span class="text-danger">*</span></label>
                                            @error("action_start_date")
                                                    <span class="text-danger">{{ $message }}<span>
                                            @enderror
                                            <input type="date" name="action_start_date" id="action_start_date" class="form-control form-control-sm rounded-0" placeholder="Choose Start Date" value="{{ old('action_start_date',$appraisalcycle->action_start_date) }}"/>
                                        </div>


                                        <div class="col-md-3">
                                            <label for="action_end_date">Action End Date <span class="text-danger">*</span></label>
                                            @error("action_end_date")
                                                    <span class="text-danger">{{ $message }}<span>
                                            @enderror
                                            <input type="date" name="action_end_date" id="action_end_date" class="form-control form-control-sm rounded-0" placeholder="Choose Start Date" value="{{ old('action_end_date',$appraisalcycle->action_end_date) }}"/>
                                        </div>



                                        <div class="col-md-3">
                                            <label for="action_start_time">Action Start Time <span class="text-danger">*</span></label>
                                            @error("action_start_date")
                                                    <span class="text-danger">{{ $message }}<span>
                                            @enderror
                                            <input type="time" name="action_start_time" id="action_start_time" class="form-control form-control-sm rounded-0" placeholder="Choose Start Date" value="{{ old('action_start_time',$appraisalcycle->action_start_time) }}"/>
                                        </div>


                                        <div class="col-md-3">
                                            <label for="action_end_time">Action End Time <span class="text-danger">*</span></label>
                                            @error("action_end_time")
                                                    <span class="text-danger">{{ $message }}<span>
                                            @enderror
                                            <input type="time" name="action_end_time" id="action_end_time" class="form-control form-control-sm rounded-0" placeholder="Choose Start Date" value="{{ old('action_end_time',$appraisalcycle->action_end_time) }}"/>
                                        </div>

                                        <div class="col-md-12">
                                            <label for="description">Description <span class="text-danger">*</span></label>
                                            @error("description")
                                                    <span class="text-danger">{{ $message }}<span>
                                            @enderror
                                            <textarea name="description" id="description" class="form-control form-control-sm rounded-0 fixedtxtareas" cols="30" rows="4" placeholder="Write Something....">{{ old('description',$appraisalcycle->description) }}</textarea>
                                        </div>



                                        {{--
                                        <div class="col-md-3">
                                            <label for="branch_id">Branch</label>
                                            <select name="branch_id" id="branch_id" class="form-control form-control-sm rounded-0">
                                                @foreach($branches as $branch)
                                                    <option value="{{$branch['branch_id']}}" {{ $branch['branch_id'] == old('branch_id',$appraisalcycle->branch_id) ? "selected" : "" }}>{{$branch['branch_name']}}</option>
                                                @endforeach
                                            </select>
                                        </div> --}}



                                        <div class="col-md-12 mt-2">

                                            <button type="button" id="back-btn" class="btn btn-light btn-sm rounded-0" onclick="window.history.back();">Back</button>

                                            <button type="submit" class="btn btn-primary btn-sm rounded-0">Update</button>
                                        </div>
                                    </div>
                                </form>
                            {{-- </div> --}}
                        </div>


                        <div id="peer_to_peer" class="tab-pane">
                            <div class="row">

                                <div class="col-lg-3">
                                    {{-- <h4>All Accessors</h4> --}}
                                    {{-- <input type="text" id="search-input" class="form-control form-control-sm rounded-0" placeholder="Search...." />
                                    <div id="treeview">

                                        <ul id="assessors-tree">
                                            @foreach($users as $user)
                                                <li><label for="{{ $user->id }}">{{ $user->name }}</label></li>
                                            @endforeachuser-list

                                        </ul>
                                    </div> --}}


                                    <div class="header">
                                        <h4 class="title">All Accessors</h4>
                                        <small class="subtitle">Search by name or employee id</small>
                                        <input type="text" name="search" id="search" class="search" placeholder="Search...."/>
                                    </div>
                                    <ul id="result" class="user-list">
                                        @foreach($users as $user)
                                        {{-- <img src="${user.picture.large}" alt="${user.name.first}"/> --}}
                                        <div class="user-info">
                                            <li data-user_id = {{ $user->id }}>
                                                <i class="ri-folder-4-line"></i>
                                                    <h4>{{ $user->name }} ( {{ $user->employee_id }} )</h4>
                                                {{-- <p>${user.location.city} , ${user.location.country}</p> --}}
                                            </li>

                                        </div>
                                        @endforeach


                                        {{-- <li><h3>Loading...</h3></li> --}}
                                    </ul>

                                    <form id="peer_to_peer_form" action="{{ route('peertopeers.create') }}" method="" class="my-2">
                                        <input type="hidden" id="assessor_user_id" name="assessor_user_id" class="" value=""/>
                                        <input type="hidden" id="appraisal_cycle_id" name="appraisal_cycle_id" class="" value="{{ $appraisalcycle->id }}"/>
                                        <button type="submit" class="btn new_btn">New</button>
                                    </form>
                                </div>

                                <div class="col-lg-9">
                                    <div class="table-responsive rounded mb-3">
                                        <table id="peertopeer" class="table mb-0" >
                                            <thead class="bg-white text-uppercase">
                                                <tr class="ligth ligth-data">
                                                    <th>No</th>
                                                    <th>Assessor Name</th>
                                                    <th>Assessee Name</th>
                                                    <th>Department</th>
                                                    <th>Branch</th>
                                                    <th>Position Level</th>
                                                    <th>Position</th>
                                                    <th>Assessment-form Category</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="ligth-body">

                                            </tbody>
                                        </table>
                                        <div class="d-flex justify-content-center">
                                            {{-- {{ $genders->appends(request()->all())->links("pagination::bootstrap-4") }} --}}
                                        </div>


                                    </div>
                                </div>
                            </div>

                        </div>

                        <div id="appraisal" class="tab-pane">

                            <div class="row">

                               <div class="col-lg-12">
                                <h4 class="title">All Participants</h4>

                                <table id="peertopeer" class="table mb-0" >
                                    <thead class="bg-white text-uppercase">
                                        <tr class="ligth ligth-data">
                                            <th>No</th>
                                            <th>Employee Name</th>
                                            <th>Employee Code</th>
                                            <th>Sent / All Forms</th>
                                            <th>Progress</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="ligth-body">
                                            @foreach($participantusers as $idx=>$participantuser)
                                                <tr>
                                                    <td>{{ ++$idx }}</td>
                                                    {{-- <td>{{$idx + $participantuser->firstItem()}}</td> --}}
                                                    <td>{{ $participantuser->employee->employee_name }}</td>
                                                    <td>{{ $participantuser->employee->employee_code }}</td>
                                                    <td>{{ $participantuser->getAppraisalFormCount($appraisalcycle->id) }} / {{  $participantuser->getAllFormCount($appraisalcycle->id) }} </td>
                                                    <td class="">
                                                        {{-- <div class="d-flex justify-content-center align-items-center">
                                                            <i class="fas fa-check-circle fa-2x text-success mr-2"></i> Complete </td>
                                                        </div> --}}
                                                        <div class="d-flex justify-content-center align-items-center">
                                                        <div id="progresses"  style="background : conic-gradient(steelblue {{$participantuser->getSentPercentage($appraisalcycle->id)}}%,#eee {{$participantuser->getSentPercentage($appraisalcycle->id)}}%)">
                                                            <span id="progressvalues">{{$participantuser->getSentPercentage($appraisalcycle->id)}}%</span>
                                                        </div>
                                                        </div>
                                                    </td>

                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center">
                                                            <form id="appraisalform" action="{{ route('appraisalforms.create') }}" method="GET">
                                                                <input type="hidden" name="assessor_user_id" value="{{ $participantuser->id }}">
                                                                <input type="hidden" name="appraisal_cycle_id" value="{{ $appraisalcycle->id }}"/>
                                                                <a href="javascript:void(0);" class="text-primary mr-2" title="Send" onclick="$(this).closest('form').submit()"><i class="fas fa-paper-plane"></i></a>
                                                            </form>


                                                            <form action="{{ route('appraisalforms.index') }}" method="GET">
                                                                <input type="hidden" name="filter_assessor_user_id" value="{{ $participantuser->id }}">
                                                                <input type="hidden" name="filter_appraisal_cycle_id" value="{{ $appraisalcycle->id }}"/>
                                                                <a href="javascript:void(0);" class="text-primary mr-2" title="Open" onclick="$(this).closest('form').submit()"><i class="far fa-envelope-open"></i></i></a>
                                                            </form>
                                                        </div>

                                                    </td>


                                                </tr>
                                            @endforeach
                                    </tbody>
                                </table>
                               </div>
                            </div>
                        </div>

                        <div id="assesseesummary" class="tab-pane">
                            <div class="row">

                                <div class="col-lg-12">
                                 <h4 class="title">All Assessees</h4>

                                 <table id="peertopeer" class="table mb-0" >
                                     <thead class="bg-white text-uppercase">
                                         <tr class="ligth ligth-data">
                                             <th>No</th>
                                             <th>Employee Name</th>
                                             <th>Employee Code</th>
                                             <th>Action</th>
                                         </tr>
                                     </thead>
                                     <tbody class="ligth-body">
                                             @foreach($assesseeusers as $idx=>$assesseeuser)
                                                 <tr>
                                                     <td>{{ ++$idx }}</td>
                                                     {{-- <td>{{$idx + $participantuser->firstItem()}}</td> --}}
                                                     <td>{{ $assesseeuser->employee->employee_name }}</td>
                                                     <td>{{ $assesseeuser->employee->employee_code }}</td>
                                                    <td>
                                                            <a href="{{ route('assesseesummary.review',["assessee_user_id"=>$assesseeuser->id,"appraisal_cycle_id"=>$appraisalcycle->id]) }}" class="text-primary mr-2" title="Open" onclick=""><i class="far fa-eye"></i></i></a>
                                                    </td>


                                                 </tr>
                                             @endforeach
                                     </tbody>
                                 </table>
                                </div>
                             </div>
                        </div>
                </div>
            </div>
            </div>






            <div class="col-md-12 mb-2">
                @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if ($message = Session::get('error'))
                <div class="alert alert-danger">
                    <p>{{ $message }}</p>
                </div>
                @endif
                @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
                @endif


                @if($getvalidationerrors = Session::get('validation_errors'))
                    {{-- <li>{{ Session::get('validation_errors') }}</li> --}}
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your excel file at row {{ json_decode($getvalidationerrors)->row }}.<br><br>
                        <ul>
                            {{-- {{ dd(json_decode($getvalidationerrors)) }} --}}
                            @foreach ($validationerrors = json_decode($getvalidationerrors) as $idx=>$import_errors)
                                {{-- {{dd($errors)}} --}}
                                @if($idx != 'row')
                                    @foreach($import_errors as $import_error)
                                        <li>{{ $import_error }}</li>
                                    @endforeach
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif
           </div>
            {{-- <div class="col-lg-12 d-flex mb-4">
                <div class="form-row col-md-2">
                    <label> {{__('branch.branch_name')}} </label>
                    <input type="input" class="form-control" id="branch_name" value="">
                </div>
                <div class="form-row col-md-2">
                    <label> {{__('branch.branch_short_name')}}</label>
                    <input type="input" class="form-control" id="branch_short_name" value="">
                </div>
                <button id="branch_search" class="btn btn-primary document_search ml-2 mr-2 mt-4">{{__('button.search')}}</button>
            </div> --}}
        </div>
    </div>

</div>

</div>

<!-- START MODAL AREA -->



<!-- End MODAL AREA -->
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/jstreerepo/dist/themes/default/style.min.css')}}"/>
@endsection
@section('js')
    <script src="{{ asset('assets/libs/jstreerepo/dist/jstree.min.js') }}" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        $("#status_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Division',
            searchField: ["value", "label"]
        });




        $("#start_date,#end_date,#action_start_date,#action_end_date").flatpickr({
            dateFormat: "Y-m-d",
            {{-- minDate: "today", --}}
            {{-- maxDate: new Date().fp_incr(30) --}}
       });

       $("#action_start_time,#action_end_time").flatpickr({
            enableTime: true, // Enable time picker
            noCalendar: true, // Hide the calendar if only time is needed
            dateFormat: "H:i", // Format for hours and minutes
            time_24hr: true // Use 24-hour format
        });




        //Start change-btn
        $(document).on("change",".statuschange-btn",function(){

             var getid = $(this).data("id");
             // console.log(getid);
             {{-- console.log(getid); --}}

             var setstatus = $(this).prop("checked") === true ? 1 : 2;
             {{-- console.log(setstatus); --}}

             $.ajax({
                  url:"/positionsstatus",
                  type:"POST",
                  dataType:"json",
                  data:{
                        "id":getid,
                        "status_id":setstatus,
                        "_token": '{{ csrf_token()}}'
                    },
                  success:function(response){
                       console.log(response); // {success: 'Status Change Successfully'}
                       console.log(response.success); // Status Change Successfully

                       Swal.fire({
                            title: "Updated!",
                            text: "Status Updated Successfully",
                            icon: "success"
                       });
                  },
                  error:function(response){
                    console.log(response);
                  }
             });
        });
        // End change btn

        {{-- Start Preview Image --}}

        var previewimages = function(input, output) {
            if (input.files) {
                var totalfiles = input.files.length;

                if (totalfiles > 0) {
                    $('.gallery').addClass('removetxt');
                } else {
                    $('.gallery').removeClass('removetxt');
                }

                $(output).html(""); // Clear previous previews

                let html = ''
                for (let i = 0; i < totalfiles; i++) {
                    var file = input.files[i];
                    var filereader = new FileReader();

                    filereader.onload = function(e) {
                        let fileType = file.type;
                        console.log("File Type:", fileType);

                        {{-- if (fileType === 'application/pdf') {
                            // Show PDF icon
                            $($.parseHTML('<img>')).attr({
                                'src': '{{ asset('images/pdf.png') }}',
                                'title': file.name
                            }).appendTo(output);
                        } else if (
                            fileType === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ||
                            fileType === 'application/vnd.ms-excel'
                        ) {
                            // Show Excel icon
                            $($.parseHTML('<img>')).attr({
                                'src': '{{ asset('images/excel.png') }}',
                                'title': file.name
                            }).appendTo(output);
                        } else {
                            // Show normal image preview
                            $($.parseHTML('<img>')).attr({
                                'src': e.target.result,
                                'title': file.name
                            }).appendTo(output);
                        } --}}

                        if (
                            fileType === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ||
                            fileType === 'application/vnd.ms-excel'
                        ) {
                            // Show Excel icon
                            {{-- $($.parseHTML('<img>')).attr({
                                'src': '{{ asset('images/excel.png') }}',
                                'title': file.name
                            }).appendTo(output); --}}


                            html = `
                                <img src="{{ asset('images/excel.png') }}" title=${file.name} />
                            `;
                            $(output).append(html);
                        }else{
                            Swal.fire({
                                title: "Invalid File!!",
                                text: "Only Excel files (.xls, .xlsx) are allowed.",
                                icon: "question"
                            });


                        html = `
                            <img src="{{ asset('images/file-invalid.png') }}" title=${file.name} />
                        `;
                        $(output).append(html);
                        }

                    };

                    filereader.readAsDataURL(file);
                }
            }
            $('#file').change(function() {
                previewimages(this, '.gallery');
            });

            {{-- End Preview Image --}}

        };



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


        document.getElementById('tab-title').textContent = evn.target.textContent;

    }

    document.getElementById('autoclick').click();
    // End Tag Box



    {{-- Start User List Filter --}}
    const filterel = document.getElementById('search');
    const resultel = document.getElementById('result');

    const totalusers = 50;

    const listitems = document.querySelectorAll('.user-info li');

    async function getdata(){

        // Method 1
        // fetch(url)
        // .then(res=>res.json())
        // .then(data => data.results)



        // Method 2
        const res = await fetch(`https://randomuser.me/api/?results=${totalusers}`);
        // console.log(res);

        // const data = await res.json();
        // console.log(data);
        // console.log(data.results);
        // other api can
        // console.log(data[results]);


        const {results} = await res.json();
        // console.log(results);

        resultel.innerText = '';

        results.forEach(user => {

            // console.log(user);

            const li = document.createElement('li');

            li.innerHTML = `

            <img src="${user.picture.large}" alt="${user.name.first}"/>
            <div class="user-info">
                <h4>${user.name.title}. ${user.name.first} ${user.name.last}</h4>
                <p>${user.location.city} , ${user.location.country}</p>
            </div>


            `;

            resultel.appendChild(li);

            listitems.push(li);

            // console.log(listitems);

        });
    }
    {{-- getdata(); --}}


    filterel.addEventListener('input',(e)=>filterdata(e.target.value));

    function filterdata(search){
        // console.log(search);
        listitems.forEach(listitem=>{
            // console.log(listitem);
            if(listitem.innerText.toLocaleLowerCase().includes(search.toLowerCase())){
                listitem.classList.remove('hide');
            }else{
                listitem.classList.add('hide');
            }
        });
    }

    let tableBody = document.querySelector("#peertopeer tbody");
    $(document).on('click',".user-info li",function(){
        let getuser_id = $(this).data('user_id');
        {{-- let getassformcat_id =  --}}
        console.log(getuser_id);
        $(".user-info li").removeClass('active');
        $(this).toggleClass('active');
        $('#assessor_user_id').val(getuser_id);


        $.ajax({
            url: `/getAssessorAssessees`,
            type: "GET",
            dataType: "json",
            data: $('#peer_to_peer_form').serialize(),
            success: function (response) {
                console.log(response);

                let html = '';
                const peertopeers = response;

                peertopeers.forEach(function(peertopeer,idx){
                    html += `
                    <tr>
                        <td>
                            ${++idx}
                        </td>
                        <td>${peertopeer.assessoruser.employee.employee_name}</td>
                        <td>${peertopeer.assesseeuser.employee.employee_name}</td>
                        <td>${peertopeer.assesseeuser.employee.department.name}</td>
                        <td>${peertopeer.assesseeuser.employee.branch.branch_name}</td>
                        <td>${peertopeer.assesseeuser.employee.positionlevel.name}</td>
                        <td>${peertopeer.assesseeuser.employee.position.name}</td>
                        <td style="width:150px;">${peertopeer.assformcat.name}</td>
                        <td class="text-center">
                            <a href="#" class="text-danger ms-2 delete-btns" data-idx="${idx}"><i class="fas fa-trash-alt"></i></a>
                            <form id="formdelete-${idx}" class="" action="/peertopeers/${peertopeer.id}" method="POST">
                                @csrf
                                @method("DELETE")
                            </form>
                        </td>

                    </tr>`;
                })


                tableBody.innerHTML = html;


            },
            error: function (response) {
                console.log("Error:", response);
            }
        });



    });


    // Start Delete Item
    $(document).on("click",".delete-btns",function(){
        console.log('hay');

        var getidx = $(this).data("idx");
        {{-- // console.log(getidx); --}}


        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $('#formdelete-'+getidx).submit();

            }
        });


   });
   // End Delete Item
    {{-- End User List Filter --}}



    {{-- Start Assessor List Filter --}}
    const afilterel = document.getElementById('asearch');
    const aresultel = document.getElementById('aresult');


    const alistitems = document.querySelectorAll('.assessor-info li');
    {{-- End Assessor List Filter --}}

    afilterel.addEventListener('input',(e)=>afilterdata(e.target.value));

    function afilterdata(search){
        // console.log(search);
        alistitems.forEach(listitem=>{
            // console.log(listitem);
            if(listitem.innerText.toLocaleLowerCase().includes(search.toLowerCase())){
                listitem.classList.remove('hide');
            }else{
                listitem.classList.add('hide');
            }
        });
    }


    $(document).on('click',".assessor-info li",function(){
        let getuser_id = $(this).data('user_id');
        $(".assessor-info li").removeClass('active');
        $(this).toggleClass('active');
        $('#aassessor_user_id').val(getuser_id);


        $.ajax({
            url: `/getAssessorAssessees`,
            type: "GET",
            dataType: "json",
            data: $('#peer_to_peer_form').serialize(),
            success: function (response) {
                console.log(response);

                let html = '';
                const peertopeers = response;

                peertopeers.forEach(function(peertopeer,idx){
                    html += `
                    <tr>
                        <td>
                            ${++idx}
                        </td>
                        <td>${peertopeer.assessoruser.employee.employee_name}</td>
                        <td>${peertopeer.assesseeuser.employee.employee_name}</td>
                        <td>${peertopeer.assesseeuser.employee.department.name}</td>
                        <td>${peertopeer.assesseeuser.employee.branch.branch_name}</td>
                        <td>${peertopeer.assesseeuser.employee.positionlevel.name}</td>
                        <td>${peertopeer.assesseeuser.employee.position.name}</td>
                        <td style="width:150px;">${peertopeer.assformcat.name}</td>


                    </tr>`;
                })


                tableBody.innerHTML = html;


            },
            error: function (response) {
                console.log("Error:", response);
            }
        });
    });



</script>
@stop
