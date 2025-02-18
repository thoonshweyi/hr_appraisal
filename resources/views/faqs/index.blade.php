@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">FAQs</h4>
                    </div>
                </div>
            </div>
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

        </div>
        <div class="col-lg-12">
            <div class="table-responsive rounded mb-3">
                <table class="table mb-0 tbl-server-info" id="faq_list">
                    <thead class="bg-white text-uppercase">
                        <tr class="ligth ligth-data">
                            <th>Eng Name</th>
                            <th>Myanmar Name</th>
                            <th></th>
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

    var table = $('#faq_list').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": false,
        "lengthChange": false,
        "autoWidth": true,
        "responsive": true,
        "order": [
            [1, 'des']
        ],
        'ajax': {
            'url': "/faqs",
            'type': 'GET',
            'data': function(d) {
                d.name_eng = $('#name_eng').val();
                d.name_mm = $('#name_mm').val();
            }
        },
        columns: [{
                data: 'name_eng',
                name: 'name_eng',
                orderable: true
            }, {
                data: 'name_mm',
                name: 'name_mm',
                orderable: true
            },

            {
                data: 'action',
                name: 'action',
                orderable: false,
                render: function(data, type, row) {
                    return `<div class="d-flex align-items-center list-action">
                        <a class="badge bg-success mr-2" data-toggle="tooltip" data-placement="top" title="Detail" data-original-title="Detail"
                            href="/faqs/${row.id}"><i class="ri-eye-line mr-0"></i></a>
                        <a class="badge bg-primary mr-2" data-toggle="tooltip" data-placement="top" title="Detail" data-original-title="Detail"
                            href="/faqs/${row.id}/edit"><i class="ri-edit-line mr-0"></i></a>
                            <a class="badge bg-warning mr-2" data-toggle="tooltip" data-placement="top" title="Delete" data-original-title="Delete"
                                        id="delete" href="#"" data-faq_id="${row.id}"><i class="ri-delete-bin-line mr-0"></i></a>
                    </div>`
                }
            }
        ],
        "columnDefs": [{
            "searchable": false,
            "orderable": false,
            "targets": 0,
        }],
    })

    $('#user_search').on('click', function(e) {
        $('#faq_list').DataTable().draw(true);
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
            text: "{{ __('message.document_delete') }}",
            showCancelButton: true,
            cancelButtonText: "{{ __('message.cancel') }}",
            confirmButtonText: "{{ __('message.ok') }}"

        }).then((result) => {
            if (result.isConfirmed) {
                var faq_id = $(this).data('faq_id');
                var token = $("meta[name='csrf-token']").attr("content");
                $.ajax({
                    url: '/faqs/' + faq_id,
                    type: 'DELETE',
                    data: {
                        "_token": token,
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        jQuery("#load").fadeOut();
                        jQuery("#loading").show();
                    },
                    complete: function() {
                        jQuery("#loading").hide();
                    },
                    success: function(response) {
                        $('#faq_list').DataTable().draw(true);
                    }
                });
            }
        });
    });
});
</script>
@stop
