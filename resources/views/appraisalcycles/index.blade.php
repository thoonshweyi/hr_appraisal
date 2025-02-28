@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Appraisal Cycles</h4>

                        <a href="{{ route('appraisalcycles.create') }}" class="btn btn-primary" >Create</a>



                    </div>
                </div>
            </div>



            {{-- <div class="col-lg-12 my-2 ">
                <form class="d-inline" action="{{ route('appraisalcycles.index') }}" method="GET">
                    @csrf
                    <div class="row align-items-end justify-content-end ">

                        <div class="col-md-2">
                            <label for="filter_appraisalcycle_name">Enployee Name <span class="text-danger">*</span></label>
                            <input type="text" name="filter_appraisalcycle_name" id="filter_appraisalcycle_name" class="form-control form-control-sm rounded-0" placeholder="Enter Employee Name" value="{{ request()->filter_name }}"/>
                        </div>

                        <div class="col-md-2">
                            <label for="filter_appraisalcycle_code">Enployee Code <span class="text-danger">*</span></label>
                            <input type="text" name="filter_appraisalcycle_code" id="filter_appraisalcycle_code" class="form-control form-control-sm rounded-0" placeholder="Enter Employee Code" value="{{ request()->filter_appraisalcycle_code }}"/>
                        </div>

                        <div class="col-md-2">
                            <label for="filter_branch_id">Branch</label>
                            <select name="filter_branch_id" id="filter_branch_id" class="form-control form-control-sm rounded-0">
                                <option value="" selected disabled>Choose Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{$branch['branch_id']}}" {{ $branch['branch_id'] == request()->filter_branch_id ? 'selected' : '' }}>{{$branch['branch_name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="filter_position_level_id">Position Level</label>
                            <select name="filter_position_level_id" id="filter_position_level_id" class="form-control form-control-sm rounded-0">
                                <option value="" selected disabled>Choose Position Level</option>
                                @foreach($positionlevels as $positionlevel)
                                    <option value="{{$positionlevel['id']}}" {{ $positionlevel['id'] == request()->filter_position_level_id ? 'selected' : '' }}>{{$positionlevel['name']}}</option>
                                @endforeach
                            </select>
                        </div>


                        <button type="submit" class="btn btn-success" class=""><i class="ri-search-line"></i> Search</a>
                        @if(count(request()->query()) > 0)
                            <button type="button" id="btn-clear" class="btn btn-light btn-clear ml-2" title="Refresh" onclick="window.location.href = window.location.href.split('?')[0];"><i class="ri-refresh-line"></i> Reset</button>
                        @endif

                    </div>

                </form>
            </div> --}}






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


                @if($getvalidationerrors = Session::get('validation_errors'))
                    {{-- <li>{{ Session::get('validation_errors') }}</li> --}}
                    <div class="alert alert-danger">
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
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Action Start Date</th>
                        <th>Action End Date</th>
                        <th>By</th>
                        <th>Created At</th>
                        {{-- <th>Updated At</th> --}}
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="ligth-body">
                    @foreach($appraisalcycles as $idx=>$appraisalcycle)
                    <tr>
                        <td>{{$idx + $appraisalcycles->firstItem()}}</td>
                        <td>{{$appraisalcycle["name"]}}</td>
                        <td>
                            <div class="custom-switch p-0">
                                <!-- The actual checkbox that controls the switch -->
                                <input type="checkbox" id="customSwitch-{{ $idx + $appraisalcycles->firstItem() }}" class="custom-switch-input statuschange-btn" {{ $appraisalcycle->status_id === 1 ? "checked" : "" }} data-id="{{ $appraisalcycle->id }}"/>
                                <!-- The label is used to style the switch, and clicking it toggles the checkbox -->
                                <label class="custom-switch-label" for="customSwitch-{{ $idx + $appraisalcycles->firstItem() }}"></label>
                                <!-- Optional label text next to the switch -->
                            </div>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($appraisalcycle->start_date)->format('d M Y') }}</td>
                        <td>{{  \Carbon\Carbon::parse($appraisalcycle->end_date)->format('d M Y') }}</td>


                        <td>{{ \Carbon\Carbon::parse($appraisalcycle->action_start_date)->format('d M Y') }}</td>
                        <td>{{  \Carbon\Carbon::parse($appraisalcycle->action_end_date)->format('d M Y') }}</td>
                        <td>{{ $appraisalcycle["user"]["name"] }}</td>

                        <td>{{ $appraisalcycle->created_at->format('d M Y') }}</td>
                        {{-- <td>{{ $appraisalcycle->updated_at->format('d M Y') }}</td> --}}
                        <td class="text-center">
                             <a href="{{ route('appraisalcycles.edit',$appraisalcycle->id) }}" class="text-info mr-2"><i class="fas fa-pen"></i></a>
                             <a href="#" class="text-danger ms-2 delete-btns" data-idx="{{$idx}}"><i class="fas fa-trash-alt"></i></a>
                        </td>
                        <form id="formdelete-{{ $idx }}" class="" action="{{route('appraisalcycles.destroy',$appraisalcycle->id)}}" method="POST">
                             @csrf
                             @method("DELETE")
                        </form>
                   </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-center">
                {{ $appraisalcycles->appends(request()->all())->links("pagination::bootstrap-4") }}
            </div>


        </div>
    </div>
</div>

</div>

<!-- START MODAL AREA -->



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


        //Start change-btn
        $(document).on("change",".statuschange-btn",function(){

             var getid = $(this).data("id");
             // console.log(getid);
             {{-- console.log(getid); --}}

             var setstatus = $(this).prop("checked") === true ? 1 : 2;
             {{-- console.log(setstatus); --}}

             $.ajax({
                  url:"/appraisalcyclesstatus",
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
