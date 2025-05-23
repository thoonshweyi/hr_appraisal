@extends('adminlte::page')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Permissions</h2>
        </div>
        <div class="pull-right mb-3">
            @can('permission-create')
            <a class="btn btn-success" href="{{ route('permissions.create') }}"> Create New permission</a>
            @endcan
        </div>
    </div>
</div>


@if ($message = Session::get('success'))
<div class="alert alert-success alert-dismissible fade show">
    <p>{{ $message }}</p>
  <button type="button" class="close text-danger" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
</div>
@endif


<table class="table table-bordered">
    <tr>
        <th>No</th>
        <th>Name</th>
        <th>Details</th>
        <th width="280px">Action</th>
    </tr>
    @foreach ($permissions as $permission)
    <tr>
        <td>{{ ++$i }}</td>
        <td>{{ $permission->name }}</td>
        <td>{{ $permission->short_desc }}</td>
        <td>
            <form action="{{ route('permissions.destroy',$permission->id) }}" method="POST">
                <a class="btn btn-info" href="{{ route('permissions.show',$permission->id) }}">Show</a>
                @can('permission-edit')
                <a class="btn btn-primary" href="{{ route('permissions.edit',$permission->id) }}">Edit</a>
                @endcan


                @csrf
                @method('DELETE')
                @can('permission-delete')
                <button type="submit" class="btn btn-danger">Delete</button>
                @endcan
            </form>
        </td>
    </tr>
    @endforeach
</table>


{!! $permissions->links() !!}


@endsection
