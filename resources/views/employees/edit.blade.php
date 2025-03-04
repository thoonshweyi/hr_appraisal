@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Employee Edit</h4>
                    </div>
                </div>
            </div>



            <div class="col-lg-12 my-2 ">
                <form id="" action="{{route('employees.update',$employee->id)}}" method="POST">
                    {{ csrf_field() }}
                    @method('PUT')
                    <div class="row align-items-start">
                        <div class="col-md-3">
                            <label for="employee_name">Name <span class="text-danger">*</span></label>
                            @error("employee_name")
                                    <span class="text-danger">{{ $message }}<span>
                            @enderror
                            <input type="text" name="employee_name" id="employee_name" class="form-control form-control-sm rounded-0" placeholder="Enter Employee Name" value="{{ old('employee_name',$employee->employee_name) }}"/>
                        </div>

                        <div class="col-md-3">
                            <label for="nickname">Nickname</label>
                            @error("nickname")
                                    <span class="text-danger">{{ $message }}<span>
                            @enderror
                            <input type="text" name="nickname" id="nickname" class="form-control form-control-sm rounded-0" placeholder="Enter Employee Nickname" value="{{ old('nickname',$employee->nickname) }}"/>
                        </div>



                        <div class="col-md-3">
                            <label for="division_id">Division</label>
                            <select name="division_id" id="division_id" class="form-control form-control-sm rounded-0">
                                <option value="" selected disabled>Choose Division</option>
                                @foreach($divisions as $division)
                                    <option value="{{$division['id']}}" {{ $division['id'] == old('division_id',$employee->division_id) ? "selected" : "" }}>{{$division['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="department_id">Departments</label>
                            <select name="department_id" id="department_id" class="form-control form-control-sm rounded-0">
                                <option value="" selected disabled>Choose Department</option>
                                @foreach($departments as $department)
                                    <option value="{{$department['id']}}" {{ $department['id'] == old('department_id',$employee->department_id) ? "selected" : "" }}>{{$department['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="sub_department_id">Sub Departments</label>
                            <select name="sub_department_id" id="sub_department_id" class="form-control form-control-sm rounded-0">
                                <option value="" selected disabled>Choose Sub Department</option>
                                @foreach($subdepartments as $subdepartment)
                                    <option value="{{$subdepartment['id']}}"  {{ $subdepartment['id'] == old('sub_department_id',$employee->sub_department_id) ? "selected" : "" }}>{{$subdepartment['name']}}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-md-3">
                            <label for="section_id">Sections</label>
                            <select name="section_id" id="section_id" class="form-control form-control-sm rounded-0">
                                <option value="" selected disabled>Choose Section</option>
                                @foreach($sections as $section)
                                    <option value="{{$section['id']}}" {{ $section['id'] == old('section_id',$employee->section_id) ? "selected" : "" }}>{{$section['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="position_id">Positions</label>
                            <select name="position_id" id="position_id" class="form-control form-control-sm rounded-0">
                                <option value="" selected disabled>Choose Position</option>
                                @foreach($positions as $position)
                                    <option value="{{$position['id']}}" {{ $position['id'] == old('position_id',$employee->position_id) ? "selected" : "" }}>{{$position['name']}}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-md-3">
                            <label for="status_id">Status</label>
                            <select name="status_id" id="status_id" class="form-control form-control-sm rounded-0">
                                @foreach($statuses as $status)
                                    <option value="{{$status['id']}}" {{ $status['id'] == old('status_id',$employee->status_id) ? "selected" : "" }}>{{$status['name']}}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-md-3">
                            <label for="beginning_date">Beginning Date <span class="text-danger">*</span></label>
                            @error("beginning_date")
                                    <span class="text-danger">{{ $message }}<span>
                            @enderror
                            <input type="date" name="beginning_date" id="beginning_date" class="form-control form-control-sm rounded-0" placeholder="Choose Beginning Date" value="{{ old('beginning_date',$employee->beginning_date) }}"/>
                        </div>

                        <div class="col-md-3">
                            <label for="employee_code">Employee Code <span class="text-danger">*</span></label>
                            @error("employee_name")
                                    <span class="text-danger">{{ $message }}<span>
                            @enderror
                            <input type="text" name="employee_code" id="employee_code" class="form-control form-control-sm rounded-0" placeholder="Enter Employee Code" value="{{ old('employee_code',$employee->employee_code) }}"/>
                        </div>

                        <div class="col-md-3">
                            <label for="branch_id">Branch</label>
                            <select name="branch_id" id="branch_id" class="form-control form-control-sm rounded-0">
                                @foreach($branches as $branch)
                                    <option value="{{$branch['branch_id']}}" {{ $branch['branch_id'] == old('branch_id',$employee->branch_id) ? "selected" : "" }}>{{$branch['branch_name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="age">Age <span class="text-danger">*</span></label>
                            @error("age")
                                    <span class="text-danger">{{ $message }}<span>
                            @enderror
                            <input type="text" name="age" id="age" class="form-control form-control-sm rounded-0" placeholder="Enter Age" value="{{ old('age',$employee->age) }}"/>
                        </div>

                        <div class="col-md-3">
                            <label for="gender_id">Gender</label>
                            <select name="gender_id" id="gender_id" class="form-control form-control-sm rounded-0">
                                <option value="" selected disabled>Choose Gender</option>

                                @foreach($genders as $gender)
                                    <option value="{{$gender['id']}}" {{ $gender['id'] == old('gender_id',$employee->gender_id) ? "selected" : "" }}>{{$gender['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="position_level_id">Position Level</label>
                            <select name="position_level_id" id="position_level_id" class="form-control form-control-sm rounded-0">
                                <option value="" selected disabled>Choose Position Level</option>

                                @foreach($positionlevels as $positionlevel)
                                    <option value="{{$positionlevel['id']}}" {{ $positionlevel['id'] == old('position_level_id',$employee->position_level_id) ? "selected" : "" }}>{{$positionlevel['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="nrc">NRC <span class="text-danger">*</span></label>
                            @error("nrc")
                                    <span class="text-danger">{{ $message }}<span>
                            @enderror
                            <input type="text" name="nrc" id="nrc" class="form-control form-control-sm rounded-0" placeholder="Enter NRC" value="{{ old('nrc',$employee->nrc) }}"/>
                        </div>


                        <div class="col-md-3">
                            <label for="father_name">Father Name <span class="text-danger">*</span></label>
                            @error("father_name")
                                    <span class="text-danger">{{ $message }}<span>
                            @enderror
                            <input type="text" name="father_name" id="father_name" class="form-control form-control-sm rounded-0" placeholder="Enter Father Name" value="{{ old('father_name',$employee->father_name) }}"/>
                        </div>

                        <hr>

                        {{-- <div class="col-md-3">
                            <label for="division_id"></label>
                            <select name="division_id" id="division_id" class="form-control form-control-sm rounded-0">
                                <option value="" selected disabled>Choose Division</option>
                                @foreach($divisions as $division)
                                    <option value="{{$division['id']}}" {{ $division['id'] == old('division_id',$employee->division_id) ? "selected" : "" }}>{{$division['name']}}</option>
                                @endforeach
                            </select>
                        </div> --}}


                        <div class="col-md-12">
                            <hr/>

                            <h6>Form Info:</h6>
                        </div>

                        <div class="col-md-3">
                            <label for="attach_form_type_id">Attach Form Type</label>
                            <select name="attach_form_type_id" id="attach_form_type_id" class="form-control form-control-sm rounded-0">
                                <option value="" selected disabled>Choose Attach Form Type</option>
                                @foreach($attachformtypes as $attachformtype)
                                    <option value="{{$attachformtype['id']}}" {{ $attachformtype['id'] == old('attach_form_type_id',$employee->attach_form_type_id) ? "selected" : "" }}>{{$attachformtype['name']}}</option>
                                @endforeach
                            </select>
                        </div>



                        <div class="col-md-12 mt-2">

                            <button type="button" id="back-btn" class="btn btn-light btn-sm rounded-0" onclick="window.history.back();">Back</button>

                            <button type="submit" class="btn btn-primary btn-sm rounded-0">Update</button>
                        </div>
                    </div>
                </form>
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

</div>

</div>

<!-- START MODAL AREA -->



    <!-- start edit modal -->
    {{-- <div id="editmodal" class="modal fade">
    <div class="modal-dialog modal-lg modal-dialog-centered">
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
                                <div class="col-md-6">
                                    <label for="edit_name">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="edit_name" id="edit_name" class="form-control form-control-sm rounded-0" placeholder="Enter Status Name" value="{{ old('name') }}"/>
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_division_id">Division</label>
                                    <select name="edit_division_id" id="edit_division_id" class="form-control form-control-sm rounded-0">
                                        @foreach($divisions as $division)
                                            <option value="{{$division['id']}}">{{$division['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_department_id">Departments</label>
                                    <select name="edit_department_id" id="edit_department_id" class="form-control form-control-sm rounded-0">
                                        <option value="" selected disabled>Choose Department</option>
                                        @foreach($departments as $department)
                                            <option value="{{$department['id']}}">{{$department['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_sub_department_id">Sub Departments</label>
                                    <select name="edit_sub_department_id" id="edit_sub_department_id" class="form-control form-control-sm rounded-0">
                                        <option value="" selected disabled>Choose Sub Department</option>
                                        @foreach($subdepartments as $subdepartment)
                                            <option value="{{$subdepartment['id']}}">{{$subdepartment['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="edit_section_id">Sections</label>
                                    <select name="edit_section_id" id="edit_section_id" class="form-control form-control-sm rounded-0">
                                        <option value="" selected disabled>Choose Section</option>
                                        @foreach($sections as $section)
                                            <option value="{{$section['id']}}">{{$section['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="col-md-6">
                                    <label for="edit_status_id">Status</label>
                                    <select name="edit_status_id" id="edit_status_id" class="form-control form-control-sm rounded-0">
                                        @foreach($statuses as $status)
                                            <option value="{{$status['id']}}">{{$status['name']}}</option>
                                        @endforeach
                                    </select>
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
    </div> --}}
      <!-- end edit modal -->

<!-- End MODAL AREA -->
@endsection
@section('js')
<script>
    $(document).ready(function() {
        $("#division_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Division',
            searchField: ["value", "label"]
        });

        $("#department_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Department',
            searchField: ["value", "label"]
        });

        $("#sub_department_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Sub Department',
            searchField: ["value", "label"]
        });

        $("#section_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Section',
            searchField: ["value", "label"]
        });


        $("#status_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Status',
            searchField: ["value", "label"]
        });


        $("#position_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Position',
            searchField: ["value", "label"]
        });

        $("#gender_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Status',
            searchField: ["value", "label"]
        });

        $("#position_level_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Status',
            searchField: ["value", "label"]
        });



        $("#beginning_date,#enddate").flatpickr({
            dateFormat: "Y-m-d",
            {{-- minDate: "today", --}}
            {{-- maxDate: new Date().fp_incr(30) --}}
       });


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
            {{-- console.log($(this).attr("data-id"),$(this).attr("data-name")); --}}
            {{-- console.log($(this).attr("data-status")); --}}

            $("#edit_name").val($(this).attr("data-name"));
            $("#edit_code").val($(this).attr("data-code"));
            $("#edit_division_id").val($(this).attr("data-division"));
            $("#edit_department_id").val($(this).attr("data-department"));
            $("#edit_sub_department_id").val($(this).attr("data-sub_department"));
            $("#edit_status_id").val($(this).attr("data-status"));
            $("#edit_section_id").val($(this).attr("data-section"));


            const getid = $(this).attr("data-id");
            $("#formaction").attr("action",`/positions/${getid}`);

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
                  url:"/positionsstatus",
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
