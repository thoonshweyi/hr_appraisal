@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Assessee Summary</h4>
                    </div>
                </div>



            </div>



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

           <div class="col-md-12">
                <button class="btn my-2 ml-auto cus_btn" data-target="#assessorsummarymodal" data-toggle="modal">Assessors Summary</button>
           </div>

           <div class="col-md-12 mb-2">

                <div class="row">

                    <div class="col-md-12 mb-2">

                        <button type="button" id="back-btn" class="btn btn-light btn-sm rounded-0" onclick="window.history.back();">Back</button>

                    </div>
                    <div class="col-md-12">

                        <div class="table-responsive">

                        <table class="table table-bordered my-0 report-table">
                            <tr>
                              <th colspan="4" class="table-header">Employee Performance Appraisal Report</th>
                            </tr>
                            <tr>
                                <td class="label-cell">Assessee Name:</td>
                                <td class="value-cell">{{ $assesseeuser->employee->employee_name }}</td>
                                <td class="label-cell">Employee Code:</td>
                                <td class="value-cell">{{ $assesseeuser->employee->employee_code }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Position:</td>
                                <td class="value-cell">{{ $assesseeuser->employee->position->name }}</td>
                                <td class="label-cell">Criteria Set:</td>
                                <td class="value-cell">{{ $assesseeuser->getAssFormCat()->name }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Department:</td>
                                <td class="value-cell">{{ $assesseeuser->employee->department->name }}</td>
                                <td class="label-cell">Grade:</td>
                                <td class="value-cell">{{ substr($grade->name, 0, 1) }}</td>
                            </tr>
                            <tr class="criteria-label">
                              <td style="">S/No</td>
                              <td colspan="2">Criteria Description</td>
                              <td>Criteria Total</td>
                            </tr>
                            <!-- Rows for criteria -->

                            @foreach ($criterias as $idx=>$criteria)

                            <tr>
                              <td >{{ ++$idx }}</td>
                              <td colspan="2">{{ $criteria->name }}</td>
                              <td>{{ $criteria_totals[$criteria->id] }}</td>
                            </tr>
                            @endforeach
                            <!-- Totals -->
                            <tr>
                              <td colspan="3" class="">Rate Total</td>
                              <td colspan="1">{{ $ratetotal }}</td>
                            </tr>
                            <tr>
                              <td colspan="3" class="">Accessor</td>
                              <td colspan="1">{{ $assessoruserscount }}</td>
                            </tr>
                            <tr>
                              <td colspan="3" class="">Average</td>
                              <td colspan="1">{{ $average }}</td>
                            </tr>
                        </table>

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
 <div id="assessorsummarymodal" class="modal fade">
    <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h6 class="modal-title">An Assessee's Assessor Details</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>



                <div class="modal-body">

                    <label for="" class="mr-2">Assessee: </label><h6 class="text-dark fw-bold d-inline text-lg">{{ $assesseeuser->employee->employee_name }}</h6>
                    <div>
                        <label for="" class="mr-2">Criteria Set: </label>
                          {{-- {{ dd($assesseesummary->getAssesseeAssFormCats($assesseeuser->id, Route::current()->parameter('appraisal_cycle_id'))) }} --}}
                          @php
                          $asseseeformcats = $assesseesummary->getAssesseeAssFormCats($assesseeuser->id, Route::current()->parameter('appraisal_cycle_id'));
                         @endphp

                         @foreach($asseseeformcats as $asseseeformcat)
                            <span class="badge user-select-none me-1 assesseeformcattag" style='background:skyblue;color:white;' data-formid = '{{ $asseseeformcat->id }}' >{{ $asseseeformcat->name }}</span>
                         @endforeach
                    </div>


                    <div class="table-responsive" id="assessortable-{{ $asseseeformcat->id }}">
                        <table class="table table-bordered assessorsummarytable">
                            <thead>
                              <tr>
                                <th rowspan="2" style="width: 5% !important;">No</th>
                                <th rowspan="2" class="criteria_headers">Criteria</th>
                                <th colspan="4">Assessors</th>
                              </tr>
                              <tr>
                                @foreach ($assessorusers as $assessoruser)
                                    <th>{{ $assessoruser->employee->employee_name }} <br><small>{{ $assessoruser->employee->branch->branch_name }}</small></th>
                                @endforeach

                              </tr>
                            </thead>
                            <tbody>

                                @foreach($criterias as $idx => $criteria)
                                    <tr class="criterias formcriteria-{{ $criteria->assformcat->id }}">
                                        <td>{{ ++$idx }}</td>
                                        <td>{{ $criteria->name }}</td>

                                        @foreach ($assessorusers as $assessoruser)
                                            <td>{{ $assesseesummary->getAssessorGivenMark($assessoruser->id,$assesseeuser->id,$criteria->id,  Route::current()->parameter('appraisal_cycle_id') ) }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>

                <div class="modal-footer">

                </div>
            </div>
    </div>
</div>
<!-- end create modal -->>

<!-- End MODAL AREA -->
@endsection
@section('js')
<script>
    $(document).ready(function() {


        {{-- Your rating-value doesn't match the given-rating-scale-values! --}}
        {{-- $(".custom-input").on("input", function () {
            let min = parseInt($(this).attr("min"));
            let max = parseInt($(this).attr("max"));
            let value = parseInt($(this).val());

            if (value > max) {

                Swal.fire({
                    icon: "warning",
                    title: "Greater than maximum value.",
                    text:"Value cannot be greater than " + max,
                });
                $(this).val(max);
            } else if (value < min) {
                Swal.fire({
                    icon: "warning",
                    title: "Less than minimum value.",
                    text: "Value cannot be less than " + min,
                });
                $(this).val(min);
            }
        }); --}}


        document.querySelectorAll('.custom-input').forEach(function (input) {
            input.addEventListener('input', function () {
                const allowed = this.dataset.valids.split(',').map(Number);
                const value = parseInt(this.value);

                if (this.value !== '' && !allowed.includes(value)) {
                    {{-- alert('Invalid value! Please enter one of: ' + allowed.join(', ')); --}}

                    Swal.fire({
                        icon: "warning",
                        title: "Your rating-value doesn't match the given-rating-scale-values!",
                        text:"Please enter one of: " + allowed.join(', '),
                    });
                    this.value = ''; // Clear invalid input
                }
                updateTotals();

            });


        });

        function updateTotals() {
            const totals = {};

            // Reset all totals
            document.querySelectorAll('[id^="total_results_"]').forEach(span => {
                span.textContent = '0';
            });

            // Sum up scores by assessee
            document.querySelectorAll('.custom-input').forEach(input => {
                const assesseeId = input.dataset.assessee;
                const val = parseFloat(input.value);

                if (!totals[assesseeId]) {
                    totals[assesseeId] = 0;
                }

                if (!isNaN(val)) {
                    totals[assesseeId] += val;
                }
            });

            // Update the DOM
            for (const id in totals) {
                const span = document.getElementById(`total_results_${id}`);
                if (span) {
                    span.textContent = totals[id];
                }
            }

            console.log(totals);
        }


        {{-- Start Form Tag --}}
        $('.assesseeformcattag').click(function(){
            $('.assesseeformcattag').removeClass('active');
            $(this).addClass('active')
            let formid = $(this).data('formid');
            console.log(formid);

            $('.criterias').addClass('d-none');
            $('.formcriteria-'+formid).removeClass('d-none');
        });
        {{-- End Form Tag --}}

    });
</script>
@stop
