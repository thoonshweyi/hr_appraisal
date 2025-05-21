@extends('layouts.app')

@section('content')
<div class="content-page">

    <div class="container-fluid">
        <div class="row">


            {{-- <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-1">
                    <div>
                        <h4 class="mb-1">Performance Appraisal Assessment (Mobile)</h4>
                    </div>
                </div>
            </div> --}}


            <div class="col-md-12 mb-2">
                @php
                $errorCounts = array_count_values($errors->all());
                @endphp
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                                @once($error)
                                    <li>{{ $error }} x{{ $errorCounts[$error] }} times.</li>
                                @endonce
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
                <div class="form-header mb-2">
                        {{-- <h4 class="text-center">PRO1 Global Company Co.,Ltd</h4> --}}
                        <h5 class="">{{ $appraisalform->assformcat->name }}</h5>
                    <div class="row">
                        <div class="col-sm-6">
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
                                <span class="value">{{ $appraisalform->assessoruser->employee->attachformtype->name }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label for="assessees" class="form-label"><strong>Assessees (အမှတ်ပေးခံရမည့်သူ) </strong></label>
                            <select class="form-control" id="current_assessees">
                                @foreach($assesseeusers as $assesseeuser)
                                    <option value="{{$assesseeuser->id}}">{{ $assesseeuser->employee->employee_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                    <form id="appraisalformf" action="" method="POST">
                        @csrf
                        @method('PUT')

                    @foreach($assesseeusers as $assesseeuser)
                        <div id="assessee_{{ $assesseeuser->id }}_criterias" class="assessee_criterias" style="display: none;">
                        @foreach ($criterias as $idx=>$criteria)

                            <div class="form-card">
                                <div class="section-title">{{ $criteria->name }}</div>
                                <div class="score-radio d-flex flex-wrap">
                                    <div class="form-check me-2">
                                        <input class="form-check-input" type="radio" name="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]" id="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]-1" value="{{ $criteria->excellent }}" {{ $checked = ($appraisalform->getResult($assesseeuser->id,$criteria->id) == $criteria->excellent) ? 'checked' : '' }}/>
                                        @if (!$checked)
                                            <input type="hidden" name="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]"  value="">
                                        @endif
                                        <label class="custom-input" for="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]-1">{{ $criteria->excellent }}</label>
                                    </div>

                                    <div class="form-check me-2">
                                        <input type="hidden" name="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]"  value="">
                                        <input class="form-check-input" type="radio" name="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]" id="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]-2" value="{{ $criteria->good }}" {{ $appraisalform->getResult($assesseeuser->id,$criteria->id) == $criteria->good ? 'checked' : '' }}/>
                                        <label class="custom-input" for="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]-2">{{ $criteria->good }}</label>
                                    </div>

                                    <div class="form-check me-2">
                                        <input type="hidden" name="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]"  value="">
                                        <input class="form-check-input" type="radio" name="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]" id="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]-3" value="{{ $criteria->meet_standard }}" {{ $appraisalform->getResult($assesseeuser->id,$criteria->id) == $criteria->meet_standard ? 'checked' : '' }}/>
                                        <label class="custom-input" for="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]-3">{{ $criteria->meet_standard }}</label>
                                    </div>

                                    <div class="form-check me-2">
                                        <input type="hidden" name="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]"  value="">
                                        <input class="form-check-input" type="radio" name="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]" id="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]-4" value="{{ $criteria->below_standard }}" {{ $appraisalform->getResult($assesseeuser->id,$criteria->id) == $criteria->below_standard ? 'checked' : '' }}/>
                                        <label class="custom-input" for="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]-4">{{ $criteria->below_standard }}</label>
                                    </div>

                                    <div class="form-check me-2">
                                        <input type="hidden" name="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]"  value="">
                                        <input class="form-check-input" type="radio" name="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]" id="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]-5" value="{{ $criteria->weak }}" {{ $appraisalform->getResult($assesseeuser->id,$criteria->id) == $criteria->weak ? 'checked' : '' }}/>
                                        <label class="custom-input" for="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]-5">{{ $criteria->weak }}</label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </div>
                    @endforeach
                    </form>


                {{-- <div class="navigation-bar">
                    <button class="btn" id="prevBtn"><i class="fas fa-chevron-left"></i></button>
                    <h6 id="assesseeName">Yin Min Hlaing</h6>
                    <button class="btn" id="nextBtn"><i class="fas fa-chevron-right"></i></button>
                </div> --}}

           </div>

            <div class="col-md-12 mt-2">

                <button type="button" id="back-btn" class="btn btn-light btn-sm rounded-0" onclick="window.history.back();">{{ __('button.back')}}</button>
                <input type="button" name="savedraft" class="btn btn-warning btn-sm rounded-0 savedraftbtns" value="{{ __('button.savedraft')}}" />


                <button type="button" class="btn btn-success btn-sm rounded-0 submitbtns">{{ __('button.submit')}}</button>
            </div>
        </div>
    </div>


</div>

</div>

<!-- START MODAL AREA -->



    <!-- start edit modal -->

      <!-- end edit modal -->

<!-- End MODAL AREA -->

{{-- <iframe id="reprint_frame" name="reprint_frame" src="{{ url('appraisalformsshowprintframe/'.$appraisalform->id) }}" style="position: absolute;width:auto;height:auto;border:0;display: none;"  class=""></iframe> --}}

@endsection

@section('css')
<style type="text/css">
      .form-header {
        background-color: #ffffff;
        padding: 10px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);

        border-top: 8px solid orange;
        }

       .navigation-bar {
      background: #fff;
      padding: 0.75rem 1rem;
      border-bottom: 1px solid #ddd;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 10;
    }

    .navigation-bar h6 {
      margin: 0;
      font-weight: 600;
    }

    .navigation-bar .btn {
      background: none;
      border: none;
      font-size: 1.25rem;
      color: #333;
    }


   .score-radio input {
      display: none;
    }
    .score-radio label {
      margin: 0 3px;
      cursor: pointer;
    }
    .score-radio input:checked + label {
      background-color: #0d6efd;
      color: white;
    }
    .score-radio label {
      border: 1px solid #ced4da;
      border-radius: 5px;
      padding: 0.4em 0.8em;
    }
    .form-header {
      background-color: #ffffff;
      padding: 1.5rem;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    .section-title {
      font-weight: 600;
      /* font-size: 16px; */
      margin-bottom: 0.75rem;
    }
    .form-card {
      background-color: white;
      border-radius: 12px;
      padding: 1rem;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
      margin-bottom: 1rem;
    }
</style>
@endsection

@section('js')
<script>
    $(document).ready(function() {


        $('.custom-input').on('input', function () {
            const $input = $(this);
            const allowed = $input.data('valids').toString().split(',').map(Number);
            const value = parseInt($input.val());

            if ($input.val() !== '' && !allowed.includes(value)) {
                Swal.fire({
                    icon: "warning",
                    title: "သတ်မှတ်ထားသော အဆင့်သတ်မှတ်ချက်များနှင့် မကိုက်ညီပါ။",
                    text: allowed.join(', ') + " ထဲမှ တစ်ခုကို ရွေးပါ",
                });
                $input.val('');
            }

            updateTotals();
            autofocusNextInput($input);
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

        function autofocusNextInput(input) {
            {{-- console.log(input); --}}
            const $focused = $(':focus');
            const $activeInput = $focused.length ? $focused : input;

            const $td = $activeInput.closest('td');
            const $tr = $td.closest('tr');
            const $table = $tr.closest('table');
            const columnIndex = $tr.children().index($td);
            const $rows = $table.find('tr');
            const rowIndex = $rows.index($tr);

            let found = false;

            // Try to move to next row in the same column
            for (let i = rowIndex + 1; i < $rows.length; i++) {
                const $nextTd = $rows.eq(i).children().eq(columnIndex);
                const $nextInput = $nextTd.find('input.custom-input');
                if ($nextInput.length) {
                    $nextInput.focus();
                    found = true;
                    break;
                }
            }

            // If no more rows, go to first row of the next column
            if (!found) {
                const nextColumnIndex = columnIndex + 1;
                for (let i = 0; i < $rows.length; i++) {
                    const $nextTd = $rows.eq(i).children().eq(nextColumnIndex);
                    const $nextInput = $nextTd.find('input.custom-input');
                    if ($nextInput.length) {
                        $nextInput.focus();
                        break;
                    }
                }
            }
        }


        {{-- Start Tooltip --}}
        // This prevents the tooltip from hiding if the click is on the input or inside the tooltip.

        // Focus event to show the tooltip when the input is focused
        $('.custom-input').focus(function () {
            $(".critooltips").addClass('d-none'); // Hide all tooltips first
            $(this).next(".critooltips").removeClass('d-none'); // Show the specific tooltip for the input

            const $tableWrapper = $(this).closest('.table-responsive');
            const scrollWidth = $tableWrapper[0].scrollWidth;

            $tableWrapper.animate({
                scrollLeft: scrollWidth
            }, 100); // Scroll to the right for a quick scroll

            const input = $(this);

            // Remove old handlers to avoid duplicates and update input value on criteria circle click
            input.next(".critooltips").find('.criteria-circles').off('mousedown').on('mousedown', function (e) {
                {{-- e.preventDefault(); // Prevent input blur --}}
                const value = $(this).data('value');
                input.val(value);
                updateTotals();

                autofocusNextInput(input);
            });
        });


        $('.custom-input').blur(function () {
            {{-- setTimeout(() => {
                $('.critooltips').addClass('d-none');
            }, 1000); --}}
        });

        $('.tooltipcloses').click(function(){
            $(this).closest('.critooltips').addClass('d-none');
        });

        $(document).on('mousedown',function(e){
            {{-- console.log(e.target.classList.contains('custom-input')); --}}
            {{-- console.log(e.target.closest('.critooltips')) --}}

            if(!e.target.classList.contains('custom-input') && !e.target.closest('.critooltips')){
                $('.critooltips').addClass('d-none');
            }
        });


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
                            {{-- pdfFrame1.focus(); --}}
                            pdfFrame1.print();
        });
        {{-- End Print Arera --}}




        {{-- Start Target Each Assessee  --}}

        {{-- End Target Each Assessee --}}
    });


    $('#current_assessees').change(function(){
        let val = this.value;
        console.log(val);

          $('.assessee_criterias').css('display', 'none');

        $(`#assessee_${val}_criterias`).css('display', 'block');
    });
    $('#current_assessees').trigger('change')

</script>
@stop
