@extends('layouts.app')

@section('content')
<div class="content-page">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
          <div>
            <h4 class="mb-3">{{__('role.roles')}}</h4>
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
      <div class="col-lg-12 d-flex mb-4">
        <div class="form-row col-md-2">
          <label>{{__('role.name')}}</label>
          <input type="input" class="form-control" id="role_name" value="">
        </div>
        <button id="role_search" class="btn btn-primary document_search ml-2 mr-2 mt-4">{{__('button.search')}}</button>
        <button id="role_add" class="btn btn-secondary document_search mr-2 mt-4" onclick=location.href="{{ route('roles.create') }}">{{__('button.add_new')}}</button>
      </div>
    </div>
  </div>
  <div class="col-lg-12">
    <div class="table-responsive rounded mb-3">
      <table class="table mb-0 tbl-server-info" id="role_list">
        <thead class="bg-white text-uppercase">
          <tr class="ligth ligth-data">
            <th>{{__('role.name')}}</th>
            <th>{{__('role.action')}}</th>
          </tr>
        </thead>
        <tbody class="ligth-body">
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
@section('js')
<script>
  $(document).ready(function() {

    $('#role_list').DataTable({
      "processing": true,
      "serverSide": true,
      "searching": false,
      "lengthChange": false,
      "autoWidth": true,
      "responsive": true,
      // "order": [[ 5, 'des' ]],
      'ajax': {
        'url': "/roles",
        'type': 'GET',
        'data': function(d) {
          d.role_name = $('#role_name').val();
        }
      },
      columns: [{
          data: 'role_name',
          name: 'role_name',
          orderable: true
        },
        {
          data: 'action',
          name: 'action',
          orderable: false,
          render: function(data, type, row) {
            return `<div class="d-flex align-items-center list-action">
                                    <a class="badge badge-success mr-2" title="Detail" data-original-title="View"
                                        href="/roles/${row.id}"><i class="ri-eye-line mr-0"></i></a>
                                    <a class="badge badge-primary mr-2" title="Edit" data-original-title="Edit"
                                        href="/roles/${row.id}/edit"><i class="ri-edit-line mr-0"></i></a>
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


    $('#role_search').on('click', function(e) {
      $('#role_list').DataTable().draw(true);
    })
  });
</script>
@stop
