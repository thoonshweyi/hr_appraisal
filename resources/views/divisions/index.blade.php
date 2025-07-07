@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Divisions</h4>
                        @can('create-fixed-analysis')
                        <a href="#createmodal" class="btn btn-primary" data-toggle="modal">Create</a>
                        @endcan
                    </div>
                </div>
            </div>


            <div class="col-lg-12">
                <form class="d-inline" action="{{ route('divisions.excel_import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row align-items-end">

                        <div class="col-md-4">
                            @error("file")
                            <b class="text-danger">{{ $message }}</b>
                            @enderror
                            <label for="file" class="gallery @error('file') is-invalid @enderror mb-0"><span>Choose Excel File</span></label>
                            <input type="file" name="file" id="file" class="form-control form-control-sm rounded-0" value="" hidden/>
                        </div>


                        <button type="submit" class="btn btn-light" class=""><i class="ri-file-download-line"></i> Import</a>
                    </div>

                </form>
            </div>



            <div class="col-md-12 mb-2">
                @if (count($errors) > 0)
                <div class="alert alert-danger alert-dismissible fade show">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

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


                @if($getvalidationerrors = Session::get('validation_errors'))
                    {{-- <li>{{ Session::get('validation_errors') }}</li> --}}
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong>Whoops!</strong> There were some problems with your excel file at row {{ json_decode($getvalidationerrors)->row }}.<br><br>
                        <ul>
                            {{-- {{ dd(json_decode($getvalidationerrors)) }} --}}
                            @foreach ($validationerrors = json_decode($getvalidationerrors) as $idx=>$import_errors)
                                {{-- {{dd($errors)}} --}}
                                @if($idx != 'row')
                                    @foreach($import_errors as $import_error)
                                        <li>{{ $import_error }}</li>
                                    @endforeach
                                @endif
                            @endforeach
                        </ul>
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
                        <th>Status</th>
                        <th>By</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="ligth-body">
                    @foreach($divisions as $idx=>$division)
                    <tr>
                        <td>{{$idx + $divisions->firstItem()}}</td>
                        <td>{{$division["name"]}}</td>
                        <td>
                            <div class="custom-switch p-0">
                                <!-- The actual checkbox that controls the switch -->
                                <input type="checkbox" id="customSwitch-{{ $idx + $divisions->firstItem() }}" class="custom-switch-input statuschange-btn" {{ $division->status_id === 1 ? "checked" : "" }} data-id="{{ $division->id }}"/>
                                <!-- The label is used to style the switch, and clicking it toggles the checkbox -->
                                <label class="custom-switch-label" for="customSwitch-{{ $idx + $divisions->firstItem() }}"></label>
                                <!-- Optional label text next to the switch -->
                            </div>
                        </td>
                        <td>{{ $division["user"]["name"] }}</td>
                        <td>{{ $division->created_at->format('d M Y') }}</td>
                        <td>{{ $division->updated_at->format('d M Y') }}</td>
                        <td class="text-center">
                            @can('edit-fixed-analysis')
                            <a href="javascript:void(0);" class="text-info editform mr-2" data-toggle="modal" data-target="#editmodal" data-id="{{$division->id}}" data-name="{{$division->name}}" data-status="{{$division->status_id}}"><i class="fas fa-pen"></i></a>
                            @endcan

                            @can('delete-fixed-analysis')
                            <a href="#" class="text-danger ms-2 delete-btns" data-idx="{{$idx}}"><i class="fas fa-trash-alt"></i></a>
                            @endcan
                        </td>
                        <form id="formdelete-{{ $idx }}" class="" action="{{route('divisions.destroy',$division->id)}}" method="POST">
                             @csrf
                             @method("DELETE")
                        </form>
                   </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-center">
                {{ $divisions->appends(request()->all())->links("pagination::bootstrap-4") }}
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
                        <form id="{{route('divisions.store')}}" action="" method="POST">
                            {{ csrf_field() }}
                            <div class="row align-items-end">
                                <div class="col-md-12">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    @error("name")
                                            <span class="text-danger">{{ $message }}<span>
                                    @enderror
                                    <input type="text" name="name" id="name" class="form-control form-control-sm rounded-0" placeholder="Enter Dept Group Name" value="{{ old('name') }}"/>
                                </div>

                                <div class="col-md-12">
                                    <label for="status_id">Status</label>
                                    <select name="status_id" id="status_id" class="form-control form-control-sm rounded-0">
                                        @foreach($statuses as $status)
                                            <option value="{{$status['id']}}">{{$status['name']}}</option>
                                        @endforeach
                                    </select>
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
                                <div class="col-md-12 mb-2">
                                    <label for="edit_name">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="edit_name" id="edit_name" class="form-control form-control-sm rounded-0" placeholder="Enter Status Name" value="{{ old('name') }}"/>
                                </div>

                                <div class="col-md-12">
                                    <label for="edit_status_id">Status</label>
                                    <select name="edit_status_id" id="edit_status_id" class="form-control form-control-sm rounded-0">
                                        @foreach($statuses as $status)
                                            <option value="{{$status['id']}}">{{$status['name']}}</option>
                                        @endforeach
                                    </select>
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
            const getid = $(this).attr("data-id");
            $("#formaction").attr("action",`/divisions/${getid}`);

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
                  url:"/divisionsstatus",
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

      {{-- Start Preview Image --}}

      var previewimages = function(input, output) {
        if (input.files) {
            var totalfiles = input.files.length;

            if (totalfiles > 0) {
                $('.gallery').addClass('removetxt');
            } else {
                $('.gallery').removeClass('removetxt');
            }

            $(output).html(""); // Clear previous previews

            let html = ''
            for (let i = 0; i < totalfiles; i++) {
                var file = input.files[i];
                var filereader = new FileReader();

                filereader.onload = function(e) {
                    let fileType = file.type;
                    console.log("File Type:", fileType);

                    {{-- if (fileType === 'application/pdf') {
                        // Show PDF icon
                        $($.parseHTML('<img>')).attr({
                            'src': '{{ asset('images/pdf.png') }}',
                            'title': file.name
                        }).appendTo(output);
                    } else if (
                        fileType === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ||
                        fileType === 'application/vnd.ms-excel'
                    ) {
                        // Show Excel icon
                        $($.parseHTML('<img>')).attr({
                            'src': '{{ asset('images/excel.png') }}',
                            'title': file.name
                        }).appendTo(output);
                    } else {
                        // Show normal image preview
                        $($.parseHTML('<img>')).attr({
                            'src': e.target.result,
                            'title': file.name
                        }).appendTo(output);
                    } --}}

                    if (
                        fileType === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ||
                        fileType === 'application/vnd.ms-excel'
                    ) {
                        // Show Excel icon
                        {{-- $($.parseHTML('<img>')).attr({
                            'src': '{{ asset('images/excel.png') }}',
                            'title': file.name
                        }).appendTo(output); --}}


                        html = `
                            <img src="{{ asset('images/excel.png') }}" title=${file.name} />
                        `;
                        $(output).append(html);
                    }else{
                        Swal.fire({
                            title: "Invalid File!!",
                            text: "Only Excel files (.xls, .xlsx) are allowed.",
                            icon: "question"
                          });


                    html = `
                          <img src="{{ asset('images/file-invalid.png') }}" title=${file.name} />
                    `;
                      $(output).append(html);
                    }

                };

                filereader.readAsDataURL(file);
            }
        }
    };

    $('#file').change(function() {
        previewimages(this, '.gallery');
    });

        {{-- End Preview Image --}}


    });
</script>
@stop
