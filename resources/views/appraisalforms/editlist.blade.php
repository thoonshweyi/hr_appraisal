@extends('layouts.app')

@section('content')
<!-- Loader Overlay -->
<div id="pageLoader">
  <div class="loader"></div>
</div>
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
                                    <script type="text/javascript">
                                        Swal.fire({
                                            title: "Form Submit Error!",
                                            text: "{{ $error }}",
                                            icon: "error"
                                        });
                                    </script>
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

            @php
                $isMobileView = session('view_mode') === 'mobile';
            @endphp

            <div class="col-md-12 mb-2 text-start">
                <button
                    type="submit"
                    form="appraisalformf"
                    formaction="{{ route('appraisalforms.switch-view', $appraisalform->id) }}"
                    name="view"
                    value="{{ $isMobileView ? 'desktop' : 'mobile' }}"
                    class="btn btn-primary"
                >
                    Switch to {{ $isMobileView ? 'Desktop' : 'Mobile' }} View
                </button>
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
                        {{--<nav aria-label="Pagination" class="mt-2">
                            <ul class="pagination justify-content-center" >
                                <li class="page-item" id="prevPage">
                                    <a class="page-link" href="#" aria-label="Previous">
                                    ‹
                                    </a>
                                </li>

                                <!-- Page buttons will be inserted here -->
                                <li id="pageNumbers" class="d-flex" style="flex-wrap:wrap;"></li>

                                <li class="page-item" id="nextPage">
                                    <a class="page-link" href="#" aria-label="Next">
                                    ›
                                    </a>
                                </li>
                            </ul>
                        </nav> --}}
                        <form id="appraisalformf" action="" method="POST">
                            @csrf
                            @method('PUT')

                                <div class="printableArea page" style="{{ 0 > 0 ? 'page-break-before: always;' : '' }}">

                                    <div class="table-responsive">
                                        <table class="assessmentformtable" style="width:100% !important;">
                                            <tr class="header-row">
                                                <td colspan="{{ 6+$assesseeusers->flatten()->count() }}">
                                                    {{-- <span style="" class="print-date">Print Date: {{ Carbon\Carbon::now()->format('d-M-Y') }}</span> --}}
                                                    <h4 class="company-title">PRO1 Global Company Co.,Ltd</h4>
                                                    <strong class="form-title">Assessment Form: {{ $appraisalform->assformcat->name }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="min-width: calc(60vw);" rowspan="2">
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
                                                        <span class="value">{{ $appraisalform->assessoruser->employee->subdepartment->name }}</span>
                                                    </div>
                                                </td>
                                                <th colspan="{{ 5+$assesseeusers->flatten()->count() }}" class="text-left">Assessees Location : {{-- $branch --}} -</th>
                                            </tr>

                                            <tr>
                                                <th colspan="{{ 5+$assesseeusers->flatten()->count() }}" class="text-left">Assessees (အမှတ်ပေးခံရမည့်သူ) </th>
                                            </tr>
                                            <!-- Header Row -->
                                            <tr>
                                                <th class="criteria-header" style="">CRITERIA</th>
                                                @foreach(['Excellent', 'Good', 'Meet', 'Below', 'Weak'] as $rating)
                                                <th class="vertical-header"> 
                                                    <span class="">
                                                        {{ $rating }}
                                                    </span>
                                                </th>
                                                @endforeach

                                                @foreach($assesseeusers as $branch=>$assesseeuserbybranch)
                                                    @foreach($assesseeuserbybranch as $assesseeuser)
                                                    <th class="vertical-header">
                                                        <span class="employees">
                                                        @if(isset($assesseeuser))
                                                            {{ $assesseeuser->employee->employee_name }} {{-- "Lorem Ipsum is simply" --}}
                                                        @else
                                                            &nbsp;
                                                        @endif
                                                        </span>
                                                    </th>
                                                    @endforeach
                                                @endforeach
                                            </tr>

                                            <!-- Criteria Rows -->
                                            @foreach ($criterias as $idx=>$criteria)
                                                <tr>
                                                    <td class="text-left"><span class="rating-marks">{{ $criteria->name }}</span></td>
                                                    <td style="vertical-align: middle"><span class="rating-marks">{{ $criteria->excellent }}</span></td>
                                                    <td><span class="rating-marks">{{ $criteria->good }}</span></td>
                                                    <td><span class="rating-marks">{{ $criteria->meet_standard }}</span></td>
                                                    <td><span class="rating-marks">{{ $criteria->below_standard }}</span></td>
                                                    <td><span class="rating-marks">{{ $criteria->weak }}</span></td>

                                                    @foreach($assesseeusers as $branch=>$assesseeuserbybranch)
                                                        @foreach($assesseeuserbybranch as $assesseeuser)
                                                        <td class="position-relative">
                                                            @if(isset($assesseeuser))
                                                                <div class="position-relative">

                                                                    <input type="number" name="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]" class="custom-input" max="{{ $criteria->excellent }}" min="{{ $criteria->weak }}"
                                                                    value="{{ old('appraisalformresults') ? old('appraisalformresults')[$assesseeuser->id][$criteria->id] :  $preloadresults[$assesseeuser->id][$criteria->id]->result ?? '' }}"  data-valids="{{ implode(',', $criteria->getRatingScaleAttribute()) }}"
                                                                    data-assessee="{{ $assesseeuser->id }}" data-assessee-name="{{ $assesseeuser->employee->employee_name }}" data-criteria-name="{{ $criteria->name }}"
                                                                    readonly/>
                                                                    {{-- @if($i == 0 && $idx == 0) --}}
                                                                    <div class="critooltips invisible">
                                                                        <h6> <span>{{ $assesseeuser->employee->employee_name }}</span>
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
                                                        @endforeach
                                                    @endforeach
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
                                                @foreach($assesseeusers as $branch=>$assesseeuserbybranch)
                                                    @foreach($assesseeuserbybranch as $assesseeuser)
                                                    <td class="position-relative">
                                                        @if(isset($assesseeuser))
                                                            <span id="total_results_{{ $assesseeuser->id }}"> {{ $appraisalform->getTotalResult($assesseeuser->id) != 0 ? $appraisalform->getTotalResult($assesseeuser->id) : '' }} </span>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    @endforeach
                                                @endforeach
                                            </tr>

                                            <tr>
                                                <td colspan="6">Notes:</td>
                                                <td colspan="{{ $assesseeusers->flatten()->count() }}">Voter's Signature:</td>
                                            </tr>
                                        </table>

                                            <!-- Notice Section -->
                                            <div class="notice-section">
                                                <span class="notice">အမှတ်ပေးသူများသတိပြုရန်</span>
                                                <div class="flex-row">
                                                    <div class="col-50">
                                                        <ul class="list-unstyled">
                                                            <li>၁။ မိမိပေးသောအမှတ်ကို မိမိတာဝန်ယူရမည်။</li>
                                                            <li>၂။ အမှတ်ပေးရာတွင် အောက်ပါအချက်များကို သတိပြုရှောင်ကြဉ်ရမည်။</li>
                                                            <li>(က) တစ်ချက်ကောင်းမြင်ရုံနှင့် အမှတ်များများပေးခြင်း။</li>
                                                            <li>(ခ) တစ်ချက်ဆိုးမြင်ရုံနှင့် အမှတ်နဲနဲပေးခြင်း။</li>
                                                            <li>(ဂ) လတ်တလောအခြေအနေကြည့်ပြီး အမှတ်ပေးခြင်း။</li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-50">
                                                        <ul class="list-unstyled">
                                                            <li>(ဃ) မျက်နှာလိုက်ပြီး အမှတ်ပေးခြင်း။</li>
                                                            <li>(င) အမှတ်ပေးကပ်စီးနဲခြင်း။</li>
                                                            <li>(စ) အမှတ်ပေးရက်ရောခြင်း။</li>
                                                            <li>(ဆ) အမြဲတမ်းပျမ်းမျှပေးခြင်း။</li>
                                                            <li>(ဇ) စိတ်မကြည်လင်သော အချိန်ပေးခြင်း။</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </div>


                            @if($appraisalform->status_id != 19 || Auth::id() == 1)
                            <div class="col-md-12 mt-3 d-flex flex-wrap align-items-center appraisal-action-buttons">

                                <button type="button" id="back-btn" class="btn btn-light back-btn appraisal-action-button">{{ __('button.back')}}</button>
                                <input type="button" name="savedraft" class="btn btn-warning savedraftbtns appraisal-action-button" value="{{ __('button.savedraft')}}" />


                                <button type="button" class="btn btn-success submitbtns appraisal-action-button">{{ __('button.submit')}}</button>
                            </div>
                            @endif
                        </form>

                    </div>
                </div>
           </div>


        </div>
    </div>


</div>

</div>

<!-- START MODAL AREA -->





<!-- End MODAL AREA -->

<iframe id="reprint_frame" name="reprint_frame" src="{{ url('appraisalformsshowprintframe/'.$appraisalform->id) }}" style="position: absolute;width:auto;height:auto;border:0;display: none;"  class=""></iframe>

@endsection

@section('css')
     <link href="{{ asset('assets/dist/css/pageloader.css') }}" rel="stylesheet" />    
@endsection

@section('js')

<script>

    const draftRedirectUrl = @json(
        adminHRAuthorize()
            ? route('appraisalcycles.edit', $appraisalform->appraisal_cycle_id)
            : route('appraisalforms.notification')
    );

    $(document).ready(function() {


        let typingTimer;

        $('.custom-input').on('input', function () {
            const $input = $(this);
            // this.value = this.value.replace(/[^0-9]/g, ""); 

            clearTimeout(typingTimer);

            typingTimer = setTimeout(() => {
                validateScore($input);
            }, 300); // wait 300ms after user stops typing
        });
        function validateScore($input) {
            const allowed = $input.data('valids').toString().split(',').map(Number);
            const value = parseInt($input.val());

            if (isNaN(value) || !allowed.includes(value)) {
                Swal.fire({
                    icon: "warning",
                    title: "သတ်မှတ်ထားသော အဆင့်သတ်မှတ်ချက်များနှင့် မကိုက်ညီပါ။",
                    text: allowed.join(', ') + " ထဲမှ တစ်ခုကို ရွေးပါ",
                    scrollbarPadding: false
                });
                $input.val('');
            }

            updateTotals();
            autofocusNextInput($input);
        }

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
        updateTotals();

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
        
        let getInputMethod = localStorage.getItem('inputmethod') ?? 'mouse';
        console.log(getInputMethod);
        let allowKeyboard = getInputMethod == 'keyboard';
        console.log(allowKeyboard);
        let adminHRAuthorize = @json(adminHRAuthorize()); 
        if (allowKeyboard && adminHRAuthorize) {
            $('.custom-input').removeAttr('readonly');
        } else {
            $('.custom-input').attr('readonly', true);
        }
        $('.custom-input').focus(function () {


            $(".critooltips").addClass('invisible'); // Hide all tooltips first

            allowKeyboard && adminHRAuthorize ? '' : $(this).next(".critooltips").removeClass('invisible'); // **** Show the specific tooltip for the input

            const $tableWrapper = $(this).closest('.table-responsive');
            const scrollWidth = $tableWrapper[0].scrollWidth;

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
            $(this).closest('.critooltips').addClass('invisible');
        });

        $(document).on('mousedown',function(e){
            {{-- console.log(e.target.classList.contains('custom-input')); --}}
            {{-- console.log(e.target.closest('.critooltips')) --}}

            if(!e.target.classList.contains('custom-input') && !e.target.closest('.critooltips')){
                $('.critooltips').addClass('invisible');
            }
        });
        {{-- End Tooltip --}}


        let confirmClicked = false;
        let submitting = false;
        $('.submitbtns').click(function(e){
            if (submitting) return; 

            if (!validateAssesseeCompletion()) return;

            Swal.fire({
                title: "{{ __('apprasialform.result_submit')}}",
                text: "",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "{{ __('message.ok')}}",
                cancelButtonText: "{{ __('message.cancel')}}",
            }).then((result) => {
                if (result.isConfirmed && !confirmClicked) {
                    confirmClicked = true;
                    submitting = true;

                    $('#pageLoader').fadeIn();

                    $('#appraisalformf').attr('action','{{ route('appraisalforms.update',$appraisalform->id) }}');
                    // $('#appraisalformf').submit();

                    $.ajax({
                        url:  $('#appraisalformf').attr('action'),
                        type:"POST",
                        dataType: "json",
                        data:$("#appraisalformf").serialize(),
                        success:function(response){
                            console.log(response);

                            const data = response;
                            const appraisalform = data.data;

                            if(data.success){
                                Swal.fire({
                                    icon: "success",
                                    title: "Finished!",
                                    text: data.message,
                                });
                                setTimeout(() => {                                            
                                    window.location.replace(draftRedirectUrl);
                                }, 3000);
                            }else{
                                Swal.fire({
                                    icon: "error",
                                    title: "Submit Error!!",
                                    text: `${data.message}`,
                                });
                            }
                        },
                        error:function(response){
                            console.log("Error: ",response);

                            Swal.fire({
                                icon: "error",
                                title: "Submit Error!!",
                                text: "Something went wrong while submiting Appraisal Form.",
                            });
                        },
                        complete:function(resopnse){
                            confirmClicked = false;
                            submitting = false;

                            $('#pageLoader').fadeOut();
                        }
                    });
                }
            });
        });

        {{-- Start Save Draft --}}
        $('.savedraftbtns').click(function(e){
            e.preventDefault();
            let $btn = $(this);
            if ($btn.prop('disabled')) return; 

            $btn.prop('disabled', true);      
            $btn.val('Saving...');            
            $('#pageLoader').fadeIn();

            $('#appraisalformf')
                .attr('action', '{{ route('appraisalforms.savedraft', $appraisalform->id) }}')
            
            $.ajax({
                url:  $('#appraisalformf').attr('action'),
                type:"POST",
                dataType: "json",
                data:$("#appraisalformf").serialize(),
                success:function(response){
                    console.log(response);

                    const data = response;
                    const appraisalform = data.data;

                    if(data.success){
                         
                        Swal.fire({
                            icon: "success",
                            title: "Saved!",
                            text: data.message,
                        });
                        setTimeout(() => {                                            
                            window.location.replace(draftRedirectUrl);
                        }, 3000);
                    }else{
                        Swal.fire({
                            icon: "error",
                            title: "Save Error!!",
                            text: `${data.message}`,
                        });
                    }
                },
                error:function(response){
                    console.log("Error: ",response);

                    Swal.fire({
                        icon: "error",
                        title: "Save Error!!",
                        text: "Something went wrong while saving Appraisal Form.",
                    });
                },
                complete:function(resopnse){
                    $('#pageLoader').fadeOut();
                    $btn.prop('disabled', false);      
                }
            });
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

    {{-- Start Back Btn --}}
    $(".back-btn").click(function(){
        Swal.fire({
            title: "{{ __('apprasialform.result_save')}}",
            text: "",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "{{ __('button.save')}}",
            cancelButtonText: "{{ __('message.cancel')}}",
        }).then((result) => {
            if (result.isConfirmed) {
                $('.savedraftbtns').click();
            }else{
                window.location.replace(draftRedirectUrl);
            }
        });

    })
    {{-- End Back Btn --}}

    // Asessee တစ်sယောက်လုံး ဘာ Criteria မှ မဖြည့်ရသေးဘူးလား
    // Assessee အားလုံးမှာ နည်းနည်းစီတော့ ဖြည့်ထားတယ်၊ ဒါပေမယ့် Criteria အကွက်တချို့ လွတ်နေသေးလား
    function validateAssesseeCompletion(){
        let completelyEmptyAssessees = [];
        let incompleteAssessees = [];

        let assessees = {};

        $('input.custom-input').each(function () {

            let assesseeId = $(this).data('assessee');
            let assesseeName = $(this).data('assessee-name');

            if (!assessees[assesseeId]) {
                assessees[assesseeId] = {
                    name: assesseeName,
                    id: assesseeId,
                    total: 0,
                    filled: 0
                };
            }

            let criteriaName = $(this).attr('name');

            if (!assessees[assesseeId].criteria) {
                assessees[assesseeId].criteria = {};
            }

            if (!assessees[assesseeId].criteria[criteriaName]) {
                assessees[assesseeId].criteria[criteriaName] = {
                    filled: false
                };

                assessees[assesseeId].total++;
            }

            let $input = $(this);
            let inputType = $input.attr('type');
            let isFilled = false;
            if (inputType === 'radio') {
                isFilled = $input.is(':checked');
            } else if (inputType === 'number') {
                isFilled = $input.val().trim() !== '';
            }

            if (isFilled) {
                assessees[assesseeId].criteria[criteriaName].filled = true;
            }
        });
        // console.log(assessees); return false;


        $.each(assessees, function (id, assessee) {

            $.each(assessee.criteria, function (criteriaName, criteria) {

                if (criteria.filled) {
                    assessee.filled++;
                }

            });

            // Criteria တစ်ခုမှ မရွေးရသေး
            if (assessee.filled === 0) {
                completelyEmptyAssessees.push(assessee.name || assessee.id);
            } else if (assessee.filled < assessee.total) {
                incompleteAssessees.push(assessee.name || assessee.id);
            }
        });


        console.log('Completely Empty:', completelyEmptyAssessees);
        console.log('Incomplete:', incompleteAssessees);
        if (completelyEmptyAssessees.length > 0){
            Swal.fire({
                icon: "warning",
                title: "Submission Failed",
                text: @json(__('apprasialform.emloyee_remaining')),
            });
            return false;
        }
        if(incompleteAssessees.length > 0){
            Swal.fire({
                icon: "warning",
                title: "Submission Failed",
                text: @json(__('apprasialform.criteria_missing')),
            });
            return false;
        }

        return true;
    }


 
</script>
@stop
