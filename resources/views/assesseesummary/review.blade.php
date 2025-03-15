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

           <div class="col-md-12">
                <button class="btn my-2 ml-auto cus_btn" data-target="#assessorsummarymodal" data-toggle="modal">Assessors Summary</button>
           </div>

           <div class="col-md-12 mb-2">

                <div class="row">


                    <div class="col-md-12">
                        {{-- <div class="header-bar mb-0">{{ $assesseeuser->getAssFormCat()->name }}</div> --}}
                        {{-- <div class="table-responsive">

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

                                    </tr>

                                </thead>
                                <tbody class="">
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>



                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div> --}}



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
                                <td class="label-cell">Assessment-Form Category:</td>
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
                        <div class="col-md-12 mt-2">

                            <button type="button" id="back-btn" class="btn btn-light btn-sm rounded-0" onclick="window.history.back();">Back</button>

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

    });
</script>
@stop
