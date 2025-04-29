@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Appraisal Cycle Create</h4>
                    </div>
                </div>
            </div>



            <div class="col-lg-12 my-2 ">
                <form id="" action="{{route('appraisalcycles.store')}}" method="POST">
                    {{ csrf_field() }}
                    <div class="row align-items-start">
                        <div class="col-md-3">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            @error("name")
                                    <span class="text-danger">{{ $message }}<span>
                            @enderror
                            <input type="text" name="name" id="name" class="form-control form-control-sm rounded-0" placeholder="Enter Appraisal Cycle Name" value="{{ old('name') }}"/>
                        </div>


                        <div class="col-md-3">
                            <label for="status_id">Status</label>
                            <select name="status_id" id="status_id" class="form-control form-control-sm rounded-0">
                                @foreach($statuses as $status)
                                    <option value="{{$status['id']}}" {{ $status['id'] == old('status_id') ? "selected" : "" }}>{{$status['name']}}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-md-3">
                            <label for="start_date">Period Start Date <span class="text-danger">*</span></label>
                            @error("start_date")
                                    <span class="text-danger">{{ $message }}<span>
                            @enderror
                            <input type="date" name="start_date" id="start_date" class="form-control form-control-sm rounded-0" placeholder="Choose Start Date" value="{{ old('start_date') }}"/>
                        </div>


                        <div class="col-md-3">
                            <label for="end_date">Period End Date <span class="text-danger">*</span></label>
                            @error("end_date")
                                    <span class="text-danger">{{ $message }}<span>
                            @enderror
                            <input type="date" name="end_date" id="end_date" class="form-control form-control-sm rounded-0" placeholder="Choose End Date" value="{{ old('end_date') }}"/>
                        </div>


                        <div class="col-md-3">
                            <label for="action_start_date">Action Start Date <span class="text-danger">*</span></label>
                            @error("action_start_date")
                                    <span class="text-danger">{{ $message }}<span>
                            @enderror
                            <input type="date" name="action_start_date" id="action_start_date" class="form-control form-control-sm rounded-0" placeholder="Choose Action Start Date" value="{{ old('action_start_date') }}"
                            onChange="check_start_date(this.value);"
                            />
                        </div>


                        <div class="col-md-3">
                            <label for="action_end_date">Action End Date <span class="text-danger">*</span></label>
                            @error("action_end_date")
                                    <span class="text-danger">{{ $message }}<span>
                            @enderror
                            <input type="date" name="action_end_date" id="action_end_date" class="form-control form-control-sm rounded-0" placeholder="Choose Action End Date" value="{{ old('action_end_date') }}"
                             onChange="check_end_date(this.value);"
                            />
                        </div>


                        <div class="col-md-12">
                            <label for="description">Description <span class="text-danger">*</span></label>
                            @error("description")
                                    <span class="text-danger">{{ $message }}<span>
                            @enderror
                            <textarea name="description" id="description" class="form-control form-control-sm rounded-0 fixedtxtareas" cols="30" rows="4" placeholder="Write Something...."></textarea>
                        </div>


                        <div class="col-md-12 mt-2">

                            <button type="button" id="back-btn" class="btn btn-light btn-sm rounded-0" onclick="window.history.back();">Back</button>

                            <button type="submit" class="btn btn-primary btn-sm rounded-0">Submit</button>
                        </div>
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

        $("#start_date,#end_date").flatpickr({
            dateFormat: "Y-m-d",
            {{-- minDate: "today", --}}
            {{-- maxDate: new Date().fp_incr(30) --}}
       });

        $('#action_start_date').flatpickr({
            dateFormat: "Y-m-d",
            minDate: "today",
            {{-- maxDate: new Date().fp_incr(30) --}}
       });
       $('#action_end_date').flatpickr({
                dateFormat: "Y-m-d",
                minDate: new Date($("#action_start_date").val()),
                {{-- maxDate: new Date().fp_incr(30) --}}
        });




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

    function check_start_date(start_date) {
        start_date = new Date(start_date);
        end_date = $('#action_end_date').val();
        var today = new Date();
        d_end_date = new Date(end_date);
        if (start_date > d_end_date) {
            Swal.fire({
                icon: 'warning',
                title: "{{ __('message.warning') }}",
                text: "{{ __('message.start_date_is_not_grether_than_end_date') }}",
                confirmButtonText: "{{ __('message.ok') }}",
            }).then(function() {
                $('#action_start_date').val(end_date);
                return false;

            });
        }
        if (today > start_date) {
            Swal.fire({
                icon: 'warning',
                title: "{{ __('message.warning') }}",
                text: "{{ __('message.start_date_is_not_last_than_today') }}",
                confirmButtonText: "{{ __('message.ok') }}",
            }).then(function() {
                $('#action_start_date').val(formatDate(today));
                return false;

            });
        }
    }


    function check_end_date(end_date) {
        end_date = new Date(end_date);
        start_date = $('#action_start_date').val();
        var today = new Date();
        d_start_date = new Date(start_date);
        if (d_start_date > end_date) {
            Swal.fire({
                icon: 'warning',
                title: "{{ __('message.warning') }}",
                text: "{{ __('message.end_date_is_not_last_than_start_date') }}",
                confirmButtonText: "{{ __('message.ok') }}",
            }).then(function() {
                $('#action_end_date').val(start_date);
                return false;

            });
        }
        if (today > end_date) {
            Swal.fire({
                icon: 'warning',
                title: "{{ __('message.warning') }}",
                text: "{{ __('message.end_date_is_not_last_than_today') }}",
                confirmButtonText: "{{ __('message.ok') }}",
            }).then(function() {
                $('#action_end_date').val(formatDate(today));
                return false;

            });
        }
    }


    function formatDate(date) {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;

        return [year, month, day].join('-');
    }
</script>
@stop
