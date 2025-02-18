@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">{{__('branch.branches')}}</h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 d-flex mb-4">
                <div class="form-row col-md-2">
                    <label> {{__('branch.branch_name')}} </label>
                    <input type="input" class="form-control" id="branch_name" value="">
                </div>
                <div class="form-row col-md-2">
                    <label> {{__('branch.branch_short_name')}}</label>
                    <input type="input" class="form-control" id="branch_short_name" value="">
                </div>
                <button id="branch_search" class="btn btn-primary document_search ml-2 mr-2 mt-4">{{__('button.search')}}</button>
                <!-- <button id="branch_add" class="btn btn-secondary document_search mr-2" onclick=location.href="{{ route('branches.create') }}">Add New</button> -->
                <!-- <button id="branch_syn" class="btn btn-success document_search mr-2">Syn Branch</button> -->
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="table-responsive rounded mb-3">
            <table class="table mb-0 tbl-server-info" id="branch_list">
                <thead class="bg-white text-uppercase">
                    <tr class="ligth ligth-data">
                        <th>{{__('branch.branch_name')}}</th>
                        <th>{{__('branch.branch_short_name')}}</th>
                        <th>{{__('branch.branch_address')}}</th>
                    </tr>
                </thead>
                <tbody class="ligth-body">
                </tbody>
            </table>


        </div>
    </div>
</div>

</div>
@endsection
@section('js')
<script>
    $(document).ready(function() {

        $('#branch_list').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": false,
            "lengthChange": false,
            "autoWidth": true,
            "responsive": true,
            // "order": [[ 5, 'des' ]],
            'ajax': {
                'url': "/branches",
                'type': 'GET',
                'data': function(d) {
                    d.branch_name = $('#branch_name').val();
                    d.branch_short_name = $('#branch_short_name').val();
                }
            },
            columns: [{
                    data: 'branch_name',
                    name: 'branch_name',
                    orderable: true,
                    render: function(data, type, row) {
                        return `<div style="text-align:left">${data}</div>`;
                    }
                },
                {
                    data: 'branch_short_name',
                    name: 'branch_short_name',
                    orderable: true,
                    render: function(data, type, row) {
                        return `<div style="text-align:left">${data}</div>`;
                    }
                },
                {
                    data: 'branch_address1',
                    name: 'branch_address1',
                    orderable: true,
                    render: function(data, type, row) {
                        return `<div style="text-align:left">${data}</div>`;
                    }
                },
            ],
            "columnDefs": [{
                "searchable": false,
                "orderable": false,
                "targets": 0,
            }],
        })

        $('#branch_search').on('click', function(e) {
            $('#branch_list').DataTable().draw(true);
        })
    });
</script>
@stop