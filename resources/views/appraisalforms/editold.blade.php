@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Performance Appraisal Assessment</h4>
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

           <div class="col-md-12 mb-2">
                <div class="row">
                    <div class="col-auto mb-2">
                        {{-- <a href="{{ route('appraisalforms.printpdf',$appraisalform->id) }}" class="btn  cus_btn">Print</a> --}}
                        <a href="javascript:void(0);" class="btn cus_btn">Print</a>

                    </div>

                    <div class="col-md-12">
                        <div class="header-bar mb-0">{{ $appraisalform->assformcat->name }}</div>

                        <form id="appraisalformf" action="" method="POST">
                            @csrf
                            @method('PUT')
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

                                <table id="mytable" class="table table-bordered custables" >

                                    <thead class="sticky-header m-0">
                                        <tr class="">
                                            <th>S/No</th>
                                            <th class=" ">CRITERIA Description</th>
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
                                                {{-- {{ dd($criteria->pluck('excellent')) }} --}}
                                                {{-- {{ dd($criteria->getRatingScaleAttribute()) }} --}}
                                                <td class="p-0" style="width:auto;">
                                                    <input type="number" name="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]" class="custom-input" max="{{ $criteria->excellent }}" min="{{ $criteria->weak }}"
                                                    value="{{ old('appraisalformresults') ? old('appraisalformresults')[$assesseeuser->id][$criteria->id] :  $appraisalform->getResult($assesseeuser->id,$criteria->id) }}" data-valids="{{ implode(',', $criteria->getRatingScaleAttribute()) }}"
                                                    data-assessee="{{ $assesseeuser->id }}"
                                                    />
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
                                                <span id="total_results_{{ $assesseeuser->id }}"> {{ $appraisalform->getTotalResult($assesseeuser->id) ?? '' }} </span>
                                            </td>
                                            @endforeach

                                        </tr>
                                    </tfoot>
                                </table>
                            </div>


                            <div class="col-md-12 mt-2">

                                <button type="button" id="back-btn" class="btn btn-light btn-sm rounded-0" onclick="window.history.back();">Back</button>
                                <input type="button" name="savedraft" class="btn btn-warning btn-sm rounded-0 savedraftbtns" value="Save Draft" />


                                <button type="button" class="btn btn-success btn-sm rounded-0 submitbtns">Submit</button>
                            </div>
                        </form>
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

<iframe id="reprint_frame" name="reprint_frame" src="{{ url('appraisalformsshowprintframe/'.$appraisalform->id) }}" style="position: absolute;width:0;height:0;border:0;"  class="hidden"></iframe>

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


        {{-- Start Save Draft --}}
        {{-- $('#appraisalform').submit(function(e){
            e.preventDefault();
            console.log('hi');

            $(this).action('')
        }); --}}

        $('.submitbtns').click(function(e){
            Swal.fire({
                title: "Are you sure you want to submit Appraisal Form",
                text: "",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, submit it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#appraisalformf').attr('action','{{ route('appraisalforms.update',$appraisalform->id) }}');
                    $('#appraisalformf').submit();
                }
            });



        });
        $('.savedraftbtns').click(function(e){
            $('#appraisalformf').attr('action','{{ route('appraisalforms.savedraft',$appraisalform->id) }}');
            $('#appraisalformf').submit();
        });
        {{-- End Save Draft --}}

        {{-- Start Print Area --}}
        document.querySelector('.cus_btn').addEventListener('click', function () {
            var pdfFrame1 = window.frames["reprint_frame"];
                            pdfFrame1.focus();
                            pdfFrame1.print();
        });
        {{-- End Print Arera --}}
    });
</script>
@stop
