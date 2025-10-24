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
                    @can('print-appraisal-form')
                    <div class="col-auto mb-2">
                        {{-- <a href="{{ route('appraisalforms.printpdf',$appraisalform->id) }}" class="btn  cus_btn">Print</a> --}}
                        <a href="javascript:void(0);" class="btn cus_btn">{{ __('button.print_document')}}</a>

                    </div>
                    @endcan

                    <div class="col-md-12">
                        <nav aria-label="Pagination" class="mt-2">
                            <ul class="pagination justify-content-center">
                                <li class="page-item" id="prevPage">
                                    <a class="page-link" href="#" aria-label="Previous">
                                    ‹
                                    </a>
                                </li>

                                <!-- Page buttons will be inserted here -->
                                <li id="pageNumbers" class="d-flex"></li>

                                <li class="page-item" id="nextPage">
                                    <a class="page-link" href="#" aria-label="Next">
                                    ›
                                    </a>
                                </li>
                            </ul>
                        </nav>

                        @php
                        $assesseeChunks = $assesseeusers->chunk(5);
                        @endphp
                        @foreach($assesseeChunks as $chunkIndex => $chunk)

                        <div class="printableArea page" style="{{ $chunkIndex > 0 ? 'page-break-before: always;' : '' }}">
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
                                                <span class="value">{{ $appraisalform->assessoruser->employee->attachformtype->name }}</span>
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
                                                    @if(isset($chunkArray[$i]) && !branchHR())
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
                                                @if(isset($chunk[$i]) && !branchHR())
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

<iframe id="reprint_frame" name="reprint_frame" src="{{ url('appraisalformsshowprintframe/'.$appraisalform->id) }}" style="position: absolute;width:auto;height:auto;border:0;display: none;"  class=""></iframe>

@endsection
@section('js')
<script>
    $(document).ready(function() {


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


    {{-- Start Print Area --}}
    document.querySelector('.cus_btn').addEventListener('click', function () {
        var pdfFrame1 = window.frames["reprint_frame"];
                        {{-- pdfFrame1.focus(); --}}
                        pdfFrame1.print();
    });
    {{-- End Print Arera --}}

</script>
@stop

