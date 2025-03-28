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


                    <div class="col-md-12">
                        @php
                        $assesseeChunks = $assesseeusers->chunk(5);
                        @endphp
                        @foreach($assesseeChunks as $chunkIndex => $chunk)

                        <div class="printableArea" style="{{ $chunkIndex > 0 ? 'page-break-before: always;' : '' }}">
                            <div class="table-responsive">

                                <table  class="assessmentformtable">
                                    <tr class="header-row">
                                        <td colspan="11">
                                            {{-- <span style="" class="print-date">Print Date: {{ Carbon\Carbon::now()->format('d-M-Y') }}</span> --}}
                                            <h4 class="company-title">PRO1 Global Company Co.,Ltd</h4>
                                            <strong class="form-title">Assessment Form: {{ $appraisalform->assformcat->name }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left" rowspan="2">
                                            <div class="assessor-infos">
                                                <strong>Assessor (အကဲဖြတ်အမှတ်ပေးမည့်သူ)</strong>
                                                <span class="delimiter">-</span>
                                                <span class="value">{{ $appraisalform->assessoruser->employee->employee_name }}</span>
                                            </div>
                                            <div class="assessor-infos">
                                                <strong>Position (ရာထူး)</strong>
                                                <span class="delimiter">-</span>
                                                <span class="value">{{ $appraisalform->assessoruser->employee->position->name }}</span>
                                            </div>
                                            <div class="assessor-infos">
                                                <strong>Department (ဌာန)</strong>
                                                <span class="delimiter">-</span>
                                                <span class="value">{{ $appraisalform->assessoruser->employee->department->name }}</span>
                                            </div>
                                        </td>
                                        <th colspan="10" class="text-left">Assessees Location : ____</th>
                                    </tr>

                                    <tr>
                                        <th colspan="10" class="text-left">Assessees (အမှတ်ပေးခံရမည့်သူ) </th>
                                    </tr>
                                    <!-- Header Row -->
                                    <tr>
                                        <th class="criteria-header" style="">CRITERIA</th>
                                        @foreach(['Excellent', 'Good', 'Meet', 'Below', 'Weak'] as $rating)
                                            <th class="vertical-header"> <span class="ratings">{{ $rating }} </span></th>
                                        @endforeach
                                        @php $chunkArray = $chunk->values(); @endphp
                                        @for($i = 0; $i < 5; $i++)
                                            <th class="vertical-header">
                                                <span class="employees">
                                                @if(isset($chunkArray[$i]))
                                                    {{ $chunkArray[$i]->employee->employee_name }}
                                                @else
                                                    &nbsp;
                                                @endif
                                                </span>
                                            </th>
                                        @endfor
                                    </tr>


                                    <!-- Criteria Rows -->
                                    @foreach ($criterias as $criteria)
                                        <tr>
                                            <td class="text-left">{{ $criteria->name }}</td>
                                            <td>{{ $criteria->excellent }}</td>
                                            <td>{{ $criteria->good }}</td>
                                            <td>{{ $criteria->meet_standard }}</td>
                                            <td>{{ $criteria->below_standard }}</td>
                                            <td>{{ $criteria->weak }}</td>

                                            @for($i = 0; $i < 5; $i++)
                                                <td>
                                                    @if(isset($chunkArray[$i]))
                                                        {{ $appraisalform->getResult($chunkArray[$i]->id, $criteria->id) }}
                                                    @else
                                                        &nbsp;
                                                    @endif
                                                </td>
                                            @endfor
                                        </tr>
                                    @endforeach

                                    <!-- Total Row -->
                                    <tr class="total-row">
                                        <td>Total Score</td>
                                        <td>{{ $total_excellent }}</td>
                                        <td>{{ $total_good }}</td>
                                        <td>{{ $total_meet_standard }}</td>
                                        <td>{{ $total_below_standard }}</td>
                                        <td>{{ $total_weak }}</td>

                                        @for($i = 0; $i < 5; $i++)
                                            <td>
                                                @if(isset($chunk[$i]))
                                                    {{ $appraisalform->getTotalResult($chunk[$i]->id) != 0 ? $appraisalform->getTotalResult($chunk[$i]->id) : '' }}
                                                @else
                                                    &nbsp;
                                                @endif
                                            </td>
                                        @endfor
                                    </tr>

                                    <tr>
                                        <td colspan="6">Notes:</td>
                                        <td colspan="5">Voter's Signature:</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        @endforeach

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
