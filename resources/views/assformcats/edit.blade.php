@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Assessment-form Category Edit</h4>
                    </div>
                </div>
            </div>



            <div class="col-lg-12 my-2 ">
                <form id="" action="{{route('assformcats.update',$assformcat->id)}}" method="POST">
                    {{ csrf_field() }}
                    @method('PUT')
                    <div class="row align-items-start">
                        <div class="col-md-3">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            @error("name")
                                    <span class="text-danger">{{ $message }}<span>
                            @enderror
                            <input type="text" name="name" id="name" class="form-control form-control-sm rounded-0" placeholder="Enter Asssessment-form Category Name" value="{{ old('assformcat_name',$assformcat->name) }}"/>
                        </div>



                        <div class="col-md-3">
                            <label for="status_id">Status</label>
                            <select name="status_id" id="status_id" class="form-control form-control-sm rounded-0">
                                @foreach($statuses as $status)
                                    <option value="{{$status['id']}}" {{ $status['id'] == old('status_id',$assformcat->status_id) ? "selected" : "" }}>{{$status['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-12 d-flex justify-content-end">
                            <button type="button" class="btn add_btn"><i class="fas fa-plus"></i></button>
                        </div>

                        <div class="col-lg-12 mt-4">
                            <div class="container-box">
                                <div class="header-bar">CRITERIA Setup</div>

                                <div class="table-responsive">
                                    <table id="mytable" class="table table-bordered custables">
                                        <thead>
                                            <tr>
                                                <th>S/No</th>
                                                <th>CRITERIA Description</th>
                                                @foreach($ratingscales as $ratingscale)
                                                    <th>{{ $ratingscale->name }}</th>
                                                @endforeach
                                                <th>Inactive</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($criterias as $idx=>$criteria)
                                            @php
                                                $idx++
                                            @endphp
                                            <tr id="tb_row_{{$idx}}">
                                                <td>{{ $idx}}</td>
                                                <td class="cells">
                                                    <textarea type="text" name="names[]" class="custom-input-lg" value="{{ $criteria->name }}" placeholder="Write Something....">{{ $criteria->name }}</textarea>
                                                </td>
                                                <td>
                                                    <input type="text" name="excellents[]" class="custom-input" value="{{ $criteria->excellent }}">
                                                </td>
                                                <td>
                                                    <input type="text" name="goods[]" class="custom-input" value="{{ $criteria->good }}">
                                                </td>
                                                <td>
                                                    <input type="text" name="meet_standards[]" class="custom-input" value="{{ $criteria->meet_standard }}">
                                                </td>

                                                <td>
                                                    <input type="text" name="below_standards[]" class="custom-input" value="{{ $criteria->below_standard }}">
                                                </td>
                                                <td>
                                                    <input type="text" name="weeks[]" class="custom-input" value="{{ $criteria->week }}">
                                                </td>
                                                <td>
                                                    <input type="checkbox" name="status_ids[]" class="status_ids" value="1" {{ $criteria->status_id == 1 ? "checked" : ''  }}>
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0);" type="button" title="Remove" class="remove-btns text-danger" data-id='{{ $idx}}'>
                                                        <i class="fas fa-minus-circle fa-lg"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2">Total Score</td>
                                                <td><span id="total_excellent">{{ $total_excellent }}</span></td>
                                                <td><span id="total_good">{{ $total_good }}</span></td>
                                                <td><span id="total_meet_standard">{{ $total_meet_standard }}</span></td>
                                                <td><span id="total_below_standard">{{ $total_below_standard }}</span></td>
                                                <td><span id="total_week">{{ $total_week }}</span></td>

                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                            </div>
                        </div>

                        <div class="col-md-12 mt-2">

                            <button type="button" id="back-btn" class="btn btn-light btn-sm rounded-0" onclick="window.history.back();">Back</button>

                            <button type="submit" class="btn btn-primary btn-sm rounded-0">Update</button>
                        </div>
                    </div>
                </form>
            </div>



            <div class="col-lg-12">
                <form class="d-inline" action="{{ route('criterias.excel_import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row align-items-end">

                        <div class="col-md-4">
                            @error("file")
                            <b class="text-danger">{{ $message }}</b>
                            @enderror
                            <label for="file" class="gallery @error('file') is-invalid @enderror mb-0"><span>Choose Excel File</span></label>
                            <input type="file" name="file" id="file" class="form-control form-control-sm rounded-0" value="" hidden/>
                        </div>
                        <input type="hidden" id="ass_form_cat_id" name="ass_form_cat_id" value="{{ $assformcat->id }}"/>


                        <button type="submit" class="btn btn-light" class=""><i class="ri-file-download-line"></i> Import</a>
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


    {{-- Start Add Btn --}}
            var cri_idx = {{ count($criterias) + 1 }};
            $('.add_btn').click(function() {
                let html = `
                    <tr id="tb_row_${cri_idx}">
                        <td>${cri_idx}</td>
                        <td class="cells">
                            <input type="text" name="names[]" class="custom-input-lg" value="" placeholder="Write Something....">
                        </td>

                        <td><input type="text" name="excellents[]" class="custom-input" value=""></td>
                        <td><input type="text" name="goods[]" class="custom-input" value=""></td>
                        <td><input type="text" name="meet_standards[]" class="custom-input" value=""></td>
                        <td><input type="text" name="below_standards[]" class="custom-input" value=""></td>
                        <td><input type="text" name="weeks[]" class="custom-input" value=""></td>

                        <td>
                            <input type="checkbox" name="status_ids[]" class="status_ids" value="1" checked>
                        </td>
                        <td>
                            <a href="javascript:void(0);" type="button" title="Remove" class="remove-btns text-danger" data-id='${cri_idx}'>
                                <i class="fas fa-minus-circle fa-lg"></i>
                            </a>
                        </td>
                    </tr>
                `;
                $("#mytable tbody").append(html);
                cri_idx++;
            });

            $(document).on('change', '.status_ids', function() {
                if (!$(this).is(':checked')) {
                    $(this).after('<input type="hidden" name="status_ids[]" value="2">');
                } else {
                    $(this).siblings('input[type="hidden"]').remove();
                }
            });
            $('.status_ids').trigger('change');



    });
    {{-- End Add Btn --}}


    {{-- Start Remove Btn --}}
    $(document).on("click",".remove-btns",function(){
        {{-- console.log('hay'); --}}
        var getid = $(this).data("id");

        console.log(`tb_row_${getid}`);
        $(`#tb_row_${getid}`).remove();

    });


    $(document).on("change",".status_ids",function(){
        {{-- console.log($(this).prop("checked")); --}}

        if($(this).prop("checked")){
            $(this).val('1');
        }else{
            $(this).val('2');
        }
    });

    {{-- End Remove Btn --}}
    $('.custom-input-lg').each(function () {
        // Adjust height on page load
        $(this).css('height', 'auto').css('height', this.scrollHeight + 'px');
    });

    $('.custom-input-lg').on('input', function () {
        $(this).css('height', 'auto').css('height', this.scrollHeight + 'px');
    });



    function updateTotals() {
        let total_excellent = 0;
        let total_good = 0;
        let total_meet_standard = 0;
        let total_below_standard = 0;
        let total_week = 0;

        // Loop through each input and sum values
        $("input[name='excellents[]']").each(function() {
            total_excellent += parseInt($(this).val()) || 0;
        });

        $("input[name='goods[]']").each(function() {
            total_good += parseInt($(this).val()) || 0;
        });

        $("input[name='meet_standards[]']").each(function() {
            total_meet_standard += parseInt($(this).val()) || 0;
        });

        $("input[name='below_standards[]']").each(function() {
            total_below_standard += parseInt($(this).val()) || 0;
        });

        $("input[name='weeks[]']").each(function() {
            total_week += parseInt($(this).val()) || 0;
        });

        // Update UI with new totals
        $("#total_excellent").text(total_excellent);
        $("#total_good").text(total_good);
        $("#total_meet_standard").text(total_meet_standard);
        $("#total_below_standard").text(total_below_standard);
        $("#total_week").text(total_week);
    }

    // Call updateTotals() when an input changes
    $(".custom-input").on("input", function() {
        updateTotals();
    });
</script>
@stop
