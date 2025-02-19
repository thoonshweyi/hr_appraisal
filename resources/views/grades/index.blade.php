@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Grades</h4>

                        <a href="#createmodal" class="btn btn-primary" data-toggle="modal">Create</a>

                    </div>
                </div>
            </div>

            <div class="col-md-12 mb-2">
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
            {{-- <div class="col-lg-12 d-flex mb-4">
                <div class="form-row col-md-2">
                    <label> {{__('branch.branch_name')}} </label>
                    <input type="input" class="form-control" id="branch_name" value="">
                </div>
                <div class="form-row col-md-2">
                    <label> {{__('branch.branch_short_name')}}</label>
                    <input type="input" class="form-control" id="branch_short_name" value="">
                </div>
                <button id="branch_search" class="btn btn-primary document_search ml-2 mr-2 mt-4">{{__('button.search')}}</button>
            </div> --}}
        </div>
    </div>
    <div class="col-lg-12">
        <div class="table-responsive rounded mb-3">
            <table class="table mb-0" id="branch_list">
                <thead class="bg-white text-uppercase">
                    <tr class="ligth ligth-data">
                        <th>No</th>
                        <th>Name</th>
                        <th>From Rate</th>
                        <th>To Rate</th>
                        <th>Status</th>
                        <th>By</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="ligth-body">
                    @foreach($grades as $idx=>$grade)
                    <tr>
                        <td>{{$idx + $grades->firstItem()}}</td>
                        <td>{{$grade["name"]}}</td>
                        <td>{{ $grade["from_rate"] }}</td>
                        <td>{{ $grade["to_rate"] }}</td>
                        <td>
                            <div class="custom-switch p-0">
                                <!-- The actual checkbox that controls the switch -->
                                <input type="checkbox" id="customSwitch-{{ $idx + $grades->firstItem() }}" class="custom-switch-input statuschange-btn" {{ $grade->status_id === 1 ? "checked" : "" }} data-id="{{ $grade->id }}"/>
                                <!-- The label is used to style the switch, and clicking it toggles the checkbox -->
                                <label class="custom-switch-label" for="customSwitch-{{ $idx + $grades->firstItem() }}"></label>
                                <!-- Optional label text next to the switch -->
                            </div>
                        </td>
                        <td>{{ $grade["user"]["name"] }}</td>
                        <td>{{ $grade->created_at->format('d M Y') }}</td>
                        <td>{{ $grade->updated_at->format('d M Y') }}</td>
                        <td class="text-center">
                             <a href="javascript:void(0);" class="text-info editform mr-2" data-toggle="modal" data-target="#editmodal" data-id="{{$grade->id}}" data-name="{{$grade->name}}" data-status="{{$grade->status_id}}" data-from_rate="{{$grade->from_rate}}" data-to_rate="{{$grade->to_rate}}"><i class="fas fa-pen"></i></a>
                             <a href="#" class="text-danger ms-2 delete-btns" data-idx="{{$idx}}"><i class="fas fa-trash-alt"></i></a>
                        </td>
                        <form id="formdelete-{{ $idx }}" class="" action="{{route('grades.destroy',$grade->id)}}" method="POST">
                             @csrf
                             @method("DELETE")
                        </form>
                   </tr>
                    @endforeach
                </tbody>
            </table>


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
                        <form id="{{route('grades.store')}}" action="" method="POST">
                            {{ csrf_field() }}
                            <div class="row align-items-end">
                                <div class="col-md-6">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    {{-- @error("name")
                                            <span class="text-danger">{{ $message }}<span>
                                    @enderror --}}
                                    <input type="text" name="name" id="name" class="form-control form-control-sm rounded-0" placeholder="Enter Grade Name" value="{{ old('name') }}"/>
                                </div>

                                <div class="col-md-6">
                                    <label for="status_id">Status</label>
                                    <select name="status_id" id="status_id" class="form-control form-control-sm rounded-0">
                                        @foreach($statuses as $status)
                                            <option value="{{$status['id']}}">{{$status['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="from_rate">From Rate <span class="text-danger">*</span></label>
                                    {{-- @error("from_rate")
                                            <span class="text-danger">{{ $message }}<span>
                                    @enderror --}}
                                    <input type="text" name="from_rate" id="from_rate" class="form-control form-control-sm rounded-0" placeholder="Enter From Rate" value="{{ old('from_rate') }}"/>
                                </div>

                                <div class="col-md-6">
                                    <label for="to_rate">To Rate <span class="text-danger">*</span></label>
                                    {{-- @error("to_rate")
                                            <span class="text-danger">{{ $message }}<span>
                                    @enderror --}}
                                    <input type="text" name="to_rate" id="to_rate" class="form-control form-control-sm rounded-0" placeholder="Enter To Rate" value="{{ old('to_rate') }}"/>
                                </div>



                                <div class="col-md-12 mt-2">
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
                                <div class="col-md-6 mb-2">
                                    <label for="edit_name">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="edit_name" id="edit_name" class="form-control form-control-sm rounded-0" placeholder="Enter Status Name" value="{{ old('name') }}"/>
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_status_id">Status</label>
                                    <select name="edit_status_id" id="edit_status_id" class="form-control form-control-sm rounded-0">
                                        @foreach($statuses as $status)
                                            <option value="{{$status['id']}}">{{$status['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="col-md-6">
                                    <label for="edit_from_rate">From Rate <span class="text-danger">*</span></label>
                                    {{-- @error("from_rate")
                                            <span class="text-danger">{{ $message }}<span>
                                    @enderror --}}
                                    <input type="text" name="edit_from_rate" id="edit_from_rate" class="form-control form-control-sm rounded-0" placeholder="Enter From Rate" value="{{ old('from_rate') }}"/>
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_to_rate">To Rate <span class="text-danger">*</span></label>
                                    {{-- @error("to_rate")
                                            <span class="text-danger">{{ $message }}<span>
                                    @enderror --}}
                                    <input type="text" name="edit_to_rate" id="edit_to_rate" class="form-control form-control-sm rounded-0" placeholder="Enter To Rate" value="{{ old('to_rate') }}"/>
                                </div>

                                <div class="col-md-12 mt-2">
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
            {{-- // console.log(getidx); --}}


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
            {{-- console.log($(this).attr("data-id"),$(this).attr("data-name"));
            console.log($(this).attr("data-status")) --}}

            $("#edit_name").val($(this).attr("data-name"));
            $("#edit_status_id").val($(this).attr("data-status"));
            $("#edit_from_rate").val($(this).attr("data-from_rate"));
            $("#edit_to_rate").val($(this).attr("data-to_rate"));

            const getid = $(this).attr("data-id");
            $("#formaction").attr("action",`/grades/${getid}`);

            e.preventDefault();
       });
       // End Edit Form


        //Start change-btn
        $(document).on("change",".statuschange-btn",function(){

             var getid = $(this).data("id");
             // console.log(getid);
             {{-- console.log(getid); --}}

             var setstatus = $(this).prop("checked") === true ? 1 : 2;
             {{-- console.log(setstatus); --}}

             $.ajax({
                  url:"/gradesstatus",
                  type:"POST",
                  dataType:"json",
                  data:{
                        "id":getid,
                        "status_id":setstatus,
                        "_token": '{{ csrf_token()}}'
                    },
                  success:function(response){
                       console.log(response); // {success: 'Status Change Successfully'}
                       console.log(response.success); // Status Change Successfully

                       Swal.fire({
                            title: "Updated!",
                            text: "Status Updated Successfully",
                            icon: "success"
                       });
                  },
                  error:function(response){
                    console.log(response);
                  }
             });
        });
        // End change btn
    });
</script>
@stop
