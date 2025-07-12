<!DOCTYPE html>
<html>
    <head>
        <title>Employees List</title>
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
                        <h5>Employees List</h5>
                    </div>
                </td>
            </tr>

            <tr class="ligth ligth-data" height="60" style="background-color: #d0f0d0;" >
                <th  style="background-color: #d0f0d0; text-align:center;vertical-align: middle;">No</th>
                <th  style="background-color: #d0f0d0; text-align:center;vertical-align: middle;">Name</th>
                <th  style="background-color: #d0f0d0; text-align:center;vertical-align: middle;">Employee Code</th>
                <th  style="background-color: #d0f0d0; text-align:center;vertical-align: middle;">Branch</th>
                <th  style="background-color: #d0f0d0; text-align:center;vertical-align: middle; ">Position</th>
                <th  style="background-color: #d0f0d0; text-align:center;vertical-align: middle;">Level</th>
                <th colspan="5"  style="background-color: #d0f0d0; text-align:left;vertical-align: middle;">Criteria Set</th>
            </tr>



            @foreach($employees as $idx=>$employee)
                <tr>
                    <td>{{ ++$idx}}</td>
                    <td>{{$employee["employee_name"]}}</td>
                    <td>{{$employee["employee_code"]}}</td>
                    <td>{{$employee["branch"]["branch_name"]}}</td>
                    <td>{{ $employee->position->name}}</td>
                    <td>{{ $employee->positionlevel->name}}</td>

                    @php
                        $assformcatCount = count($employee->emppuser?->getAssFormCats());
                    @endphp

                    @foreach($employee->emppuser?->getAssFormCats() as $idx=>$assformcat)
                        <td >{{$assformcat->name}}</td>
                    @endforeach

                    @for($i = $assformcatCount; $i < 5; $i++)
                        <td></td>
                    @endfor


                </tr>
            @endforeach
        </table>

    </body>
</html>
