@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-1">Performance Appraisal View</h4>
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

           <div class="col-md-12 mb-2">
                <div class="row">


                    <div id="printableArea">
                    <div class="col-md-12">
                        <div class="header-bar mb-0">{{ $appraisalform->assformcat->name }}</div>

                        <div class="table-responsive">
                            <table class="table table-bordered my-0">
                                <thead class="appraisal_headers">
                                    <tr class="text-white">
                                        <th colspan="2">
                                            <label for="">Assessor Name:</label>
                                            <span class="ml-4">{{ $appraisalform->assessoruser->employee->employee_name }}</span>
                                        </th>

                                        <th class="text-center" colspan="7">
                                            <label for="">Employee Code: </label>
                                            <span class="ml-4">{{ $appraisalform->assessoruser->employee->employee_code }}</span>
                                        </th>
                                    </tr>


                                    <tr class="text-white">
                                        <th colspan="2">
                                                <label for="">Position: </label>
                                                <span class="ml-4">{{ $appraisalform->assessoruser->employee->position->name }}</span>
                                        </th>
                                        <th class="text-center" colspan="7" rowspan="2">
                                            <label for="">Assessee: </label>
                                        </th>
                                    </tr>

                                    <tr>
                                        <th colspan="2">
                                            <label for="">Department: </label>
                                            <span class="ml-4">{{ $appraisalform->assessoruser->employee->department->name }}</span>
                                        </th>
                                    </tr>
                                </thead>
                            </table>

                            <table id="mytable" class="table table-bordered custables">

                                <thead class=" m-0">
                                    <tr class="table_headers">
                                        <th>S/No</th>
                                        <th>CRITERIA Description</th>
                                        <th>Excellent</th>
                                        <th>Good</th>
                                        <th>Meet Standard</th>
                                        <th>Below Standard</th>
                                        <th>Weak</th>
                                        @foreach($assesseeusers as $assesseeuser)
                                        <th style="width:auto;">{{ $assesseeuser->employee->employee_name }}</th>
                                        @endforeach
                                    </tr>

                                </thead>
                                <tbody class="">

                                    @foreach ($criterias as $idx=>$criteria)
                                    <tr class="table_rows">
                                        <td>{{ ++$idx }}</td>
                                        <td>{{ $criteria->name }}</td>
                                        <td >{{$criteria->excellent}}</td>
                                        <td >{{$criteria->good}}</td>
                                        <td >{{$criteria->meet_standard}}</td>
                                        <td >{{$criteria->below_standard}}</td>
                                        <td >{{$criteria->weak}}</td>
                                        @foreach($assesseeusers as $assesseeuser)
                                            <td style="width:auto;">
                                                {{ $appraisalform->getResult($assesseeuser->id,$criteria->id) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td colspan="2">Total Score</td>
                                        <td><span id="total_excellent">{{ $total_excellent }}</span></td>
                                        <td><span id="total_good">{{ $total_good }}</span></td>
                                        <td><span id="total_meet_standard">{{ $total_meet_standard }}</span></td>
                                        <td><span id="total_below_standard">{{ $total_below_standard }}</span></td>
                                        <td><span id="total_weak">{{ $total_weak }}</span></td>

                                        @foreach($assesseeusers as $assesseeuser)
                                        <td>
                                            <span id="total_results_{{ $assesseeuser->id }}">  {{ $appraisalform->getTotalResult($assesseeuser->id) }} </span>
                                        </td>
                                        @endforeach

                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    </div>


                    <div class="col-md-12 mt-2">

                        <button type="button" id="back-btn" class="btn btn-light btn-sm rounded-0" onclick="window.history.back();">Back</button>

                    </div>
                </div>
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
