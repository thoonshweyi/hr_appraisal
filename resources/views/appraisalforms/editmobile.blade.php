@extends('layouts.app')

@section('content')
<div class="content-page">

    <div class="container-fluid">
        <div class="row">


            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-1">Performance Appraisal Assessment (Mobile)</h4>
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

    // Create page number buttons like Laravel pagination
    for (let i = 0; i < totalPages; i++) {
        const li = document.createElement('li');
        li.classList.add('page-item');

        const a = document.createElement('a');
        a.classList.add('page-link');
        a.classList.add('rounded-0');
        a.href = "#";
        a.textContent = i + 1;
        a.setAttribute('data-page', i);

        a.addEventListener('click', function (e) {
            e.preventDefault();
            currentPage = i;
            updatePagination();
        });

        li.appendChild(a);
        pageNumbersContainer.appendChild(li);
    }

    const pageButtons = pageNumbersContainer.querySelectorAll('.page-item');

    function updatePagination() {
        pages.forEach((page, index) => {
            page.style.display = index === currentPage ? 'block' : 'none';
        });

        // Toggle prev/next
        prevBtn.classList.toggle('disabled', currentPage === 0);
        nextBtn.classList.toggle('disabled', currentPage === totalPages - 1);

        // Highlight active page
        pageButtons.forEach((li, index) => {
            li.classList.toggle('active', index === currentPage);
        });
    }

    prevBtn.querySelector('a').addEventListener('click', function (e) {
        e.preventDefault();
        if (currentPage > 0) {
            currentPage--;
            updatePagination();
        }
    });

    nextBtn.querySelector('a').addEventListener('click', function (e) {
        e.preventDefault();
        if (currentPage < totalPages - 1) {
            currentPage++;
            updatePagination();
        }
    });

    updatePagination();
    {{-- End Pages --}}
</script>
@stop
