@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">{{__('lucky_draw_type.list')}}</h4>
                    </div>
                </div>
            </div>
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
            <div class="col-lg-12 d-flex mb-2">
                <div class="form-row col-md-2">
                    <label>{{__('lucky_draw_type.name')}} </label>
                    <input type="text" class="form-control" id="lucky_draw_type_name" value="">
                </div>
                <div class="form-row col-md-2">
                    <label>{{__('lucky_draw_type.status')}} </label>
                    <select id="lucky_draw_type_status" class="form-control ">
                        <option value="0">All Status</option>
                        <option value="1">Active</option>
                        <option value="2">Inactive</option>
                    </select>
                </div>
                <button id="search" class="btn btn-primary document_search mr-2">{{__('button.search')}}</button>
                @can('export-document-admin')
                <button id="document_export" class="btn btn-success">{{__('button.product_excel_export')}}</button>
                @endcan
            </div>
            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table class="table mb-0 tbl-server-info" id="lucky_draw_types_list">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>{{__('lucky_draw_type.name')}}</th>
                                <th>{{__('lucky_draw_type.description')}}</th>
                                <th>{{__('lucky_draw_type.status')}}</th>
                                <th>{{__('lucky_draw_type.action')}}</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Page end  -->
    </div>
    <!-- Modal Edit -->
</div>
@endsection
@section('js')
<script>
    $(document).ready(function() {
        var table = $('#lucky_draw_types_list').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": false,
            "lengthChange": false,
            "autoWidth": true,
            "responsive": true,
            "pageLength": 10,
            "scrollY": "450px",
            "scrollCollapse": true,
            'ajax': {
                'url': "/lucky_draw_types/search_result",
                'type': 'GET',
                'data': function(d) {
                    d.lucky_draw_type_name = $('#lucky_draw_type_name').val();
                    d.lucky_draw_type_status = $('#lucky_draw_type_status').val();
                }
            },
            columns: [{
                    data: 'name',
                    name: 'name',
                    orderable: true,
                    render: function(data, type, row) {
                        return `<a href="/lucky_draw_types/${row.id}/edit" class="normal_status">${data}</a>`;
                    }
                },
                {
                    data: 'description',
                    name: 'description',
                    orderable: true,
                    render: function(data, type, row) {
                        return `<a href="/lucky_draw_types/${row.id}/edit" class="normal_status">${data}</a>`;
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: true,
                    render: function(data, type, row) {
                        if(data == 1){
                            return `<a href="/lucky_draw_types/${row.id}/edit" class="normal_status">Active</a>`;
                        }
                        if(data == 2){
                            return `<a href="/lucky_draw_types/${row.id}/edit" class="cancel_status">Inactive</a>`;
                        }
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    render: function(data, type, row) {
                            return `
                                <div class="d-flex align-items-center list-action">
                                    <a class="badge bg-success mr-2" data-toggle="tooltip" data-placement="top" title="Edit" data-original-title="Edit"
                                        href="/lucky_draw_types/${row.id}/edit"><i class="ri-pencil-line mr-0"></i></a>

                                    <a class="badge bg-warning mr-2" data-toggle="tooltip" data-placement="top" title="Delete" data-original-title="Delete"
                                        id="delete" href="#"" data-lucky_draw_type_id="${row.id}"
                                        ><i class="ri-delete-bin-line mr-0"></i></a>
                                </div>`
                    }
                }
            ],
            "columnDefs": [{
                "searchable": false,
                    }],
        })

        $('#search').on('click', function(e) {
            $('#lucky_draw_types_list').DataTable().draw(true);
        })
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });
        table.on('click', '#delete', function(e) {

            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: "{{ __('message.warning') }}",
                text: "{{ __('message.delete') }}",
                showCancelButton: true,
                cancelButtonText: "{{ __('message.cancel') }}",
                confirmButtonText: "{{ __('message.ok') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    var lucky_draw_type_id = $(this).data('lucky_draw_type_id');
                    var token = $("meta[name='csrf-token']").attr("content");
                    $.ajax({
                        url: '/lucky_draw_types/' + lucky_draw_type_id,
                        type: 'Delete',

                        data: {
                            "_token": token,
                            "id": lucky_draw_type_id,
                        },
                        beforeSend: function() {
                            jQuery("#load").fadeOut();
                            jQuery("#loading").show();
                        },
                        complete: function() {
                            jQuery("#loading").hide();
                        },
                        success: function(response) {
                            $('#lucky_draw_types_list').DataTable().draw(true);
                        }
                    });
                }
            });
        });
        $('#document_export').on('click', function(e) {
            var today = new Date().toISOString().slice(0, 10);
            var document_no = $('#document_no').val();
            var document_from_date = $('#document_from_date').val().length === 0 ? today : $('#document_from_date').val();
            var document_to_date = $('#document_to_date').val().length === 0 ? today : $('#document_to_date').val();
            var document_type = $('#document_type').val();
            var document_branch = $('#document_branch').val();
            var document_status = $('#document_status').val();
            var category = $('#category').val();
            var other = document_no + '-' + document_type + '-' + document_branch + '-' + document_status + '-' + category;
            var url = `/documents/document_export/${document_from_date}/${document_to_date}/${other}`;
            window.location = url;
        })
    });
</script>
@stop
