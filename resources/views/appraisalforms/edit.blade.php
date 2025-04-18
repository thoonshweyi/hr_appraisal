@extends('layouts.app')

@section('content')
<div class="content-page">

    <div class="container-fluid">
        <div class="row">


            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-1">Performance Appraisal Assessment</h4>
                    </div>
                </div>
            </div>


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
                <div class="row">



                    @can('print-appraisal-form')
                    <div class="col-auto mb-2">
                        {{-- <a href="{{ route('appraisalforms.printpdf',$appraisalform->id) }}" class="btn  cus_btn">Print</a> --}}
                        <a href="javascript:void(0);" class="btn cus_btn">{{ __('button.print_document')}}</a>

                    </div>
                    @endcan
                    <div class="col-md-12">
                    <form id="appraisalformf" action="" method="POST">
                        @csrf
                        @method('PUT')

                        @php
                        $assesseeChunks = $assesseeusers->chunk(5);
                        @endphp
                        @foreach($assesseeChunks as $chunkIndex => $chunk)

                        <div class="printableArea page" style="{{ $chunkIndex > 0 ? 'page-break-before: always;' : '' }}">

                            <div class="table-responsive" style="">
                                <table class="assessmentformtable">
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
                                    @foreach ($criterias as $idx=>$criteria)
                                        <tr>
                                            <td class="text-left">{{ $criteria->name }}</td>
                                            <td style="vertical-align: middle">{{ $criteria->excellent }}</td>
                                            <td>{{ $criteria->good }}</td>
                                            <td>{{ $criteria->meet_standard }}</td>
                                            <td>{{ $criteria->below_standard }}</td>
                                            <td>{{ $criteria->weak }}</td>

                                            @for($i = 0; $i < 5; $i++)
                                                <td class="position-relative">
                                                    @if(isset($chunkArray[$i]))
                                                        <div class="position-relative">


                                                            <input type="number" name="appraisalformresults[{{$chunkArray[$i]->id}}][{{ $criteria->id }}]" class="custom-input" max="{{ $criteria->excellent }}" min="{{ $criteria->weak }}"
                                                            value="{{ old('appraisalformresults') ? old('appraisalformresults')[$chunkArray[$i]->id][$criteria->id] :  $appraisalform->getResult($chunkArray[$i]->id,$criteria->id) }}"  data-valids="{{ implode(',', $criteria->getRatingScaleAttribute()) }}"
                                                            data-assessee="{{ $chunkArray[$i]->id }}" data-assessee-name="{{ $chunkArray[$i]->employee->employee_name }}" data-criteria-name="{{ $criteria->name }}"
                                                            />
                                                            {{-- @if($i == 0 && $idx == 0) --}}
                                                            <div class="d-none critooltips">
                                                                <h6> <span>{{ $chunkArray[$i]->employee->employee_name }}</span>
                                                                    <button type="button" class="close tooltipcloses" aria-label="Close">
                                                                        <span >&times;</span>
                                                                    </button>
                                                                </h6>
                                                                <span class="text-left">{{ $criteria->name }}</span>
                                                                <div class="d-flex justify-content-between">
                                                                    <span class="criteria-circles" data-value="{{ $criteria->excellent }}">{{ $criteria->excellent }}</span>
                                                                    <span class="criteria-circles" data-value="{{ $criteria->good }}">{{ $criteria->good }}</span>
                                                                    <span class="criteria-circles" data-value="{{ $criteria->meet_standard }}">{{ $criteria->meet_standard }}</span>
                                                                    <span class="criteria-circles" data-value="{{ $criteria->below_standard }}">{{ $criteria->below_standard }}</span>
                                                                    <span class="criteria-circles" data-value="{{ $criteria->weak }}">{{ $criteria->weak }}</span>
                                                                </div>
                                                                <div class="critriicons"></div>
                                                            </div>

                                                        </div>
                                                            {{-- @endif --}}
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
                                            <td class="position-relative">
                                                @if(isset($chunkArray[$i]))
                                                    <span id="total_results_{{ $chunkArray[$i]->id }}"> {{ $appraisalform->getTotalResult($chunkArray[$i]->id) != 0 ? $appraisalform->getTotalResult($chunkArray[$i]->id) : '' }} </span>
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



                        <div class="col-md-12 mt-2">

                            <button type="button" id="back-btn" class="btn btn-light btn-sm rounded-0" onclick="window.history.back();">{{ __('button.back')}}</button>
                            <input type="button" name="savedraft" class="btn btn-warning btn-sm rounded-0 savedraftbtns" value="{{ __('button.savedraft')}}" />


                            <button type="button" class="btn btn-success btn-sm rounded-0 submitbtns">{{ __('button.submit')}}</button>
                        </div>
                    </form>
                    <div class="pagination-controls mt-3 text-center">
                        <button type="button" id="prevPage" class="btn btn-secondary btn-sm me-1" disabled>Previous</button>

                        <span id="pageNumbers" class="d-inline-block"></span>

                        <button type="button" id="nextPage" class="btn btn-secondary btn-sm ms-1 {{ $assesseeChunks->count() <= 1 ? 'd-none' : '' }}">Next</button>
                    </div>
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

