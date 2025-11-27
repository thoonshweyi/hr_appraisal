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
                        <h5>Appraisal Name: {{ $appraisalcycle->name }}</h5>
                    </div>
                </td>
            </tr>



            {{-- {{ dd($assesseeusers) }} --}}
            @foreach($report as $assesseeIdx=>$assesseeArr)

                @foreach($assesseeArr as $catId => $assessorsInCat)
                    @php
                        $catName   = $categories[$catId]->name ?? 'Unknown';
                        $criterias = $criteriaList[$catId] ?? [];
                        $criteriaCount = count($criterias);
                    @endphp

                    <tr style="{{ $loop->first ?  'background-color: #d0f0d0;' : 'background-color: #D9D9D9;' }}">
                        @if($loop->first)
                        <th style="text-align:center;vertical-align: middle;{{ $loop->first ?  'background-color: #d0f0d0;' : 'background-color: #D9D9D9;' }}" rowspan="2" >Assessee</th>
                        @else
                        <th rowspan="2" style="text-align:center;vertical-align: middle;{{ $loop->first ?  'background-color: #d0f0d0;' : 'background-color: #D9D9D9;' }}"></th>
                        @endif
                        <th style="text-align:center;vertical-align: middle;{{ $loop->first ?  'background-color: #d0f0d0;' : 'background-color: #D9D9D9;' }}" rowspan="2">Assessors</th>
                        <th style="text-align:center;vertical-align: middle;{{ $loop->first ?  'background-color: #d0f0d0;' : 'background-color: #D9D9D9;' }}" colspan="13">{{ $catName }}</th>
                        <th style="text-align:center;vertical-align: middle;{{ $loop->first ?  'background-color: #d0f0d0;' : 'background-color: #D9D9D9;' }}" rowspan="2">Sum</th>
                    </tr>
                    <tr style="{{ $loop->first ?  'background-color: #d0f0d0;' : 'background-color: #D9D9D9;' }}">
           
                    {{-- Print available criteria --}}
                    @foreach($criterias as $i => $c)
                        <th style="text-align:center;vertical-align: middle;{{ $loop->first ?  'background-color: #d0f0d0;' : 'background-color: #D9D9D9;' }}">{{ $loop->iteration }} (Q)</th>
                    @endforeach

                    {{-- Fill remaining with empty cells to reach 13 --}}
                    @for($i = $criteriaCount; $i < 13; $i++)
                        <th style="text-align:center;vertical-align: middle;{{ $loop->first ?  'background-color: #d0f0d0;' : 'background-color: #D9D9D9;' }}">

                        </th>
                    @endfor
                    </tr>

                    @foreach($assessorsInCat as $assessorIdx=>$crisInAssessor)
                    <tr>
                        @if($loop->first)
                        {{-- <td style="text-align:center;vertical-align: middle;"> {{ $loop->first ? $assessees[$assesseeIdx]->employee->employee_name : '' }} </td> --}}
                        <td style="text-align:center;vertical-align: middle;"> {{ $loop->first ? $assessees[$assesseeIdx]->name : '' }} </td> 
                        @else
                        <td ></td>
                        @endif
                        {{-- <td style="text-align:center;vertical-align: middle;"> {{ $assessors[$assesseeIdx][$catId][$assessorIdx]->employee->employee_name ?? '---' }}</td> --}}
                        <td style="text-align:center;vertical-align: middle;"> {{ $assessors[$assesseeIdx][$catId][$assessorIdx]->name ?? '---' }}</td> 
                        {{-- Get Result --}}
                        @php

                            $totalgivenmark = 0;
                        @endphp
                        @foreach($crisInAssessor as $idx => $result)
                            <td style="width:60px;text-align:center;vertical-align: middle;">{{ $result }}</td>
                            @php
                                    $totalgivenmark += (int) $result;
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
                 
                    <tr>
                        <td colspan="15" style="text-align: right;vertical-align: middle;">Rate Total</td>
                        <td colspan="1" style="text-align: right;vertical-align: middle;">{{ $assessees[$assesseeIdx]->total_score }}</td>
                    </tr>
                    <tr>
                        <td colspan="15" style="text-align: right;vertical-align: middle;">Assessors</td>
                        <td colspan="1" style="text-align: right;vertical-align: middle;">{{ $assessees[$assesseeIdx]->assessor_count }}</td>
                    </tr>
                    <tr>
                        <td colspan="15" style="text-align: right;vertical-align: middle;">Average</td>
                        <td colspan="1" style="text-align: right;vertical-align: middle;">{{ $assessees[$assesseeIdx]->average_score }}</td>
                    </tr>
            @endforeach
        </table>

    </body>
</html>
