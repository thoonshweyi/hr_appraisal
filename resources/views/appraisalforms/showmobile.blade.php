@extends('layouts.app')

@section('content')
<a href="#assesseemodal" class="fab expanded text-white" id="mainFab" data-toggle="modal">
    <div class="fab-content">
        <span class="fab-icon">
            <i class="fas fa-arrow-circle-right"></i>
            {{-- <img src="{{ asset('./images/expand-up-down-line.svg') }}" alt=""> --}}
        </span>
        <span class="fab-text" id="fabAssesseeName">Assessee</span>
    </div>
</a>






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
                <div class="form-header mb-2" style="position: sticky;">
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
                            <label for="assessees" class="form-label"><strong>Assessees (အမှတ်ပေးခံရမည့်သူ) </strong>
                            <span class="delimiter">-</span>
                            <span><a href="#assesseemodal" data-toggle="modal">{{ $assesseeusers->count()  }} persons</span></a>
                            </label>
                            <select class="form-control" id="current_assessees">
                                @foreach($assesseeusers as $assesseeuser)
                                    <option value="{{$assesseeuser->id}}">{{ $assesseeuser->employee->employee_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                                        {{-- {{ dd(old('appraisalformresults')) }} --}}


                        @foreach($assesseeusers as $assesseeuser)
                            <div id="assessee_{{ $assesseeuser->id }}_criterias" class="assessee_criterias" style="display: none;" data-assessee="{{ $assesseeuser->id }}">
                            @foreach ($criterias as $idx=>$criteria)

                                <div class="form-card">
                                    <div class="section-title">{{ $criteria->name }}</div>
                                    <div class="score-radio d-flex flex-wrap">
                                        <div class="form-check me-2">
                                            <input class="form-check-input custom-input" type="radio" name="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]" id="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]-1" value="{{ $criteria->excellent }}"
                                                {{ (old('appraisalformresults') && isset(old('appraisalformresults')[$assesseeuser->id][$criteria->id]) && old('appraisalformresults')[$assesseeuser->id][$criteria->id] == $criteria->excellent)
                                                    ? 'checked'
                                                    : ($appraisalform->getResult($assesseeuser->id, $criteria->id) == $criteria->excellent ? 'checked' : '')
                                                }}

                                                data-assessee="{{ $assesseeuser->id }}"
                                                disabled
                                            />
                                            <label class="custom-input" for="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]-1">{{ $criteria->excellent }}</label>
                                        </div>

                                        <div class="form-check me-2">
                                            <input class="form-check-input custom-input" type="radio" name="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]" id="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]-2" value="{{ $criteria->good }}"
                                            {{ (old('appraisalformresults') && isset(old('appraisalformresults')[$assesseeuser->id][$criteria->id]) && old('appraisalformresults')[$assesseeuser->id][$criteria->id] == $criteria->good)
                                                    ? 'checked'
                                                    : ($appraisalform->getResult($assesseeuser->id, $criteria->id) == $criteria->good ? 'checked' : '') }}
                                                data-assessee="{{ $assesseeuser->id }}"
                                                disabled
                                            />
                                            <label class="custom-input" for="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]-2">{{ $criteria->good }}</label>
                                        </div>

                                        <div class="form-check me-2">
                                            <input class="form-check-input custom-input" type="radio" name="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]" id="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]-3" value="{{ $criteria->meet_standard }}"
                                                {{ (old('appraisalformresults') && isset(old('appraisalformresults')[$assesseeuser->id][$criteria->id]) && old('appraisalformresults')[$assesseeuser->id][$criteria->id] == $criteria->meet_standard)
                                                    ? 'checked'
                                                    : ($appraisalform->getResult($assesseeuser->id, $criteria->id) == $criteria->meet_standard ? 'checked' : '') }}
                                                data-assessee="{{ $assesseeuser->id }}"
                                                disabled
                                            />
                                            <label class="custom-input" for="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]-3">{{ $criteria->meet_standard }}</label>
                                        </div>

                                        <div class="form-check me-2">
                                            <input class="form-check-input custom-input" type="radio" name="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]" id="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]-4" value="{{ $criteria->below_standard }}"
                                            {{ (old('appraisalformresults') && isset(old('appraisalformresults')[$assesseeuser->id][$criteria->id]) && old('appraisalformresults')[$assesseeuser->id][$criteria->id] == $criteria->below_standard)
                                                    ? 'checked'
                                                    : ($appraisalform->getResult($assesseeuser->id, $criteria->id) == $criteria->below_standard ? 'checked' : '') }}
                                                data-assessee="{{ $assesseeuser->id }}"
                                                disabled
                                            />
                                            <label class="custom-input" for="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]-4">{{ $criteria->below_standard }}</label>
                                        </div>

                                        <div class="form-check me-2">
                                            <input class="form-check-input custom-input" type="radio" name="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]" id="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]-5" value="{{ $criteria->weak }}"
                                            {{ (old('appraisalformresults') && isset(old('appraisalformresults')[$assesseeuser->id][$criteria->id]) && old('appraisalformresults')[$assesseeuser->id][$criteria->id] == $criteria->weak)
                                                    ? 'checked'
                                                    : ($appraisalform->getResult($assesseeuser->id, $criteria->id) == $criteria->weak ? 'checked' : '') }}
                                                data-assessee="{{ $assesseeuser->id }}"
                                                disabled
                                            />
                                            <label class="custom-input" for="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]-5">{{ $criteria->weak }}</label>
                                        </div>
                                        <input type="hidden" name="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]"  value="">
                                        {{-- @php
                                            $checked = in_array($appraisalform->getResult($assesseeuser->id,$criteria->id),[$criteria->excellent,$criteria->good,$criteria->meet_standard, $criteria->below_standard,$criteria->weak  ])
                                        @endphp
                                            @if (!$checked)
                                            <input type="hidden" name="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]"  value="">
                                        @endif --}}
                                    </div>
                                </div>
                            @endforeach
                                <div class="form-card">
                                    <span>Total Score: </span>
                                    <span id="total_results_{{ $assesseeuser->id }}"> {{ $appraisalform->getTotalResult($assesseeuser->id) != 0 ? $appraisalform->getTotalResult($assesseeuser->id) : '' }} </span>
                                </div>
                            </div>
                        @endforeach


                {{-- <div class="navigation-bar">
                    <button class="btn" id="prevBtn"><i class="fas fa-chevron-left"></i></button>
                    <h6 id="assesseeName">Yin Min Hlaing</h6>
                    <button class="btn" id="nextBtn"><i class="fas fa-chevron-right"></i></button>
                </div> --}}

           </div>


            <div class="col-md-12 mt-2">

                <button type="button" id="back-btn" class="btn btn-light btn-sm rounded-0 back-btn">{{ __('button.back')}}</button>
            </div>
        </div>
    </div>


</div>

</div>

<!-- START MODAL AREA -->

   <!-- start edit modal -->
    <div id="assesseemodal" class="modal fade">
        <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-0 mycontent">
                    <div class="modal-header">
                        <h6 class="modal-title">Select an Assessee</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    {{-- <div class="modal-body "> --}}

                        <div class="form-groups assesseesearch">
                            <input type="text" id="modalSearchInput" class="form-control modal-search-input" placeholder="Search by name or role...">
                        </div>

                        <div class="assessee-list" id="assesseeList">

                            @foreach($assesseeusers as $assesseeuser)
                            <div id="assessee-list-item{{$assesseeuser->id}}" class="assessee-list-item" data-assessee="{{ $assesseeuser->id }}">
                                <div class="assessee-item-details">
                                    <div class="name">{{ $assesseeuser->name }}</div>
                                    <div class="role">{{ $assesseeuser->employee->position->name }}</div>
                                </div>
                                <div class="assessee-total">
                                    <span class="total_results_{{ $assesseeuser->id }}">{{ $appraisalform->getTotalResult($assesseeuser->id) != 0 ? $appraisalform->getTotalResult($assesseeuser->id) : '0' }}</span>
                                </div>
                                <span id="assesseestatuses{{$assesseeuser->id}}" class="status-badge status-completed assesseestatuses">completed</span>
                            </div>
                            @endforeach

                        </div>
                    {{-- </div> --}}

                    <div class="modal-footer">

                    </div>
                </div>
        </div>
    </div>
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



        .fab {


            background-color: #007bff;
            border: none;
            color: white;
            width: 56px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 2em;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.2s ease, width 0.3s ease, border-radius 0.3s ease, font-size 0.3s ease;

            position: fixed;
            bottom: 25px;
            right: 25px;
            z-index: 900;
            overflow: hidden; /* Hide overflowing text when not expanded */
        }

        .fab:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        /* Dynamic Assessee Indicator - integrated with FAB */
        .fab.expanded {
            width: auto; /* Allow width to expand */
            padding: 0 10px; /* Add padding for text */
            border-radius: 28px; /* Pill shape when expanded */
            font-size: 1em; /* Smaller font for text */
            justify-content: flex-start; /* Align text to start */
            padding-right: 15px; /* Adjust padding */
        }

        .fab-content {
            display: flex;
            align-items: center;
            width: 100%;
        }

        .fab-icon {
            flex-shrink: 0;
            font-size: 2em;
            margin-right: 0;
            transition: margin-right 0.3s ease;
        }

        .fab.expanded .fab-icon {
            margin-right: 10px;
            font-size: 1.2em;
        }

        .fab-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            opacity: 0; /* Start hidden */
            transition: opacity 0.2s ease;
            flex-grow: 1;
        }

        .fab.expanded .fab-text {
            opacity: 1; /* Fade in text when expanded */
        }

    /* Start Assessee Modal */
        .mycontent {
            background-color: #fff;
            border-radius: 12px;
            width: 100%;
            max-width: 500px !important;
            max-height: 90vh !important;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }

        .assessee-list {
            flex-grow: 1 !important;
            overflow-y: auto;
            padding: 0 20px 10px;
        }

        .assessee-list-item {
            display: flex;
            /* justify-content: space-between; */
            align-items: center;
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background-color 0.2s ease;
            border-radius: 8px;
            margin-bottom: 5px;
        }

        .assessee-list-item:last-child {
            border-bottom: none;
        }

        .assessee-list-item:hover {
            background-color: #f0f8ff;
        }

        .assessee-list-item.selected {
            background-color: #e6f2ff;
            border-color: #007bff;
            font-weight: 600;
        }

        .assessee-item-details {
            flex-grow: 1;
            font-size: 1.05em;
        }
        .assessee-item-details .name {
            font-weight: 600;
            color: #333;
        }
        .assessee-item-details .role {
            font-size: 0.85em;
            color: #666;
        }

        .status-badge {
            font-size: 0.75em;
            padding: 0px 8px;
            border-radius: 12px;
            margin-left: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background-color: #e9ecef;
            color: #495057;
        }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-in-progress { background-color: #cfe2ff; color: #084298; }
        .status-completed { background-color: #d1e7dd; color: #0f5132; }


        .assesseesearch{
            padding: 0 20px 10px;
        }

        .assessee-total{
            width: 35px;
            height: 35px;
            background-color: #e6f2ff;
            border-radius: 50%;
            border: 2px solid #f0f0f0;
            border-color: #007bff;
            color: #007bff;

            display: flex;
            justify-content: center;
            align-items: center;
        }
    /* End Assessee Modal */
</style>
@endsection

@section('js')
<script>
    $(document).ready(function() {


        $('.custom-input').on('click', function () {
            const $input = $(this);
            {{-- const allowed = $input.data('valids').toString().split(',').map(Number);
            const value = parseInt($input.val()); --}}

            {{-- if ($input.val() !== '' && !allowed.includes(value)) {
                Swal.fire({
                    icon: "warning",
                    title: "သတ်မှတ်ထားသော အဆင့်သတ်မှတ်ချက်များနှင့် မကိုက်ညီပါ။",
                    text: allowed.join(', ') + " ထဲမှ တစ်ခုကို ရွေးပါ",
                });
                $input.val('');
            } --}}

            updateTotals();
            autofocusNextInput($input);
            updateAssesseeStatus();
        });

        function updateTotals() {
            const totals = {};

            // Reset all totals
            document.querySelectorAll('[id^="total_results_"]').forEach(span => {
                span.textContent = '0';
            });

            // Sum up scores by assessee
            document.querySelectorAll('.form-check-input.custom-input:checked').forEach(input => {
                const assesseeId = input.dataset.assessee;
                const val = parseFloat(input.value);
                console.log(val);

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

                $(`.total_results_${id}`).text(totals[id]);

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




        {{-- Start Target Each Assessee  --}}

        $('#current_assessees').change(function(){
            let val = this.value;
            console.log(val);

            $('.assessee_criterias').css('display', 'none');

            $(`#assessee_${val}_criterias`).css('display', 'block');

            $('#fabAssesseeName').text($(this).find("option:selected").text());

            $('.assessee-list-item').removeClass('selected');
            $('#assessee-list-item'+val).addClass('selected');
        });
        $('#current_assessees').trigger('change')

        $('.form-check-input').change(function(){
               if ($(this).is(':checked')) {
                    var name = $(this).attr('name');
                    $('input[type="hidden"][name="' + name + '"]').remove();
                }
        });
        $('.form-check-input').trigger('change')


        $(document).on("click",'.assessee-list-item',function(){
            let assessee = $(this).data('assessee');
            {{-- console.log(assessee); --}}

            $('#current_assessees').val(assessee);
            $('#current_assessees').trigger('change')
            $('#assesseemodal').modal('hide');

        });



        const searchInput = document.getElementById("modalSearchInput");
        const listItems = document.querySelectorAll(".assessee-list-item");

        searchInput.addEventListener("input", function () {
            const searchText = this.value.toLowerCase();

            listItems.forEach(item => {
                const name = item.querySelector(".name").textContent.toLowerCase();
                const role = item.querySelector(".role").textContent.toLowerCase();

                if (name.includes(searchText) || role.includes(searchText)) {
                    item.style.display = "flex";
                } else {
                    item.style.display = "none";
                }
            });
        });

        function updateAssesseeStatus(){
            {{-- console.log(allresults); --}}

            $(".assessee_criterias").each(function(idx,ele){
                const allresults = $(this).find(".form-check-input.custom-input");
                const names = Array.from(allresults).map(input => input.name);
                const uniqueNames = new Set(names);
                {{-- console.log(uniqueNames.size); --}}
                const allresultscount = uniqueNames.size;



                const checkedresults = $(this).find('.form-check-input.custom-input:checked');
                const checkedresultscount = checkedresults.length;
                {{-- console.log(checkedresultscount) --}}

                let statusname = "";
                let status_id = "";
                if(allresultscount == checkedresultscount){
                    console.log("Finished")
                    status_id = 19;
                    statusname = "Finished";
                }else if(allresultscount >= checkedresultscount && checkedresultscount > 0){
                    console.log("In Progress");
                    status_id = 20;
                    statusname = "In Progress";
                }else if(allresultscount >= checkedresultscount && checkedresultscount == 0){
                    console.log("Pending");
                    status_id = 21;
                    statusname = "On Hold";
                }



                let assessee = $(this).data('assessee');
                console.log(assessee);
                const assesseestatus = $(`#assesseestatuses${assessee}`);
                $(`#assesseestatuses${assessee}`).text(statusname)

                switch(status_id){
                    case 19:
                        assesseestatus.attr('class', 'status-badge status-completed');
                        break;
                    case 20:
                        assesseestatus.attr('class', 'status-badge status-in-progress');
                        break;
                    case 21:
                        assesseestatus.attr('class', 'status-badge status-pending');
                        break;
                }



                {{-- console.log(checkedresults); --}}
            });

        }
        updateAssesseeStatus();
        {{-- End Target Each Assessee --}}



        {{-- Start Back Btn --}}
        $(".back-btn").click(function(){
            window.history.back();

        })
        {{-- End Back Btn --}}
    });


</script>
@stop
