@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            {{-- <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Appraisal Cycle Report</h4>
                    </div>
                </div>
            </div> --}}


            <div class="col-md-12 mb-2">
                @if (count($errors) > 0)
                <div class="alert alert-danger alert-dismissible fade show">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if ($message = Session::get('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <p>{{ $message }}</p>
                    <button type="button" class="close text-danger" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>
                @endif
                @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <p>{{ $message }}</p>
                    <button type="button" class="close text-danger" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif


                @if($getvalidationerrors = Session::get('validation_errors'))
                    {{-- <li>{{ Session::get('validation_errors') }}</li> --}}
                    <div class="alert alert-danger alert-dismissible fade show">
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


            <div class="col-lg-12 pt-0">

            <div class="card border-0 rounded-0 shadow mb-4">
                <ul class="nav">
                    <li class="nav-item">
                        <button type="button" id="dashboard-btn"  class="tablinks" onclick="gettab(event,'dashboard')">Dashboard</button>
                    </li>

                    @if(!branchHR())
                    <li class="nav-item">
                        <button type="button" id="assesseesummary-btn"  class="tablinks" onclick="gettab(event,'assesseesummary')">Assessee Summary</button>
                    </li>
                    @endif

                </ul>
                <h4 id="tab-title" class="tab-title"></h4>
                <div id="tab-tilter" class="col-lg-12 tab-filter">
                    <form id="searchnfilterform" class="" action="{{ route('assesseesummary.export',$appraisalcycle->id) }}" method="GET">
                        @csrf
                        <div class="row align-items-end justify-content-start">

                            <div class="col-md-2  px-1">
                                <div class="form-group d-flex">
                                    <label for="filter_employee_code"><i class="fas fa-user-tag text-primary mx-2"></i></label>
                                    <input type="text" name="filter_employee_code" id="filter_employee_code" class="form-control form-control-sm rounded-0 " placeholder="Enter Employee Code / Name" value="{{ session('filter_employee_code') }}"/>
                                    {{-- <i class="fas fa-id-card"></i> --}}
                                </div>
                            </div>

                            <div class="col-md-2 px-1">
                                <div class="form-group d-flex">
                                    <label for="filter_branch_id"><i class="fas fa-map-marker-alt text-primary mx-2"></i></label>
                                    <select name="filter_branch_id" id="filter_branch_id" class="form-control form-control-sm rounded-0 ">
                                        <option value="" selected disabled>Choose Branch</option>
                                        @foreach($branches as $branch)
                                            <option value="{{$branch['branch_id']}}" {{ $branch['branch_id'] == session('filter_branch_id') ? 'selected' : '' }}>{{$branch['branch_name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            {{-- <div class="col-md-2 px-1">
                                <div class="form-group d-flex">
                                    <label for="filter_subdepartment_id"><i class="fas fa-building text-primary mx-2"></i></label>
                                    <select name="filter_subdepartment_id" id="filter_subdepartment_id" class="form-control form-control-sm rounded-0 ">
                                        <option value="" selected disabled>Choose Sub Department</option>
                                        @foreach($subdepartments as $subdepartment)
                                            <option value="{{$subdepartment['id']}}" {{ $subdepartment['id'] == session('filter_subdepartment_id') ? 'selected' : '' }}>{{$subdepartment['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}

                            {{-- <div class="col-md-2">
                                <div class="form-group d-flex">
                                    <label for="filter_section_id"><i class="fas fa-building text-primary mx-2"></i></label>
                                    <select name="filter_section_id" id="filter_section_id" class="form-control form-control-sm rounded-0">
                                        <option value="" selected disabled>Choose  Section</option>
                                        @foreach($sections as $section)
                                                    <option value="{{$section['id']}}" {{ $section['id'] == session('filter_section_id') ? 'selected' : '' }}>{{$section['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}

                            <div class="col-md-2">
                                <div class="form-group d-flex">
                                    <label for="filter_sub_section_id"><i class="fas fa-building text-primary mx-2"></i></label>
                                    <select name="filter_sub_section_id" id="filter_sub_section_id" class="form-control form-control-sm rounded-0">
                                        <option value="" selected disabled>Choose Sub Section</option>
                                        @foreach($subsections as $subsection)
                                                    <option value="{{$subsection['id']}}" {{ $subsection['id'] == session('filter_sub_section_id') ? 'selected' : '' }}>{{$subsection['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>



                            <div class="col-md-2 px-1">
                                <div class="form-group d-flex">
                                    <label for="filter_position_level_id"><i class="fas fa-briefcase text-primary mx-2"></i></label>
                                    <select name="filter_position_level_id" id="filter_position_level_id" class="form-control form-control-sm rounded-0 ">
                                        <option value="" selected disabled>Choose Position Level</option>
                                        @foreach($positionlevels as $positionlevel)
                                            <option value="{{$positionlevel['id']}}" {{ $positionlevel['id'] == session('filter_position_level_id') ? 'selected' : '' }}>{{$positionlevel['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>



                            <div class="col-auto ">
                                <div class="d-flex align-items-end">
                                    <button type="button" class="btn  ml-auto mr-2 cus_btn searchbtns">Search</button>
                                    <button type="button" id="btn-clear" class="btn btn-light ml-auto">Reset</button>
                                </div>

                            </div>

                        </div>

                    </form>
                </div>

                <div class="tab-content">
                        <input type="hidden" id="appraisal_cycle_id" name="appraisal_cycle_id" class="" value="{{ $appraisalcycle->id }}"/>
                            

                        @if(!branchHR())
                        <div id="assesseesummary" class="tab-pane">
                            <div class="row">
                                <div class="col-lg-12">
                                    {{-- <h4 class="title">All Assessees</h4> --}}

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
                                    <button type="button" id="export-btn" class="btn cus_btn">Export</button>

                                </div>
                             </div>
                        </div>
                        @endif

                        <div id="dashboard" class="tab-pane">
                            <div class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <div class="card kpi-card rounded-4 p-3 h-100">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                        <div class="kpi-label">Total Assessors</div>
                                        <div id="kpiTotal" class="kpi-value">Loading....</div>
                                        </div>
                                        <i class="fas fa-users text-info icon"></i>
                                    </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card kpi-card rounded-4 p-3 h-100">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                        <div class="kpi-label">Completed Forms</div>
                                        <div id="kpiCompleted" class="kpi-value text-success">Loading....</div>
                                        </div>
                                        <i class="fas fa-check-circle icon" style="color: var(--success)"></i>
                                    </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card kpi-card rounded-4 p-3 h-100">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                        <div class="kpi-label">In Progress Forms</div>
                                        <div id="kpiProgress" class="kpi-value" style="color:var(--warning)">Loading....</div>
                                        </div>
                                        <i class="fas fa-tasks icon" style="color: var(--warning)"></i>
                                    </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card kpi-card rounded-4 p-3 h-100">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                        <div class="kpi-label">Not Started Forms</div>
                                        <div id="kpiNotStarted" class="kpi-value" style="color:var(--danger)">Loading....</div>
                                        </div>
                                        <i class="fas fa-exclamation-triangle icon" style="color: var(--danger)"></i>
                                    </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <h2>Branches (click to drill down)</h2>
                                            </div>
                                            {{-- <div class="sub">Horizontal bars show % Completed (target: 100%).</div> --}}

                                            <div id="byBranchChart" class="row " >
                                               
                                            </div>

                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>
                </div>
            </div>
            </div>






        </div>
    </div>

</div>

</div>


{{-- START PRINT AREA --}}
<div id="printforms">

</div>
{{-- END PRINT AREA --}}
@endsection

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
    /* Start Assessor Assessee Card */
    .card-count {
        font-size: 0.9rem;
        font-weight: 500;
        }
        .list-icon {
        color: #6c757d;
        margin-right: 0.5rem;
        }
        .balance-icon {
            width: 96px;
            height: 24px;
            color: white;
            border-radius: 50px;
            background-color: #444; /* Bootstrap warning color */
            text-decoration: none;
            transition: transform 0.2s ease;
            margin: 8px 0px;
    }

    .balance-icon:hover {
        transform: scale(1.1);
        background-color: #e0a800; /* Slightly darker on hover */
    }
    /* End Assessor Assessee Card */

    /* Start Assessor Assessee  Table Detail*/
    td.details-control {
      background: url('https://datatables.net/examples/resources/details_open.png') no-repeat center center;
      cursor: pointer;
    }
    tr.shown td.details-control {
      background: url('https://datatables.net/examples/resources/details_close.png') no-repeat center center;
    }
    /* End Assessor Assessee Table Detail */



    /* Start Assessor Assessee Tab*/
        .tabs {
            display: flex;
            gap: 10px;
            /* margin-bottom: 20px; */
        }
    .tab {
            flex: 1;
            padding: 2px;
            text-align: center;
            background: white;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
            color: #333;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .tab.active {
            background: #4facfe;
            color: white;
        }
    /* End Assessor Assessee Tab */


  </style>
@endsection
@section('js')
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

        $("#filter_branch_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Branch',
            searchField: ["value", "label"]
        });
        $("#filter_position_level_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Position Level',
            searchField: ["value", "label"]
        });
        $("#filter_subdepartment_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Sub Department',
            searchField: ["value", "label"]
        });

        $("#filter_section_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Section',
            searchField: ["value", "label"]
        });

        $("#filter_sub_section_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Sub Section',
            searchField: ["value", "label"]
        });



        $("#start_date,#end_date").flatpickr({
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


        $('#action_start_date').flatpickr({
            dateFormat: "Y-m-d",
       });
       $('#action_end_date').flatpickr({
                dateFormat: "Y-m-d",
        });



        {{-- Start assesseeusers  --}}
            const appraisalCycleId = {{ $appraisalcycle->id }};


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
                stateSave: true,
                stateDuration: -1,
                stateSaveCallback: function(settings, data) {
                    localStorage.setItem('DataTables_assesseeusertable', JSON.stringify(data));
                },
                stateLoadCallback: function(settings) {
                    return JSON.parse(localStorage.getItem('DataTables_assesseeusertable'));
                },
                'ajax': {
                    url: `/${appraisalCycleId}/assesseeusers/`, // <-- include the ID here
                    'type': 'GET',
                    'data': function(d) {
                        d.filter_employee_name = $('#filter_employee_name').val();
                        d.filter_employee_code = $('#filter_employee_code').val();
                        d.filter_branch_id = $('#filter_branch_id').val();
                        d.filter_position_level_id = $('#filter_position_level_id').val();
                        d.filter_subdepartment_id = $('#filter_subdepartment_id').val();
                        d.filter_section_id = $('#filter_section_id').val();
                        d.filter_sub_section_id = $('#filter_sub_section_id').val();
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
                $('#assesseeusertable').DataTable().draw(true);
            })

        {{-- End assessorusers, participantusers, assesseeusers, peertopeer --}}


        {{-- Start Export Btn --}}
        $('#export-btn').click(function(){
            $('#searchnfilterform').submit();
        });
        {{-- End Export Btn --}}


        {{-- Start Clear Btn --}}

        $('#btn-clear').click(function(){
            $('#searchnfilterform').trigger('reset');

            // Send request to Laravel to forget session variables
            $.ajax({
                url: '{{ route("clear.filter.sessions") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log('Session cleared');

                    // Clear frontend input fields (optional)
                    $('#filter_employee_name').val('');
                    $('#filter_employee_code').val('');
                    $('#filter_branch_id').val('');
                    $('#filter_position_level_id').val('');
                    {{-- $('#filter_subdepartment_id').val(''); --}}
                    $('#filter_section_id').val('');



                    // Redraw DataTables
                    $('#assesseeusertable').DataTable().draw(true);
                    getAssessorUsers();
                }
            });
        });
        {{-- End Clear Btn --}}

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


        if(linkid == 'appraisalcycle'){
            document.getElementById('tab-tilter').classList.add('d-none');
        }else{
            document.getElementById('tab-tilter').classList.remove('d-none');
        }

        localStorage.setItem('autoclick', linkid);
    }

    var appraisal_cycle_id = $("#appraisal_cycle_id").val();


    var autotab = localStorage.getItem("autoclick") || 'dashboard';
    document.getElementById(`${autotab}-btn`).click();
    // End Tag Box


    {{-- Start Assessor Assessee Tab --}}
    function showTab(tabId) {
        document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
        document.querySelector(`.tab[onclick="showTab('${tabId}')"]`).classList.add('active');

        document.querySelectorAll('.transactions').forEach(tx => tx.style.display = 'none');
        document.querySelector(`#${tabId}`).style.display = 'block';
    }
    {{-- End Assessor Assessee Tab --}}

    {{-- Start By Branch Dashboard --}}
    $.ajax({
		url: `/api/appraisalcycles/${appraisal_cycle_id}/bybranchesdashboard`,
		method: 'GET',
		success:function(data){
			console.log(data)


            let html = '';
            $.each(data,function(branch,databybranch){
                console.log(databybranch.statuses["19"]["percentage"]);
                let percent = databybranch.statuses["19"]["percentage"];
                let progresscolor = '';
				if(percent <= 20){
					progresscolor = 'bg-danger'
				}else if(percent <= 40){
					progresscolor = 'bg-warning'
				}else if(percent <= 60){
					progresscolor = 'bg-primary'
				}else if(percent <= 80){
					progresscolor = 'bg-info'
				}else{
					progresscolor = 'bg-success'
				}
                html += `
				<div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card p-3 branch-card" data-branch="Branch 19">
                        <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div style="font-weight:700">${branch}</div>
                            <div class="small-muted">${databybranch.assessors} employees</div>
                        </div>
                        <div class="text-end">
                            <div style="font-weight:800; font-size:1.1rem">${percent}%</div>
                            <div class="small-muted">Completed</div>
                        </div>
                        </div>
                        <div class="mt-3">
                        <div class="progress" role="progressbar" aria-valuenow="${percent}" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar" style="width:${percent}%; background:rgb(112,134,80)">${percent}%</div>
                        </div>
                        </div>
                    </div>
                </div>
				`;
            });
            $('#byBranchChart').html(html);


		},
		error: function(){
			$('#usercount').text("Error loading data");
		}
	});
    {{-- End By Branch Dashboard --}}


    {{-- Start Appraisal Form Chart --}}
	$.ajax({
		url: `/api/appraisalcycles/${appraisal_cycle_id}/appraisalformdashboard`,
		method: 'GET',
		success:function(data){
			console.log(data)

			$('#kpiTotal').text(data.totalemployees);
			$('#kpiCompleted').text(data.completed);
			$('#kpiProgress').text(data.inprogress);
			$('#kpiNotStarted').text(data.notstarted);

		},
		error: function(){
			$('#usercount').text("Error loading data");
		}
	});
    {{-- End Appraisal  --}}

</script>
@stop
