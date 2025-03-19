@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            {{-- <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Appraisal Cycle Edit</h4>
                    </div>
                </div>
            </div> --}}



            <div class="col-lg-12 pt-0">

            <div class="card border-0 rounded-0 shadow mb-4">
                <ul class="nav">
                    <li class="nav-item">
                        <button type="button" class="tablinks" onclick="gettab(event,'appraisalcycle')">Peroid</button>
                    </li>
                    <li class="nav-item">
                        <button type="button" id="autoclick" class="tablinks" onclick="gettab(event,'peer_to_peer')">Peer-to-Peer</button>
                    </li>
                    <li class="nav-item">
                        <button type="button"  class="tablinks" onclick="gettab(event,'appraisal')">Appraisal</button>
                    </li>
                    <li class="nav-item">
                        <button type="button"  class="tablinks" onclick="gettab(event,'assesseesummary')">Assessee Summary</button>
                    </li>
                </ul>
                <h4 id="tab-title" class="tab-title"></h4>
                <div class="col-lg-12 tab-filter">
                    <form id="searchnfilterform" class="" action="" method="GET">
                        @csrf
                        <div class="row align-items-end justify-content-start">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {{-- <label for="filter_employee_name">Enployee Name <span class="text-danger">*</span></label> --}}
                                    <input type="text" name="filter_employee_name" id="filter_employee_name" class="form-control form-control-sm rounded-0 filter_input" placeholder="Enter Employee Name" value="{{ request()->filter_name }}"/>
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    {{-- <label for="filter_employee_code">Enployee Code <span class="text-danger">*</span></label> --}}
                                    <input type="text" name="filter_employee_code" id="filter_employee_code" class="form-control form-control-sm rounded-0 filter_input" placeholder="Enter Employee Code" value="{{ request()->filter_employee_code }}"/>
                                    <i class="fas fa-id-card"></i>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    {{-- <label for="filter_branch_id">Branch</label> --}}
                                    <select name="filter_branch_id" id="filter_branch_id" class="custom-select custom-select-sm rounded-0 filter_input">
                                        <option value="" selected disabled>Choose Branch</option>
                                        @foreach($branches as $branch)
                                            <option value="{{$branch['branch_id']}}" {{ $branch['branch_id'] == request()->filter_branch_id ? 'selected' : '' }}>{{$branch['branch_name']}}</option>
                                        @endforeach
                                    </select>
                                    <i class="fas fa-building"></i>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    {{-- <label for="filter_position_level_id">Position Level</label> --}}
                                    <select name="filter_position_level_id" id="filter_position_level_id" class="custom-select custom-select-sm rounded-0 filter_input">
                                        <option value="" selected disabled>Choose Position Level</option>
                                        @foreach($positionlevels as $positionlevel)
                                            <option value="{{$positionlevel['id']}}" {{ $positionlevel['id'] == request()->filter_position_level_id ? 'selected' : '' }}>{{$positionlevel['name']}}</option>
                                        @endforeach
                                    </select>
                                    <i class="fas fa-briefcase"></i>
                                </div>
                            </div>

                            <div class="col-auto">
                                <button type="button" class="btn my-2 ml-auto cus_btn searchbtns">Search</button>
                           </div>

                        </div>

                    </form>
                </div>

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

                                    <div class="header">
                                        <h4 class="title">All Employees</h4>
                                        <small class="subtitle">Search by name or employee id</small>
                                        <input type="text" name="search" id="search" class="search" placeholder="Search...."/>
                                    </div>
                                    <ul id="result" class="user-list">
                                        {{-- @foreach($users as $user)
                                        <div class="user-info">
                                            <li data-user_id = {{ $user->id }}>
                                                <i class="ri-folder-4-line"></i>
                                                    <h4>{{ $user->name }} ( {{ $user->employee_id }} )</h4>
                                            </li>

                                        </div>
                                        @endforeach --}}


                                        {{-- <li><h3>Loading...</h3></li> --}}
                                    </ul>

                                    <form id="peer_to_peer_form" action="{{ route('peertopeers.create') }}" method="" class="my-2">
                                        <input type="hidden" id="assessor_user_id" name="assessor_user_id" class="" value=""/>
                                        <input type="hidden" id="appraisal_cycle_id" name="appraisal_cycle_id" class="" value="{{ $appraisalcycle->id }}"/>
                                        <button type="submit" class="btn new_btn">New</button>
                                    </form>
                                </div>

                                <div class="col-lg-9">
                                    <div class="row">

                                        {{-- <div class="col-auto mt-2">
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="ri-more-line"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                  <a class="dropdown-item" href="#">Action</a>
                                                  <a class="dropdown-item" href="#">Another action</a>
                                                  <a class="dropdown-item" href="#">Something else here</a>
                                                  <div class="dropdown-divider"></div>
                                                  <a class="dropdown-item" href="#">Separated link</a>
                                                </div>
                                            </div>
                                        </div> --}}
                                        <div class="col-md-12 col-sm-12 mb-2 ">
                                            <div class="table-responsive rounded mb-3 position-relative">
                                                <table id="peertopeer" class="table mb-0 " style="min-height: 400px !important">
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
                                                            <th>
                                                                Action
                                                                <div class="dropdown position-absolute table-dropdowns">
                                                                    <a  class="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="More">
                                                                        <i class="ri-more-2-line"></i>
                                                                    </a>
                                                                    <div class="dropdown-menu">
                                                                      {{-- <a href="#ptopmodal" id="assessmentviewbtn" class="dropdown-item"  data-toggle="modal">360° Assessment View</a> --}}
                                                                      <a href="javascript:void(0);" id="assessmentviewbtn" class="dropdown-item">360° Assessment View</a>
                                                                    </div>
                                                                </div>
                                                            </th>
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
                            </div>

                        </div>

                        <div id="appraisal" class="tab-pane">
                            <div class="row">
                               <div class="col-lg-12">
                                    <div class="table-responsive rounded mb-3">
                                        <table id="participantusertable"  class="table mb-0 w-100" >
                                            <thead class="bg-white text-uppercase">
                                                <tr class="ligth ligth-data">
                                                    <th>No</th>
                                                    <th>Employee Name</th>
                                                    <th>Employee Code</th>
                                                    <th>Branch</th>
                                                    <th>Sent / All Forms</th>
                                                    <th>Progress</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="ligth-body">

                                            </tbody>
                                        </table>
                                    </div>
                               </div>
                            </div>
                        </div>

                        <div id="assesseesummary" class="tab-pane">
                            <div class="row">
                                <div class="col-lg-12">
                                    <h4 class="title">All Assessees</h4>
                                    <div class="table-responsive rounded mb-3">
                                        <table id="assesseeusertable" class="table mb-0 w-100">
                                            <thead class="bg-white text-uppercase">
                                                <tr class="ligth ligth-data">
                                                    <th>No</th>
                                                    <th>Employee Name</th>
                                                    <th>Employee Code</th>
                                                    <th>Branch</th>
                                                    <th>Position Level</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="ligth-body">

                                            </tbody>
                                        </table>
                                    </div>
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
    <!-- start create modal -->
    <div id="ptopmodal" class="modal fade">
        <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-0">
                    <div class="modal-header">
                        <h6 class="modal-title">Assessment Network</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="d-flex justify-content-center">
                            <canvas id="leadcharts"></canvas>
                            </div>
                        <div>
                    </div>

                    <div class="modal-footer">

                    </div>
                </div>
        </div>
    </div>
    <!-- end create modal -->



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
            noCalendar: true, // Hide the calendar if only time is neededparticipantusers
            dateFormat: "H:i", // Format for hours and minutes
            time_24hr: true // Use 24-hour format
        });



        {{-- Start participantusers  --}}
        const appraisalCycleId = {{ $appraisalcycle->id }}; // Make sure you have this ID in a hidden input

        $('#participantusertable').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": false,
            "lengthChange": false,
            "pageLength": 10,
            "autoWidth": true,
            "responsive": false,
            "order": [
              [1, 'des']
            ],
            'ajax': {
                url: `/${appraisalCycleId}/participantusers/`, // <-- include the ID here
              'type': 'GET',
              'data': function(d) {
                d.filter_employee_name = $('#filter_employee_name').val();
                d.filter_employee_code = $('#filter_employee_code').val();
                d.filter_branch_id = $('#filter_branch_id').val();
                d.filter_position_level_id = $('#filter_position_level_id').val();
              }
            },
            columns: [
                {
                    data: null,
                    name: 'no',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                { data: 'employee.employee_name', name: 'employee.employee_name' },
                { data: 'employee.employee_code', name: 'employee.employee_code' },
                { data: 'employee.branch.branch_name', name: 'employee.branch.branch_name' },
                {{-- { data: 'employee.department.name', name: 'employee.department.name' },
                { data: 'employee.position.name', name: 'employee.position.name' },
                { data: 'employee.positionlevel.name', name: 'employee.positionlevel.name' } --}}

                {
                    data: 'form_count',
                    name: 'form_count',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return data ?? ''; // Render raw value
                    }
                },
                {
                    data: 'progress',
                    name: 'progress',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return data ?? '';
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return data ?? '';
                    }
                }
            ],
            "columnDefs": [{
              "searchable": false,
              "orderable": false,
              "targets": 0,
            }],
        })

        $('#assesseeusertable').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": false,
            "lengthChange": false,
            "pageLength": 10,
            "autoWidth": true,
            "responsive": true,
            "order": [
              [1, 'asc']
            ],
            'ajax': {
                url: `/${appraisalCycleId}/assesseeusers/`, // <-- include the ID here
                'type': 'GET',
                'data': function(d) {
                    d.filter_employee_name = $('#filter_employee_name').val();
                    d.filter_employee_code = $('#filter_employee_code').val();
                    d.filter_branch_id = $('#filter_branch_id').val();
                    d.filter_position_level_id = $('#filter_position_level_id').val();
                  }
            },
            columns: [
                {
                    data: null,
                    name: 'no',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                { data: 'employee.employee_name', name: 'employee.employee_name' },
                { data: 'employee.employee_code', name: 'employee.employee_code' },
                { data: 'employee.branch.branch_name', name: 'employee.branch.branch_name' },
                { data: 'employee.positionlevel.name', name: 'employee.positionlevel.name' },
                {{-- { data: 'employee.department.name', name: 'employee.department.name' },
                { data: 'employee.position.name', name: 'employee.position.name' }, --}}
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return data ?? '';
                    }
                }
            ],
            "columnDefs": [{
              "searchable": false,
              "orderable": false,
              "targets": 0,
            }],
        })
        $('.searchbtns').on('click', function(e) {
            $('#participantusertable').DataTable().draw(true);
            $('#assesseeusertable').DataTable().draw(true);
            getAssessorUsers();

        })
        {{-- End participantusers --}}

        function getAssessorUsers(){
            $.ajax({
                url: `/${appraisalCycleId}/assessorusers/`,
                type: "GET",
                dataType: "json",
                data: $('#searchnfilterform').serialize(),
                success: function (response) {
                    console.log(response);

                    let html = '';
                    const assessorusers = response.users;

                    assessorusers.forEach(function(assessoruser,idx){
                        html += `
                        <div class="user-info">
                            <li data-user_id = ${assessoruser.id}>
                                <i class="ri-folder-4-line"></i>
                                    <h4>${assessoruser.name} ( ${assessoruser.employee_id} )</h4>
                            </li>

                        </div>
                        `;
                    })


                    $('#result').html(html);


                },
                error: function (response) {
                    console.log("Error:", response);
                }
            });
        }
        getAssessorUsers();

        {{-- Start Assessment Network --}}
        let assessmentNetworkChart = null;
        $('#assessmentviewbtn').click(function(){

            let assessor_user_id = $('.user-info li.active').data('user_id');

            $.ajax({
                url: `/api/assessmentnetwork/${assessor_user_id}/{{ $appraisalcycle->id }}/`,
                method: 'GET',
                success:function(data){
                     console.log(data)
                     const ctx = document.getElementById('leadcharts');
                     ctx.height = 250;

                    if (assessmentNetworkChart) {
                        assessmentNetworkChart.destroy();
                    }

                    assessmentNetworkChart = new Chart(ctx, {
                          type: 'doughnut',

                          data: {
                               labels: Object.keys(data.assessmentnetworksrcs),
                               datasets: [{
                                    data:  Object.values(data.assessmentnetworksrcs),
                                    backgroundColor: ["orange","#007bff"],
                                    borderWidth:1
                               }]
                          },
                          options: {
                               responsive:false
                          }
                     });

                }
           })


           $('#ptopmodal').modal();
        });
        {{-- End Assessment Network --}}
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
    const listitems = document.querySelectorAll('.user-info li');
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
