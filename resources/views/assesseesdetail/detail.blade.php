<!DOCTYPE html>
<html>
    <head>
        <title>Assessee Detail</title>
        <style>
            body{
                box-sizing: border-box
            }
            table{
                text-align: center;
                vertical-align: middle;
                table-layout: auto;
            }
            td,
            th {
                border: 1px solid #000;
                vertical-align: middle !important;
            }
        </style>
    </head>
    <body>
        <table class="table table-striped table-hover table-bordered bg-white " style="width:100%; vertical-align: middle;">
            <tr  height="60">
                <td colspan="16" style="text-align:center;vertical-align: middle;font-family:'Times New Roman'; font-size:14; font-weight: bold; word-wrap: break-word; text-align:center; margin:auto;">
                    <div>
                        <h4>Pro 1 Global CO., LTD</h4>
                        <h5>Appraisal Name: 2025 Test</h5>
                    </div>
                </td>
            </tr>



            {{-- {{ dd($assesseeusers) }} --}}
            @foreach($assesseeusers as $assesseeuser)
                @foreach($assesseeuser->getAssFormCats() as $assformcatidx=>$assformcat)
                    <tr style="{{ $assformcatidx <= 0 ?  'background-color: #d0f0d0;' : 'background-color: #D9D9D9;' }}">
                        @if($assformcatidx <= 0)
                        <th style="text-align:center;vertical-align: middle;{{ $assformcatidx <= 0 ?  'background-color: #d0f0d0;' : 'background-color: #D9D9D9;' }}" rowspan="2" >Assessee</th>
                        @else
                        <th rowspan="2" style="text-align:center;vertical-align: middle;{{ $assformcatidx <= 0 ?  'background-color: #d0f0d0;' : 'background-color: #D9D9D9;' }}"></th>
                        @endif
                        <th style="text-align:center;vertical-align: middle;{{ $assformcatidx <= 0 ?  'background-color: #d0f0d0;' : 'background-color: #D9D9D9;' }}" rowspan="2">Assessors</th>
                        <th style="text-align:center;vertical-align: middle;{{ $assformcatidx <= 0 ?  'background-color: #d0f0d0;' : 'background-color: #D9D9D9;' }}" colspan="13">{{ $assformcat->name }}</th>
                        <th style="text-align:center;vertical-align: middle;{{ $assformcatidx <= 0 ?  'background-color: #d0f0d0;' : 'background-color: #D9D9D9;' }}" rowspan="2">Sum</th>
                    </tr>
                    <tr style="{{ $assformcatidx <= 0 ?  'background-color: #d0f0d0;' : 'background-color: #D9D9D9;' }}">
                    @php
                        $criteriaCount = count($assformcat->criterias);
                    @endphp

                    {{-- Print available criteria --}}
                    @foreach($assformcat->criterias as $idx => $criteria)
                        <th style="text-align:center;vertical-align: middle;{{ $assformcatidx <= 0 ?  'background-color: #d0f0d0;' : 'background-color: #D9D9D9;' }}">{{ $idx + 1 }} (Q)</th>
                    @endforeach

                    {{-- Fill remaining with empty cells to reach 13 --}}
                    @for($i = $criteriaCount; $i < 13; $i++)
                        <th style="text-align:center;vertical-align: middle;{{ $assformcatidx <= 0 ?  'background-color: #d0f0d0;' : 'background-color: #D9D9D9;' }}">

                        </th>
                    @endfor
                    </tr>

                    @foreach($assessors = $assesseeuser->getAssessorsByAssFormCat($appraisal_cycle_id,$assformcat->id) as $idx=>$assessoruser)
                        <tr>
                            @if($assformcatidx <= 0)
                            <td style="text-align:center;vertical-align: middle;"> {{ $idx == 0 ? $assesseeuser->employee->employee_name : '' }} </td>
                            @else
                            <td ></td>
                            @endif
                            <td style="text-align:center;vertical-align: middle;">{{ $assessoruser->employee->employee_name }}</td>

                            {{-- Get Result --}}
                            @php

                                $totalgivenmark = 0;
                            @endphp
                            @foreach($assformcat->criterias as $idx => $criteria)
                                <td style="width:60px;text-align:center;vertical-align: middle;">{{ $getassessorgivenmark = $assesseedetail->getAssessorGivenMark($assessoruser->id,$assesseeuser->id,$criteria->id,  $appraisal_cycle_id ) }}</td>
                                @php
                                    $totalgivenmark += (int) $getassessorgivenmark;
                                @endphp
                            @endforeach
                            {{-- Fill remaining with empty cells to reach 13 --}}
                            @for($i = $criteriaCount; $i < 13; $i++)
                                <td style="text-align:center;vertical-align: middle;"></td>
                            @endfor
                            <td style="text-align: right;vertical-align: middle;">
                                {{ $totalgivenmark }}
                            </td>
                        </tr>
                    @endforeach


                @endforeach
                    @php
                            $assessors = $assesseeuser->getAssessors($appraisal_cycle_id);
                            $assessoruserscount = $assesseeuser->getAssessorUsersCount($assessors);
                            $criteria_totals = $assesseeuser->getCriteriaTotalArrs($appraisal_cycle_id);
                            $ratetotal = $assesseeuser->getRateTotal($criteria_totals);
                            $average = $assesseeuser->getAverage($ratetotal,$assessoruserscount);
                            $grade = $assesseeuser->getGrade($average) ? $assesseeuser->getGrade($average)->name : '';
                    @endphp
                    <tr>
                        <td colspan="15" style="text-align: right;vertical-align: middle;">Rate Total</td>
                        <td colspan="1" style="text-align: right;vertical-align: middle;">{{ $ratetotal }}</td>
                    </tr>
                     <tr>
                        <td colspan="15" style="text-align: right;vertical-align: middle;">Assessors</td>
                        <td colspan="1" style="text-align: right;vertical-align: middle;">{{ $assessoruserscount }}</td>
                    </tr>
                     <tr>
                        <td colspan="15" style="text-align: right;vertical-align: middle;">Average</td>
                        <td colspan="1" style="text-align: right;vertical-align: middle;">{{ $average}}</td>
                    </tr>
                    <tr>
                        <td colspan="15" style="text-align: right;vertical-align: middle;">Grade</td>
                        <td colspan="1" style="text-align: right;vertical-align: middle;">{{ $grade}}</td>
                    </tr>
            @endforeach
        </table>

    </body>
</html>
