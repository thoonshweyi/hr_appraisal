@extends('layouts.app')

@section('content')
<div class="content-page">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12 margin-tb">
        <div class="pull-left">
          <h4>Departments</h4>
        </div>
        <div class="pull-right mb-3">
          @can('dept-create')
          <a class="btn btn-success" href="{{ route('departments.create') }}"> Add New Department</a>
          @endcan
        </div>
      </div>
    </div>

    @if ($message = Session::get('success'))
    <div class="alert alert-success">
      <p>{{ $message }}</p>
    </div>
    @endif
    <div class="table-responsive rounded mb-3">
      <table class="table mb-0 tbl-server-info">
        <tr class="ligth ligth-data">
          <th>No</th>
          <th>Branch Name</th>
          <th>Dept</th>
          <th width="280px">Action</th>
        </tr>
        @foreach ($departments as $department)
        <tr>
          <td style="text-align:center;">{{ ++$i }}</td>
          <td>{{ isset($department->branch_id) ? $department->branches->branch_short_name: '-'}}</td>
          <td>{{ $department->name }}</td>
          <td style="text-align:center;">
            <form action="{{ route('departments.destroy',$department->id) }}" method="POST">
              <a class="btn btn-info" href="{{ route('departments.show',$department->id) }}">Show</a>
              <a class="btn btn-primary" href="{{ route('departments.edit',$department->id) }}">Edit</a>
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-danger"> Delete</button>
            </form>
          </td>
        </tr>
        @endforeach
      </table>
      {!! $departments->links() !!}
    </div>
  </div>
  @endsection