<iframe id="reprint_frame" name="reprint_frame" src="{{ url('appraisalformsshowprintframe/'.$appraisalform->id) }}" style="position: absolute;width:auto;height:auto;border:0;display: none;"  class=""></iframe>

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
                        title: "သတ်မှတ်ထားသော အဆင့်သတ်မှတ်ချက်များနှင့် မကိုက်ညီပါ။",
                        text: allowed.join(', ')+" ထဲမှ တစ်ခုကို ရွေးပါ" ,
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
                e.preventDefault(); // Prevent input blur
                const value = $(this).data('value');
                input.val(value);
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

{{--
        document.addEventListener('click', function (e) {

            if(e.target.className != 'custom-input'){
                console.log(e.target);
                $(".critooltips").addClass('d-none'); // Hide all tooltips first

            }
        }); --}}



        {{-- Start Global tooltip --}}
        {{-- $('.custom-input').focus(function () {
            let $input = $(this);
            let tooltip = $input.next('.critooltips').clone(); // clone the actual tooltip content

            // Set content and remove previous
            $('#global-tooltip').html(tooltip.html()).removeClass('d-none');

            // Get input offset
            let offset = $input.offset();
            console.log(offset.top);
            let inputHeight = $input.outerHeight();

            // Position tooltip above the input
            $('#global-tooltip').css({
                position:absolute,
                top: offset.top - 210 + 'px', // adjust based on your tooltip height
                left: offset.left + ($input.outerWidth() / 2) + 'px',
                transform: 'translateX(-50%)',
                position: 'absolute',
                zIndex: 9999
            });
        });

        $('.custom-input').blur(function () {
            $('#global-tooltip').addClass('d-none');
        });


        tippy('.custom-input', {
            content: 'My tooltip!',
        }); --}}

        {{-- End Global tooltip --}}

        {{-- End Tooltip --}}

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
                            {{-- pdfFrame1.focus(); --}}
                            pdfFrame1.print();
        });
        {{-- End Print Arera --}}
    });



    {{-- Start Pages --}}

    let currentPage = 0;
    const pages = document.querySelectorAll('.printableArea.page');
    const totalPages = pages.length;

    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');
    const pageNumbersContainer = document.getElementById('pageNumbers');

    // Create page number buttons
    for (let i = 0; i < totalPages; i++) {
        const btn = document.createElement('button');
        btn.textContent = i + 1;
        btn.classList.add('btn', 'btn-outline-primary', 'btn-sm', 'mx-1', 'page-number');
        btn.setAttribute('data-page', i);

        btn.addEventListener('click', function () {
            currentPage = i;
            updatePagination();
        });

        pageNumbersContainer.appendChild(btn);
    }

    const pageButtons = document.querySelectorAll('.page-number');

    function updatePagination() {
        pages.forEach((page, index) => {
            page.style.display = index === currentPage ? 'block' : 'none';
        });

        prevBtn.disabled = currentPage === 0;
        nextBtn.disabled = currentPage === totalPages - 1;

        pageButtons.forEach((btn, index) => {
            if (index === currentPage) {
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-primary');
            } else {
                btn.classList.add('btn-outline-primary');
                btn.classList.remove('btn-primary');
            }
        });
    }

    prevBtn.addEventListener('click', function () {
        if (currentPage > 0) {
            currentPage--;
            updatePagination();
        }
    });

    nextBtn.addEventListener('click', function () {
        if (currentPage < totalPages - 1) {
            currentPage++;
            updatePagination();
        }
    });

    updatePagination();
    {{-- End Pages --}}
</script>
@stop
