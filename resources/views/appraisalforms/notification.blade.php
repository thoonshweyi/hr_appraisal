@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            {{-- <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-2">
                    <div>
                        <h4 class="mb-3">Appraisal Forms Notification</h4>
                    </div>
                </div>
            </div> --}}


            <div class="col-md-12">
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



    {{-- <div class="container-fluid">
        <div class="card">
            <div class="card-header p-2" >
                <div class="col-lg-12 mb-2">
                    <div class="noti-container d-flex align-items-start mb-1">
                        <div class="">
                            <div class="noti-icon mr-3 " style="flex: none;">
                                <span class="fw-bold" style="color: white">5</span>
                            </div>
                            <div class="smart-label mb-1">Total</div>
                        </div>


                        <div class="flex-grow-1">
                            <h3 class="text-danger mb-2">
                                <i class="fas fa-clipboard-list me-2"></i>
                                Assessment Forms Awaiting Your Review
                            </h3>
                            <p class="text-muted mb-0">Your insights are crucial for employee development. Let's complete these assessments together!</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row my-2">




                    <div class="col-4 text-center">
                        <div class="done">
                            <div class="smart-icon bg-success bg-opacity-10 text-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="smart-label mb-1">Done</div>
                            <div class="smart-value text-success">5</div>
                        </div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="in-progress">
                            <div class="smart-icon bg-warning bg-opacity-10 text-warning">
                                    <i class="fas fa-clock"></i>
                            </div>
                            <div class="smart-label mb-1">In Progress</div>
                            <div class="smart-value text-warning">4</div>
                        </div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="not-started">
                        <div class="smart-icon bg-danger bg-opacity-10 text-danger">
                                <i class="fas fa-pause-circle"></i>
                            </div>
                            <div class="smart-label mb-1">Not Started</div>
                            <div class="smart-value text-danger">3</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div> --}}

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="dashboard-card">
                    <div class="dashboard-label">
                        <i class="fas fa-clipboard-list me-2"></i>
                        Appraisal Form Notification
                    </div>
                    <div class="notis-style p-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div class="d-flex align-items-center">
                                <div class="notis-icon mr-3" style="width: 45px; height: 45px; font-size: 1rem; flex:none;">
                                    <span class="fw-bold">{{ count($appraisalforms) }}</span>
                                </div>
                                <div>
                                    <h5 class="mb-1">Appraisal Forms Total !</h5>
                                    {{-- <small class="text-muted">7 forms pending your review</small> --}}
                                    <p class="mb-0">ယခုနှစ်အတွက် သင်အကဲဖြတ်အမှတ်ပေးရန် လက်ခံရရှိထားသော စာစောင် <strong>{{ count($appraisalforms) }}</strong> ခုရှိပါသည်</p>
                                </div>
                            </div>
                        </div>

                        {{-- <div class="row">
                            <div class="col-lg-12">
                                <div class="my-3">
                                    <div  class="progress" role="progressbar" aria-valuenow="45.95" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" style="width:45.95%; background:rgb(112,134,80)">45.95%</div>
                                    </div>
                                    <small class="text-muted" style="float:left;">5 of 12 appraisals completed</small>
                                </div>
                            </div>
                        </div> --}}

                        <a href="javascript:void(0);" data-toggle="collapse" data-target="#dashboard-more">View More</a>
                        <div id="dashboard-more" class="collapse">
                            <div class="row g-2">
                                <div class="col-3">
                                    <div class="text-center p-2 bg-light rounded">
                                        <div class="h6 text-danger mb-0">2</div>
                                        <small class="text-muted">Urgent</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="text-center p-2 bg-light rounded">
                                        <div class="h6 text-warning mb-0">2</div>
                                        <small class="text-muted">High</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="text-center p-2 bg-light rounded">
                                        <div class="h6 text-primary mb-0">3</div>
                                        <small class="text-muted">Normal</small>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="text-center p-2 bg-light rounded">
                                        <div class="h6 text-success mb-0">5</div>
                                        <small class="text-muted">Done</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <div class="col-lg-12">
        <div class="table-responsive rounded mb-1">
            <table class="table mb-0" id="branch_list">
                <thead class="bg-white text-uppercase">
                    <tr class="ligth ligth-data">
                        <th>No</th>
                        {{-- <th>Action</th> --}}
                        <th style="min-width:250px !important;">Criteria Set</th>
                        <th>Status</th>
                        <th>Appraisal Cycle</th>
                        {{-- <th>Assessor</th> --}}
                    </tr>
                </thead>
                <tbody class="ligth-body">
                    @foreach($appraisalforms as $idx=>$appraisalform)
                    <tr>
                        <td>{{$idx + $appraisalforms->firstItem()}}</td>
                        {{-- <td class="text-center">
                            @if($appraisalform->assessed || !$appraisalform->appraisalcycle->isActioned())
                            <a href="{{ route('appraisalforms.show',$appraisalform->id) }}" class="text-info mr-2" title="Open"><i class="fas fa-eye"></i></a>
                            @else
                            <a href="{{ route('appraisalforms.edit',$appraisalform->id) }}" class="text-primary mr-2" title="Open"><i class="fas fa-edit"></i></a>
                            @endif
                       </td> --}}
                        <td><a href="{{ ($appraisalform->assessed || !$appraisalform->appraisalcycle->isActioned()) ? route('appraisalforms.show',$appraisalform->id) : route('appraisalforms.edit',$appraisalform->id) }}">{{$appraisalform->assformcat["name"]}}</a></td>
                        <td > <span class="badge {{  $appraisalform->status_id == 19 ? 'bg-success' : ($appraisalform->status_id == 21 ? 'bg-primary' : ($appraisalform->status_id == 20 ? 'bg-warning' : '')) }}"> {{ $appraisalform->status->name }} </span></td>
                        <td>{{$appraisalform->appraisalcycle["name"]}}</td>
                        {{-- <td>{{ $appraisalform->assessoruser->employee->employee_name }}</td> --}}
                   </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-center">
                {{ $appraisalforms->appends(request()->all())->links("pagination::bootstrap-4") }}
            </div>


        </div>
    </div>



</div>

</div>

<!-- START MODAL AREA -->



    <!-- start edit modal -->

      <!-- end edit modal -->

<!-- End MODAL AREA -->
@endsection
@section('js')
<script>
    $(document).ready(function() {
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
        $("#filter_appraisal_cycle_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Appraisal Cycle',
            searchField: ["value", "label"]
        });




    });
</script>
@stop
