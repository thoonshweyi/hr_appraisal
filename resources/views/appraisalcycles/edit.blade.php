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
                                    <label for="filter_position_level_id"><i class="fas fa-briefcase text-primary mx-2"></i></label>
                                    <select name="filter_position_level_id" id="filter_position_level_id" class="form-control form-control-sm rounded-0 ">
                                        <option value="" selected disabled>Choose Position Level</option>
                                        @foreach($positionlevels as $positionlevel)
                                            <option value="{{$positionlevel['id']}}" {{ $positionlevel['id'] == session('filter_position_level_id') ? 'selected' : '' }}>{{$positionlevel['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}

                            <div class="col-md-2 px-1">
                                <div class="form-group d-flex">
                                    <label for="filter_subdepartment_id"><i class="fas fa-building text-primary mx-2"></i></label>
                                    <select name="filter_subdepartment_id" id="filter_subdepartment_id" class="form-control form-control-sm rounded-0 ">
                                        <option value="" selected disabled>Choose Sub Department</option>
                                        @foreach($subdepartments as $subdepartment)
                                            <option value="{{$subdepartment['id']}}" {{ $subdepartment['id'] == session('filter_subdepartment_id') ? 'selected' : '' }}>{{$subdepartment['name']}}</option>
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
                                            <input type="date" name="end_date" id="end_date" class="form-control form-control-sm rounded-0" placeholder="Choose End Date" value="{{ old('start_date',$appraisalcycle->end_date) }}"/>
                                        </div>


                                        <div class="col-md-3">
                                            <label for="action_start_date">Action Start Date <span class="text-danger">*</span></label>
                                            @error("action_start_date")
                                                    <span class="text-danger">{{ $message }}<span>
                                            @enderror
                                            <input type="date" name="action_start_date" id="action_start_date" class="form-control form-control-sm rounded-0" placeholder="Choose Action Start Date" value="{{ old('action_start_date',$appraisalcycle->action_start_date) }}"
                                            onChange="check_start_date(this.value);"
                                            />
                                        </div>


                                        <div class="col-md-3">
                                            <label for="action_end_date">Action End Date <span class="text-danger">*</span></label>
                                            @error("action_end_date")
                                                    <span class="text-danger">{{ $message }}<span>
                                            @enderror
                                            <input type="date" name="action_end_date" id="action_end_date" class="form-control form-control-sm rounded-0" placeholder="Choose Action End Date" value="{{ old('action_end_date',$appraisalcycle->action_end_date) }}"
                                            onChange="check_end_date(this.value);"
                                            />
                                        </div>



                                        {{-- <div class="col-md-3">
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
                                        </div> --}}

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

                                            {{-- @if($appraisalcycle->isBeforeActionStart()) --}}
                                            <button type="submit" class="btn btn-primary btn-sm rounded-0">Update</button>
                                            {{-- @endif --}}
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

                                    <div id="myloading-container" class=" d-none">
                                        <div class="text-center">
                                            <img src="{{ asset('images/spinner.gif') }}" id="myloading" class="myloading" alt="loading"/>
                                        </div>
                                    </div>
                                    <form id="compare-form" action="{{ route('appraisalcycles.compareemployees',$appraisalcycle->id) }}" method="POST" class="my-2">
                                        @csrf

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
                                    </form>

                                    <div class="d-flex align-items-center">
                                        <form id="peer_to_peer_form" action="{{ route('peertopeers.create') }}" method="" class="my-2">
                                            <input type="hidden" id="assessor_user_id" name="assessor_user_id" class="" value=""/>
                                            <input type="hidden" id="appraisal_cycle_id" name="appraisal_cycle_id" class="" value="{{ $appraisalcycle->id }}"/>
                                            {{-- @if($appraisalcycle->isBeforeActionStart()) --}}
                                                <button type="button" class="btn new_btn mr-2">New</button>
                                            {{-- @endif --}}
                                        </form>
                                            {{-- <input type="hidden" id="empuser_ids" name="empuser_ids[]" value={{ $appraisalcycle->id }}> --}}
                                            <button type="button" class="btn compare_btn">Compare</button>
                                    </div>

                                </div>

                                <div class="col-lg-9">

                                        <div class="d-flex justify-content-between mt-2">
                                            <h4 class="card-title">Employee Assessment Overview</h4>
                                            <div  class="dropdown">
                                                 <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></a>
                                                 <div class="dropdown-menu shadow">
                                                    <a href="javascript:void(0);" id="assessmentviewbtn" class="dropdown-item">360° Assessment View</a>
                                                 </div>
                                            </div>
                                        </div>
                                    <div class="row g-4">
                                        <!-- Assessors -->
                                        <div class="col-md-12 d-flex justify-content-center align-items-center">

                                            {{-- <a href="#balancemodal" class="d-flex align-items-center justify-content-center mx-2 balance-icon" title="Balance" data-toggle="modal">
                                              Balance
                                            </a> --}}

                                        </div>

                                        <!-- Assessees -->
                                        <div class="col-md-6">
                                            <div class="card shadow-sm h-100">
                                                <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-3 underline" >
                                                    <h5 class="card-title mb-0"><i class="bi bi-people-fill mr-2 text-success"></i>Assessees</h5>
                                                    <span id="empassesseescount" class="badge bg-success card-count d-none">0</span>
                                                    <a href="#balancemodal" data-toggle="modal" onclick="$('#assesseestab').trigger('click');">Show More</a>
                                                </div>
                                                <ul id="assesseeschart" class="list-group list-group-flush">
                                                    {{-- <li class="list-group-item"><i class="bi bi-person-circle list-icon"></i><strong>Michael Chan</strong> — Junior Developer</li>
                                                    <li class="list-group-item"><i class="bi bi-person-circle list-icon"></i><strong>Lily Zhao</strong> — IT Intern</li>
                                                    <li class="list-group-item"><i class="bi bi-person-circle list-icon"></i><strong>Tommy Brown</strong> — QA Engineer</li>
                                                    <li class="list-group-item"><i class="bi bi-person-circle list-icon"></i><strong>Jessica Wong</strong> — UI/UX Designer</li> --}}
                                                </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                          <div class="card shadow-sm h-100">
                                            <div class="card-body">
                                              <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="card-title mb-0"><i class="bi bi-person-check-fill mr-2 text-primary"></i>Assessors</h5>
                                                <span id="empassessorscount" class="badge bg-primary card-count d-none">0</span>
                                                <a href="#balancemodal" data-toggle="modal" onclick="$('#assessorstab').trigger('click');">Show More</a>
                                              </div>
                                              <ul id="empassessorschart" class="list-group list-group-flush ">
                                                {{-- <li class="list-group-item"><i class="bi bi-person-circle list-icon"></i><strong>Alice Smith</strong> — HR Manager</li>
                                                <li class="list-group-item"><i class="bi bi-person-circle list-icon"></i><strong>David Lee</strong> — Team Lead, IT</li>
                                                <li class="list-group-item"><i class="bi bi-person-circle list-icon"></i><strong>Sara Tan</strong> — Project Manager</li> --}}
                                              </ul>
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
                                                    <th>Position Level</th>
                                                    <th>Sent / All Forms</th>
                                                    <th>Send Progress</th>
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
                </div>
            </div>
            </div>






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
                        <label for="" class="mr-2">Assessor: </label><h6 id="assessorname" class="text-dark fw-bold d-inline text-lg">name</h6>
                        <div class="d-flex justify-content-center">
                            <canvas id="leadcharts"></canvas>
                        </div>
                    </div>

                    <div class="modal-footer">

                    </div>
                </div>
        </div>
    </div>


    <div id="formchartmodal" class="modal fade">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h6 class="modal-title">Form Chart Modal</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <label for="" class="mr-2">Assessor: </label><h6 id="assessorusername" class="text-dark fw-bold d-inline text-lg">name</h6>
                    <div class="d-flex justify-content-center">
                        <canvas id="formchart" style="min-width: 100%;min-height:250px;"></canvas>
                    </div>
                </div>

                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>
    <!-- end create modal -->

    <div id="balancemodal" class="modal fade">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h6 class="modal-title">Balance Modal</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Employee Info Card -->

                    <div id="employeeinfo">
                    {{-- <div class="card shadow-sm mb-0">
                        <div class="card-body">
                        <h5 class="card-title mb-1"><i class="bi bi-person-badge-fill me-2"></i>Employee Information</h5>
                        <div class="row">
                            <div class="col-md-3"><strong>Name:</strong> John Doe</div>
                            <div class="col-md-3"><strong>Position:</strong> Senior Developer</div>
                            <div class="col-md-3"><strong>Department:</strong> IT</div>
                            <div class="col-md-3"><strong>Employee ID:</strong> EMP-001</div>
                        </div>
                        </div>
                    </div> --}}
                    </div>

                    <div class="tabs">
                        <div id="assesseestab" class="tab active" onclick="showTab('assesseescontent')">Assessees</div>
                        <div id="assessorstab" class="tab " onclick="showTab('assessorscontent')">Assessors</div>
                    </div>

                    <div id="assesseescontent" class="transactions">
                        <div class="table-responsive rounded mb-3 position-relative" style="height:60vh;">

                            <table id="peertopeer" class="table mb-0 w-100" style="min-height: 100px !important;">
                                <thead class="bg-white text-uppercase">
                                    <tr class="ligth ligth-data">
                                        <th></th>
                                        <th style="">No</th>
                                        {{-- <th>Assessor Name</th> --}}
                                        <th>Assessee Name</th>
                                        <th>Department</th>
                                        {{-- <th>Branch</th>
                                        <th>Position Level</th>
                                        <th>Position</th> --}}
                                        {{-- <th>Assessment-form Category</th> --}}
                                        <th style="width: 40% !important;">Criteria Set</th>
                                        <th>
                                            Action
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
                    <div id="assessorscontent" class="transactions"  style="display: none;">
                        <div class="table-responsive rounded mb-3 position-relative" style="height:60vh;">

                            <table id="empassessorstable" class="table mb-0 w-100" style="min-height: 100px !important;">
                                <thead class="bg-white text-uppercase">
                                    <tr class="ligth ligth-data">
                                        <th></th>
                                        <th style="">No</th>
                                        {{-- <th>Assessor Name</th> --}}
                                        <th>Assessor Name</th>
                                        <th>Department</th>
                                        {{-- <th>Branch</th>
                                        <th>Position Level</th>
                                        <th>Position</th> --}}
                                        <th>
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="ligth-body">

                                </tbody>
                            </table>
                        </div>
                    </div>


                </div>

                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>

<!-- End MODAL AREA -->
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



        {{-- Start assessorusers, participantusers, assesseeusers, peertopeer  --}}
            const appraisalCycleId = {{ $appraisalcycle->id }};
            $('#participantusertable').DataTable({
                "processing": true,
                "serverSide": true,
                "searching": false,
                "lengthChange": false,
                "pageLength": 10,
                "autoWidth": true,
                "responsive": false,
                ordering: true,
                "order": [
                    [1, 'asc']
                ],
                'ajax': {
                    url: `/${appraisalCycleId}/participantusers/`, // <-- include the ID here
                'type': 'GET',
                'data': function(d) {
                    d.filter_employee_name = $('#filter_employee_name').val();
                    d.filter_employee_code = $('#filter_employee_code').val();
                    d.filter_branch_id = $('#filter_branch_id').val();
                    d.filter_position_level_id = $('#filter_position_level_id').val();
                    d.filter_subdepartment_id = $('#filter_subdepartment_id').val();
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
                    {{-- { data: 'employee.department.name', name: 'employee.department.name' }, --}}
                    {{-- { data: 'employee.position.name', name: 'employee.position.name' }, --}}
                    { data: 'employee.positionlevel.name', name: 'employee.positionlevel.name' },

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
                        d.filter_subdepartment_id = $('#filter_subdepartment_id').val();
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
            $('#participantusertable').on('click', '.show-forms', function () {
                var $btn = $(this); // The clicked <a>
                var $icon = $btn.find('i'); // The <i> inside it
                var tr = $btn.closest('tr');
                var userId = $btn.data('user');

                if (tr.next('.child-row').length) {
                    tr.next('.child-row').toggle(); // Toggle visibility

                    // Toggle the icon
                    if ($icon.hasClass('fa-chevron-down')) {
                        $icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                    } else {
                        $icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                    }
                } else {
                    $icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                    $.ajax({
                        url: `/appraisalformsbyuser/${userId}/`,
                        type: 'GET',
                        success: function (forms) {
                            var html =`
                            <div class="d-flex justify-content-between mt-2">
                                <h4 class="card-title">Form Lists</h4>
                                <div class="dropdown">
                                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></a>
                                    <div class="dropdown-menu shadow">
                                        <a href="javascript:void();" id="" class="dropdown-item formchart-btn" data-toggle="modal" data-user=${userId}>Form Chart</a>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-sm userforms">
                                <tr>
                                    <th>Form ID</th>
                                    <th>Title</th>
                                    <th>Appraisal Cycle</th>
                                    <th>Status</th>
                            </tr>`;
                            forms.forEach(form => {
                                let statusClass = '';
                                if (form.status_id === 19) {
                                    statusClass = 'bg-success';
                                } else if (form.status_id === 21) {
                                    statusClass = 'bg-primary';
                                } else if (form.status_id === 20) {
                                    statusClass = 'bg-warning';
                                }
                                html += `
                                <tr>
                                    <td>#${form.id}</td>
                                    <td><a href="/appraisalforms/${form.id}/edit">${form.assformcat.name}</a></td>
                                    <td>${form.appraisalcycle.name}</td>
                                    <td><span class="badge ${statusClass}">${form.status.name}</span></td>
                                </tr>`;
                            });
                            html += '</table>';
                            tr.after(`<tr class="child-row"><td claass="m-0" colspan="8">${html}</td></tr>`);
                        }
                    });
                }
            });
            let formChart = null;
            $(document).on('click', '.formchart-btn', function () {
                var userId = $(this).data('user');

                $.ajax({
                    url: `/appraisalformsuserdashboard/${userId}`,
                    method: 'GET',
                    success:function(data){
                        console.log(data);

                        const formctx = document.getElementById('formchart');
                        {{-- formctx.height = 250; --}}

                        let assessor_user_name = data.assessoruser.employee.employee_name;
                        $('#assessorusername').html(assessor_user_name);

                        if (formChart) {
                            formChart.destroy();
                        }
                        formChart = new Chart(formctx, {
                            type: 'bar',

                            data: {
                                labels: Object.keys(data.formgroups),
                                datasets: [{
                                    label: 'Form Analysis',
                                    data:  Object.values(data.formgroups),
                                    backgroundColor: "steelblue",
                                    borderWidth:1
                                }]
                            },
                            options: {
                                responsive:true,
                                {{-- scales: {
                                    y:{
                                        beginAtZero: true
                                    }
                                } --}}
                            }
                        });
                    }
                });

                $('#formchartmodal').modal('show');
            });

            function getAssessorUsers(){
                $.ajax({
                    url: `/${appraisalCycleId}/assessorusers/`,
                    type: "GET",
                    dataType: "json",
                    data: $('#searchnfilterform').serialize(),
                    beforeSend : function(){
                        $("#myloading-container").removeClass('d-none');
                    },
                    success: function (response) {
                        {{-- console.log(response); --}}

                        let html = '';
                        const assessorusers = response.users;

                        assessorusers.forEach(function(assessoruser,idx){
                            html += `
                            <div class="user-info">
                                <li data-user_id = ${assessoruser.id} data-user_name = '${assessoruser.employee.employee_name}'>
                                    <i class="ri-folder-4-line"></i>
                                        <h4>${assessoruser.name} ( ${assessoruser.employee_id} )</h4>
                                        <input type="hidden" class="empuser_ids" name="empuser_ids[]" value="${assessoruser.id}">
                                </li>

                            </div>
                            `;
                        })


                        $('#result').html(html);


                    },
                    complete: function(){
                        $("#myloading-container").addClass('d-none');
                    },
                    error: function (response) {
                        console.log("Error:", response);
                    }
                });
            }
            getAssessorUsers();





            const peertopeertable =  $('#peertopeer').DataTable({
                "processing": true,
                "serverSide": true,
                "searching": false,
                "lengthChange": false,
                "pageLength": 10,
                "autoWidth": false,
                "responsive": false,
                "order": [
                    [1, 'des']
                ],
                'ajax': {
                    url: `/getEmployeeAssessees`,
                    'type': 'GET',
                    'data': function(d) {
                        var formData = $('#peer_to_peer_form').serializeArray();
                        formData.forEach(function(item) {
                            d[item.name] = item.value;
                        });
                    },
                    dataSrc: function (json) {
                        {{-- console.log('Success response:', json); --}}
                        const rowcount = json.recordsTotal;

                        if(rowcount <= 0){
                            $("#empassesseescount").addClass('d-none');
                            $("#empassesseescount").html(0);
                        }else if(json.recordsTotal > 0){
                            $("#empassesseescount").removeClass('d-none');
                            $("#empassesseescount").html(rowcount);
                        }

                        // Return the data array to populate the table
                        return json.data;
                    }
                },
                columns: [
                    {
                        className: 'details-control',
                        orderable: false,
                        data: null,
                        defaultContent: ''
                    },
                    {
                        data: null,
                        name: 'no',
                        width: "2%",
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {{-- { data: 'assessoruser.employee.employee_name', name: 'assessoruser.employee.employee_name', orderable: false}, --}}
                    { data: 'assesseeuser.employee.employee_name', name: 'assesseeuser.employee.employee_name', orderable: false},
                    { data: 'assesseeuser.employee.department.name', name: 'assesseeuser.employee.department.name', orderable: false },
                    {{-- { data: 'assesseeuser.employee.branch.branch_name', name: 'assesseeuser.employee.branch.branch_name', orderable: false },
                    { data: 'assesseeuser.employee.positionlevel.name', name: 'assesseeuser.employee.positionlevel.name', orderable: false },
                    { data: 'assesseeuser.employee.position.name', name: 'assesseeuser.employee.position.name', orderable: false }, --}}
                    { data: 'assformcat.name', name: 'assformcat.name', orderable: false },
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

            $('#peertopeer tbody').on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                var row = peertopeertable.row(tr);

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open this row
                    row.child(format(row.data())).show();
                    tr.addClass('shown');
                }
                function format(d) {
                    // You can customize this to show more info about the row
                    return `
                        <table cellpadding="5" cellspacing="0" border="0" style="width:100%;padding-left:50px;" class='empinfos'>
                            <tr>
                                <td><strong>Department:</strong></td>
                                <td >${d.assesseeuser.employee.department.name ?? 'N/A'}</td>
                            </tr>
                            <tr>
                                <td><strong>Branch:</strong></td>
                                <td >${d.assesseeuser.employee.branch.branch_name ?? 'N/A'}</td>
                            </tr>
                            <tr>
                                <td><strong>Position Level:</strong></td>
                                <td >${d.assesseeuser.employee.positionlevel.name ?? 'N/A'}</td>
                            </tr>
                            <tr>
                                <td><strong>Position:</strong></td>
                                <td >${d.assesseeuser.employee.position.name ?? 'N/A'}</td>
                            </tr>
                        </table>
                    `;
                }
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


            const empassessorstable = $('#empassessorstable').DataTable({
                "processing": true,
                "serverSide": true,
                "searching": false,
                "lengthChange": false,
                "pageLength": 10,
                "autoWidth": false,
                "responsive": false,
                "order": [
                    [1, 'des']
                ],
                'ajax': {
                    url: `/getEmployeeAssessors`,
                    'type': 'GET',
                    'data': function(d) {
                        var formData = $('#peer_to_peer_form').serializeArray();
                        formData.forEach(function(item) {
                            d[item.name] = item.value;
                        });
                    },
                    dataSrc: function (json) {
                        {{-- console.log('Success response:', json); --}}
                        const rowcount = json.recordsTotal;

                        if(rowcount <= 0){
                            $("#empassessorscount").addClass('d-none');
                            $("#empassessorscount").html(0);
                        }else if(json.recordsTotal > 0){
                            $("#empassessorscount").removeClass('d-none');
                            $("#empassessorscount").html(rowcount);
                        }

                        // Return the data array to populate the table
                        return json.data;
                    }
                },
                columns: [
                    {
                        className: 'details-control',
                        orderable: false,
                        data: null,
                        defaultContent: ''
                    },
                    {
                        data: null,
                        name: 'no',
                        width: "2%",
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {{-- { data: 'assessoruser.employee.employee_name', name: 'assessoruser.employee.employee_name', orderable: false}, --}}
                    { data: 'assessoruser.employee.employee_name', name: 'assessoruser.employee.employee_name', orderable: false},
                    { data: 'assessoruser.employee.department.name', name: 'assessoruser.employee.department.name', orderable: false },
                    {{-- { data: 'assessoruser.employee.branch.branch_name', name: 'assessoruser.employee.branch.branch_name', orderable: false },
                    { data: 'assessoruser.employee.positionlevel.name', name: 'assessoruser.employee.positionlevel.name', orderable: false },
                    { data: 'assessoruser.employee.position.name', name: 'assessoruser.employee.position.name', orderable: false }, --}}
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
            $('#empassessorstable tbody').on('click', 'td.details-control', function () {
                var tr = $(this).closest('tr');
                var row = empassessorstable.row(tr);

                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open this row
                    row.child(format(row.data())).show();
                    tr.addClass('shown');
                }
                function format(d) {
                    // You can customize this to show more info about the row
                    return `
                        <table cellpadding="5" cellspacing="0" border="0" style="width:100%;padding-left:50px;" class='empinfos'>
                            <tr>
                                <td><strong>Department:</strong></td>
                                <td >${d.assessoruser.employee.department.name ?? 'N/A'}</td>
                            </tr>
                            <tr>
                                <td><strong>Branch:</strong></td>
                                <td >${d.assessoruser.employee.branch.branch_name ?? 'N/A'}</td>
                            </tr>
                            <tr>
                                <td><strong>Position Level:</strong></td>
                                <td >${d.assessoruser.employee.positionlevel.name ?? 'N/A'}</td>
                            </tr>
                            <tr>
                                <td><strong>Position:</strong></td>
                                <td >${d.assessoruser.employee.position.name ?? 'N/A'}</td>
                            </tr>
                        </table>
                    `;
                }
            });
        {{-- End assessorusers, participantusers, assesseeusers, peertopeer --}}


        {{-- Start Assessment Network --}}
        let assessmentNetworkChart = null;
        $('#assessmentviewbtn').click(function(){

            let assessor_user_id = $('.user-info li.active').data('user_id');
            let assessor_user_name = $('.user-info li.active').data('user_name');


            $.ajax({
                url: `/api/assessmentnetwork/${assessor_user_id}/{{ $appraisalcycle->id }}/`,
                method: 'GET',
                success:function(data){
                     console.log(data)
                     const ctx = document.getElementById('leadcharts');
                     ctx.height = 250;

                     $('#assessorname').html(assessor_user_name);

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
                    $('#filter_subdepartment_id').val('');


                    // Redraw DataTables
                    $('#participantusertable').DataTable().draw(true);
                    $('#assesseeusertable').DataTable().draw(true);
                    getAssessorUsers();
                }
            });
        });
        {{-- End Clear Btn --}}


        {{-- Start Compare Form --}}


        $('.compare_btn').click(function(){

            $('#compare-form').submit();
        });
        {{-- End Compare Form --}}

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
                listitem.querySelector('.empuser_ids').removeAttribute('disabled');
            }else{
                listitem.classList.add('hide');
                listitem.querySelector('.empuser_ids').setAttribute('disabled','true');
            }
        });
    }
    let tableBody = document.querySelector("#peertopeer tbody");
    $(document).on('click',".user-info li",function(){
        let getuser_id = $(this).data('user_id');
        {{-- let getassformcat_id =  --}}
        {{-- console.log(getuser_id); --}}
        $(".user-info li").removeClass('active');
        $(this).toggleClass('active');
        $('#assessor_user_id').val(getuser_id);

        $('#peertopeer').DataTable().draw(true);
        $('#empassessorstable').DataTable().draw(true);

        {{-- Start Employee Assessees Chart --}}
        $.ajax({
            url: '/api/getrecentassessees',
            method: 'GET',
            data: $('#peer_to_peer_form').serialize(),
            success:function(data){
                {{-- console.log(data); --}}

                let html = '';
                $.each(data,function(idx,employeeassessee){


                    html += `
                    <li class="list-group-item"><i class="bi bi-person-circle list-icon"></i><strong>${employeeassessee.assesseeuser.employee.employee_name}</strong> — ${employeeassessee.assesseeuser.employee.position.name}</li>
                    `;

                });


                $('#assesseeschart').html(html);

            },
            error:function(){
                $('#postchart').html('<span class="text-danger">Failed to load employee assessees data.<span>')
            }
        });
        {{-- End Employee Assessees Chart --}}


        {{-- Start Employeee Assessors Chart --}}
        $.ajax({
            url: '/api/getrecentassessors',
            method: 'GET',
            data: $('#peer_to_peer_form').serialize(),
            success:function(data){
                {{-- console.log(data); --}}

                let html = '';
                $.each(data,function(idx,employeeassessors){


                    html += `
                    <li class="list-group-item"><i class="bi bi-person-circle list-icon"></i><strong>${employeeassessors.assessoruser.employee.employee_name}</strong> — ${employeeassessors.assessoruser.employee.position.name}</li>
                    `;

                });


                $('#empassessorschart').html(html);
            },
            error:function(){
                $('#postchart').html('<span class="text-danger">Failed to load employee assessors data.<span>')
            }
        });
        {{-- End Employee Assessors Chart --}}


        getEmployeeInfo(getuser_id);
    });
    function getEmployeeInfo(userid){
        console.log(userid);
        const appraisalCycleId = {{ $appraisalcycle->id }};

        $.ajax({
            url: `/${appraisalCycleId}/assessorusers/`,
            type: "GET",
            dataType: "json",
            data: {
                filter_user_id: userid
            },
            success: function (response) {
                {{-- console.log(response); --}}

                let html = ``;
                const userempinfo = response.user;
                html += `
                <div class="card shadow-sm mb-0">
                    <div class="card-body">
                    <h5 class="card-title mb-1"><i class="bi bi-person-badge-fill me-2"></i>Employee Information</h5>
                    <div class="row">
                        <div class="col-md-3"><strong>Name:</strong> ${userempinfo.employee.employee_name}</div>
                        <div class="col-md-3"><strong>Position:</strong> ${userempinfo.employee.position.name}</div>
                        <div class="col-md-3"><strong>Department:</strong> ${userempinfo.employee.department.name}</div>
                        <div class="col-md-3"><strong>Employee ID:</strong> ${userempinfo.employee.employee_code}</div>
                    </div>
                    </div>
                </div>
                `

                console.log(userempinfo);

                $('#employeeinfo').html(html);


            },
            error: function (response) {
                console.log("Error:", response);
            }
        });
    }


    {{-- End User List Filter --}}


    {{-- Start notify btn --}}

    $(document).on('click','.notify-btns',function(){
        $.ajax({
            url: `/appraisalcyclessendnotifications`,
            type: "GET",
            dataType: "json",
            data: $(this).closest('form').serialize(),
            success: function (response) {
                {{-- console.log(response); --}}
                Swal.fire({
                    title: "Notify",
                    text: "Send Notification to assessor successfully.",
                    icon: "success"
               });


            },
            error: function (response) {
                console.log("Error:", response);
            }
        });
    });

    {{-- End notify btn --}}


    function check_start_date(start_date) {
        start_date = new Date(start_date);
        end_date = $('#action_end_date').val();
        var today = new Date();
        d_end_date = new Date(end_date);
        if (start_date > d_end_date) {
            Swal.fire({
                icon: 'warning',
                title: "{{ __('message.warning') }}",
                text: "{{ __('message.start_date_is_not_grether_than_end_date') }}",
                confirmButtonText: "{{ __('message.ok') }}",
            }).then(function() {
                $('#action_start_date').val(end_date);
                return false;

            });
        }
        if (today > start_date) {
            Swal.fire({
                icon: 'warning',
                title: "{{ __('message.warning') }}",
                text: "{{ __('message.start_date_is_not_last_than_today') }}",
                confirmButtonText: "{{ __('message.ok') }}",
            }).then(function() {
                $('#action_start_date').val(formatDate(today));
                return false;

            });
        }
    }


    function check_end_date(end_date) {
        end_date = new Date(end_date);
        start_date = $('#action_start_date').val();
        var today = new Date();
        d_start_date = new Date(start_date);
        if (d_start_date > end_date) {
            Swal.fire({
                icon: 'warning',
                title: "{{ __('message.warning') }}",
                text: "{{ __('message.end_date_is_not_last_than_start_date') }}",
                confirmButtonText: "{{ __('message.ok') }}",
            }).then(function() {
                $('#action_end_date').val(start_date);
                return false;

            });
        }
        if (today > end_date) {
            Swal.fire({
                icon: 'warning',
                title: "{{ __('message.warning') }}",
                text: "{{ __('message.end_date_is_not_last_than_today') }}",
                confirmButtonText: "{{ __('message.ok') }}",
            }).then(function() {
                $('#action_end_date').val(formatDate(today));
                return false;

            });
        }
    }


    function formatDate(date) {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;

        return [year, month, day].join('-');
    }

    {{-- Start Assessor Assessee Tab --}}
    function showTab(tabId) {
        document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
        document.querySelector(`.tab[onclick="showTab('${tabId}')"]`).classList.add('active');

        document.querySelectorAll('.transactions').forEach(tx => tx.style.display = 'none');
        document.querySelector(`#${tabId}`).style.display = 'block';
    }
    {{-- End Assessor Assessee Tab --}}


    {{-- Start New Btn --}}
    $(".new_btn").click(function(){

        if ($('.user-info li.active').length > 0) {
            $("#peer_to_peer_form").submit();
        }else{
            Swal.fire({
                icon: 'warning',
                title: "အကဲဖြတ်အမှတ်ပေးမည့်သူရွေးပေးပါ",
                text: "Please choose Assessor",
                confirmButtonText: "{{ __('message.ok') }}",
            }).then(function() {
                
            });
        }
    });
    {{-- End New Btn --}}
</script>
@stop
