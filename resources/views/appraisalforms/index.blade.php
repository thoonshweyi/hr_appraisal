@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Appraisal Forms</h4>
                    </div>
                </div>



            </div>

            @can('view-all-appraisal-form')
            <div class="col-lg-12 ">
                <div class="tab-filter">
                    <form id="searchnfilterform" class="" action="{{ route('appraisalforms.index') }}" method="GET">
                        @csrf
                        <div class="row align-items-end justify-content-start">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {{-- <label for="filter_employee_name">Enployee Name <span class="text-danger">*</span></label> --}}
                                    <input type="text" name="filter_employee_name" id="filter_employee_name" class="form-control form-control-sm rounded-0 filter_input" placeholder="Enter Employee Name" value="{{ request()->filter_employee_name }}"/>
                                    <i class="fas fa-user"></i>
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
                                    {{-- <label for="filter_branch_id">Branch</label> --}}
                                    <select name="filter_appraisal_cycle_id" id="filter_appraisal_cycle_id" class="custom-select custom-select-sm rounded-0 filter_input">
                                        <option value="" selected disabled>Choose Appraisal Cycle</option>
                                        @foreach($appraisalcycles as $appraisalcycle)
                                            <option value="{{$appraisalcycle['id']}}" {{ $appraisalcycle['id'] == request()->filter_appraisal_cycle_id ? 'selected' : '' }}>{{$appraisalcycle['name']}}</option>
                                        @endforeach
                                    </select>
                                    <i class="fas fa-building"></i>
                                </div>
                            </div>


                            <div class="col-auto">
                                <button type="submit" class="btn my-2 ml-auto cus_btn searchbtns">Search</button>
                           </div>

                        </div>

                    </form>
                </div>

            </div>
            @endcan


            <div class="col-md-12">
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


    <div class="col-lg-12">
        <div class="table-responsive rounded mb-3">
            <table class="table mb-0" id="branch_list">
                <thead class="bg-white text-uppercase">
                    <tr class="ligth ligth-data">
                        <th>No</th>
                        <th>Assessment-form Category</th>
                        <th>Assessor</th>
                        <th>Appraisal Cycle</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="ligth-body">
                    @foreach($appraisalforms as $idx=>$appraisalform)
                    <tr>
                        <td>{{$idx + $appraisalforms->firstItem()}}</td>
                        <td>{{$appraisalform->assformcat["name"]}}</td>
                        <td>{{ $appraisalform->assessoruser->employee->employee_name }}</td>
                        <td>{{$appraisalform->appraisalcycle["name"]}}</td>
                        <td> <span class="badge {{  $appraisalform->status_id == 19 ? 'bg-success' : ($appraisalform->status_id == 21 ? 'bg-primary' : ($appraisalform->status_id == 20 ? 'bg-warning' : '')) }}"> {{ $appraisalform->status->name }} </span></td>
                        <td class="text-center">
                            @if($appraisalform->assessed)
                            <a href="{{ route('appraisalforms.show',$appraisalform->id) }}" class="text-info mr-2" title="Open"><i class="fas fa-eye"></i></a>
                            @else
                            <a href="{{ route('appraisalforms.edit',$appraisalform->id) }}" class="text-primary mr-2" title="Open"><i class="fas fa-edit"></i></a>
                            @endif
                       </td>

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


    });
</script>
@stop
