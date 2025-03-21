<!DOCTYPE html>
<html>
<head>
    <title>Appraisal Form</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10px;
            margin: 0px 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
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

        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .notice-section {
            margin-top: 25px;
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
            margin-bottom: 10px;
        }

        .vertical-header {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            padding: 5px 2px;
            font-size: 10px;
            text-align: center;
            width: 5%;
            vertical-align: middle;
        }
        .header-row td{
            padding-left: 20px;
            text-align: left;
        }
        .assessor-infos{
            padding: 6px;
        }

        .printableArea{
            /* height: 3508px; */
            margin-top: 8px;
        }
    </style>
</head>
<body>
    @php
        $assesseeChunks = $assesseeusers->chunk(5);
    @endphp

    {{-- {{ dd($assesseeChunks) }} --}}
  @foreach($assesseeChunks as $chunkIndex => $chunk)
    <div class="printableArea" style="{{ $chunkIndex > 0 ? 'page-break-before: always;' : '' }}">
        <table>
            <tr class="header-row">
                <td colspan="11">
                    <h4>PRO1 Global Company Co.,Ltd</h4>
                    <strong>Assessment Form:</strong> {{ $appraisalform->assformcat->name }}
                </td>
            </tr>
            <tr>
                <td class="text-left">
                    <div class="assessor-infos"><strong>Assessor Name:</strong> {{ $appraisalform->assessoruser->employee->employee_name }}</div>
                    <div class="assessor-infos"> <strong>Employee Code:</strong> {{ $appraisalform->assessoruser->employee->employee_code }}</div>
                    <div class="assessor-infos"><strong>Department:</strong> {{ $appraisalform->assessoruser->employee->department->name }}</div>
                    <div class="assessor-infos"><strong>Position:</strong> {{ $appraisalform->assessoruser->employee->position->name }}</div>
                </td>
                <td colspan="10">Assessees:</td>
            </tr>

            <!-- Header Row -->
            <tr>
                <th style="width: 50%">Criteria Description</th>
                @foreach(['Excellent', 'Good', 'Meet', 'Below', 'Weak'] as $rating)
                    <th class="vertical-header">{{ $rating }}</th>
                @endforeach
                @php $chunkArray = $chunk->values(); @endphp
                @for($i = 0; $i < 5; $i++)
                    <th class="vertical-header">
                        @if(isset($chunkArray[$i]))
                            {{ $chunkArray[$i]->employee->employee_name }}
                        @else
                            &nbsp;
                        @endif
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
@endforeach

</body>
</html>
