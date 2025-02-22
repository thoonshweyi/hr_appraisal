@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-2">
                    <div>
                        <h4 class="mb-3">Statuses</h4>

                        <a href="#createmodal" class="btn btn-primary" data-toggle="modal">Create</a>
                    </div>
                </div>
            </div>

            <hr/>

            <div class="col-lg-12">
                @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

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
        </div>
    </div>
    <div class="col-lg-12">
        <div class="table-responsive rounded mb-3">
            <table class="table mb-0 tbl-server-info" id="branch_list">
                <thead class="bg-white text-uppercase">
                    <tr class="ligth ligth-data">
                        {{-- <th>
                            <input type="checkbox" name="selectalls" id="selectalls" class="form-check-input selectalls" />
                        </th> --}}
                       <th>No</th>
                       <th>Name</th>
                       <th>By</th>
                       <th>Created At</th>
                       <th>Updated At</th>
                       <th>Action</th>
                    </tr>
                </thead>
                <tbody class="ligth-body">
                    @foreach($statuses as $idx=>$status)
                        <tr>
                            <td>{{$idx + $statuses->firstItem()}}</td>
                            <td>{{ $status->name }}</td>
                            <td>{{ $status->user->name }}</td>
                            <td>{{ $status->created_at->format('d M Y') }}</td>
                            <td>{{ $status->updated_at->format('d M Y') }}</td>
                            <td class="text-center">
                                <a href="javascript:void(0);" class="text-info editform mr-2" data-toggle="modal" data-target="#editmodal" data-id="{{$status->id}}" data-name="{{$status->name}}"><i class="fas fa-pen"></i></a>
                                <a href="#" class="text-danger ms-2 delete-btns" data-idx="{{$idx}}"><i class="fas fa-trash-alt"></i></a>
                            </td>
                           <form id="formdelete-{{ $idx }}" class="" action="{{route('statuses.destroy',$status->id)}}" method="POST">
                                @csrf
                                @method("DELETE")
                           </form>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-center">
                {{ $statuses->appends(request()->all())->links("pagination::bootstrap-4") }}
            </div>



        </div>
    </div>
</div>

</div>


<!-- START MODAL AREA -->
    <!-- start create modal -->
    <div id="createmodal" class="modal fade">
        <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-0">
                    <div class="modal-header">
                        <h6 class="modal-title">Create Form</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <form id="{{route('statuses.store')}}" action="" method="POST">
                            {{ csrf_field() }}
                            <div class="row align-items-end">
                                <div class="col-md-12 mb-2">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    @error("name")
                                            <span class="text-danger">{{ $message }}<span>
                                    @enderror
                                    <input type="text" name="name" id="name" class="form-control form-control-sm rounded-0" placeholder="Enter Status Name" value="{{ old('name') }}"/>
                                </div>


                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary btn-sm rounded-0">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="modal-footer">

                    </div>
                </div>
        </div>
    </div>
    <!-- end create modal -->


    <!-- start edit modal -->
    <div id="editmodal" class="modal fade">
    <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Edit Form</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="formaction" action="" method="POST">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}
                        <div class="row align-items-end">
                                <div class="col-md-12 mb-2">
                                    <label for="editname">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="editname" class="form-control form-control-sm rounded-0" placeholder="Enter Status Name" value="{{ old('name') }}"/>
                                </div>

                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary btn-sm rounded-0">Update</button>
                                </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">

                </div>
            </div>
    </div>
    </div>
      <!-- end edit modal -->

<!-- End MODAL AREA -->

@endsection
@section('js')
<script>
    $(document).ready(function() {

        // Start Delete Item
        $(".delete-btns").click(function(){
            // console.log('hay');

            var getidx = $(this).data("idx");
            // console.log(getidx);


            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#formdelete-'+getidx).submit();

                }
            });


       });
       // End Delete Item



       // Start Edit Form
       $(document).on("click",".editform",function(e){
            // console.log($(this).attr("data-id"),$(this).attr("data-name"));

            $("#editname").val($(this).attr("data-name"));
            const getid = $(this).attr("data-id");
            $("#formaction").attr("action",`/statuses/${getid}`);

            e.preventDefault();
       });
       // End Edit Form
    });
</script>
@stop
