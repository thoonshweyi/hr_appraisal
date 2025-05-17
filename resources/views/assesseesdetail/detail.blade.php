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
                table-layout: fixed
            }
            td,
            th {
                border: 1px solid #000;
            }
        </style>
    </head>
    <body>
        <table class="table table-striped table-hover table-bordered bg-white " style="width:100%; vertical-align: middle;">
            <tr  height="60">
                <td colspan="15" style="font-family:'Times New Roman'; font-size:14; font-weight: bold; word-wrap: break-word; text-align:center; margin:auto;">
                    <div>
                        <h4>Pro 1 Global CO., LTD</h4>
                        <h5>Appraisal Name: 2025 Test</h5>
                    </div>
                </td>
            </tr>

            <tr>
                <td>{{ $assesseeuser->employee->employee_name }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>

            {{-- {{ dd($assesseeusers) }} --}}
            @foreach($assesseeusers as $assesseeuser)
            <tr>
                <td>{{ $assesseeuser->employee->employee_name }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endforeach
        </table>

    </body>
</html>


























{{-- <style>
    td,
    th {
        border: 1px solid #000;
    }
    .title{
        word-wrap: break-word; text-align:center; margin:auto;
    }
</style>

<table class="table table-striped table-hover table-bordered bg-white ">

    <tr height="15">
        <td colspan="14" style="font-family:'Times New Roman'; font-size:14; font-weight: bold; color:red; word-wrap: break-word; text-align:center; margin:auto;">Assessee Detail</td>
    </tr>
    <tr>
        <th style="font-weight: bold; width:30px">No.</th>
        <th style="font-weight: bold; width:150px">Document No</th>
        <th style="font-weight: bold; width:100px">Category</th>
        <th style="font-weight: bold; width:120px">Status</th>
        <th style="font-weight: bold; width:120px">Operation</th>
        <th style="font-weight: bold; width:120px">Br.Manager</th>
        <th style="font-weight: bold; width:120px">Cat.Head</th>
        <th style="font-weight: bold; width:120px">Sourcing</th>
        <th style="font-weight: bold; width:120px">Sourcing Manager</th>
        <th style="font-weight: bold; width:120px">Account Issued</th>
        <th style="font-weight: bold; width:120px">Sourcing CN</th>
        <th style="font-weight: bold; width:120px">Finished</th>
    </tr>


</table> --}}
