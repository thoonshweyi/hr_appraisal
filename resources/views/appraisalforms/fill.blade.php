<style>
    /* body {
        font-family: 'Arial', sans-serif;
        font-size: 12px;
        margin: 0px 8px;
    } */

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 5;
    }

    th, td {
        border: 1px solid #000;
        padding: 6px;
        text-align: center;
        vertical-align: top;
    }

    th {
        /* background-color: #f2f2f2; */
        text-align: center;
    }

    .text-left {
        text-align: left;
    }

    .header-bar {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 15px;
        text-align: center;
    }
    .assessor-infos strong{
        width: 50%;
    }
    .delimiter{
        width: 20px;
    }

    .total-row {
        font-weight: bold;
        background-color: #f9f9f9;
    }

    /*  */
    .notice-section {
        margin-top: 2px;
    }

    .flex-row {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
    }

    .col-50 {
        width: 48%;
    }

    .col-100 {
        width: 100%;
    }

    .list-unstyled {
        list-style: none;
        padding-left: 0;
        margin: 0;
    }

    .notice {
        font-weight: bold;
        text-decoration: underline;
        display: block;
        margin-bottom: 5px;
    }
    /*  */
    .vertical-header {
        writing-mode: vertical-rl;
        transform: rotate(180deg);
        padding: 5px 2px;
        font-size: 10px;
        text-align: center;
        width: 4% !important;
        min-width: 4% !important;;
        max-width: 4% !important;;

        vertical-align: middle;
    }
    .header-row td{
        padding: 5px 40px;
        text-align: left;
    }
    .assessor-infos {
        display: flex;
        align-items: center;
        padding: 6px;
        font-weight: bold;
    }


    .printableArea{
        /* height: 3508px; */
        margin-top: 8px;
    }

    .company-title{
        font-size: 14px;
        margin: 0px;
        margin-bottom: 5px;
    }
    .form-title{
        font-size: 13px;
    }

    .criteria-header{
        width: 60%;
        min-width: 60% !important;
        max-width: 60% !important;;
        vertical-align:middle;
        font-size:22px;
        letter-spacing: 8px;
    }
    .print-date{
        float:right;
    }
    .employees,.ratings{
        display: inline-block;
        width: 100%;
        height: 100%;
    }
</style>
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

    @foreach($assesseeusers as $branch=>$assesseeuserbybranch)
        @php
            $assesseeChunks = $assesseeuserbybranch->chunk(5);
        @endphp

        {{-- {{ dd($assesseeChunks) }} --}}
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
                                <span class="value">{{ $appraisalform->assessoruser->employee->subdepartment->name }}</span>
                            </div>
                        </td>
                        <th colspan="10" class="text-left">Assessees Location : {{ $branch }}</th>
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
                                    <input type="hidden" name="assessee_user_ids[]" value="{{ $chunkArray[$i]->id }}"/>
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
    @endforeach
    <script>

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

