@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">


        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Criteria Set Edit</h4>
                    </div>
                </div>
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


            <div class="col-lg-12 my-2 ">
                <form id="assformcats_form" action="{{route('assformcats.update',$assformcat->id)}}" method="POST">
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
                            <label for="attach_form_type_id">Attach Form Type</label>
                            <select name="attach_form_type_id" id="attach_form_type_id" class="form-control form-control-sm rounded-0">
                                <option value="" selected disabled>Choose Attach Form Type</option>
                                @foreach($attachformtypes as $attachformtype)
                                    <option value="{{$attachformtype['id']}}" {{ $attachformtype['id'] == old('attach_form_type_id',$assformcat->attach_form_type_id) ? "selected" : "" }}>{{$attachformtype['name']}}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-md-3">
                            <label for="location_id">Location</label>
                            <select name="location_id" id="location_id" class="form-control form-control-sm rounded-0">
                                <option value="" selected disabled>Choose Location</option>
                                <option value="7" {{ old('location_id',$assformcat->location_id) == '7' ? 'selected' : '' }}>HO</option>
                                <option value="0" {{ old('location_id',$assformcat->location_id) == '0' ? 'selected' : '' }}>Branch</option>
                                {{-- @foreach($attachformtypes as $attachformtype)
                                    <option value="{{$attachformtype['id']}}" {{ $attachformtype['id'] == old('attach_form_type_id') ? "selected" : "" }}>{{$attachformtype['name']}}</option>
                                @endforeach --}}
                            </select>
                        </div>


                        <div class="col-md-3">
                            <label for="lang">Language</label>

                            <div class="d-flex">
                                <div class="form-check">
                                    <input class="form-check-input lang d-none" type="radio" name="lang" id="langmm" value="mm" {{ $assformcat->lang == 'mm' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="langmm">
                                        <span class="badge bg-light me-1" style="font-size: 12px !important;">Myanmar</span>
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input lang d-none" type="radio" name="lang" id="langen" value="en" {{ $assformcat->lang == 'en' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="langen">
                                        <span class="badge bg-light me-1" style="font-size: 12px !important;">English</span>
                                    </label>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-3">
                            <label for="position_level_ids">Position Level</label>
                            <select name="position_level_ids[]" id="position_level_ids" class="form-control form-control-sm rounded-0" multiple>
                                <option value="" selected disabled>Choose Position Level</option>

                                @foreach($positionlevels as $positionlevel)
                                    <option value="{{$positionlevel['id']}}"  {{ in_array($positionlevel->id,$assformcat->positionlevels->pluck('id')->toArray()) ? 'selected' : '' }}>{{$positionlevel['name']}}</option>
                                @endforeach
                            </select>
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
                                                <input type="hidden" name="criteriaids[]" id="{{ $criteria->id }}" value="{{ $criteria->id }}">
                                                <td class="cells">
                                                    <textarea type="text" name="names[]" class="custom-input-lg" value="{{ $criteria->name }}" placeholder="Write Something....">{{ $criteria->name }}</textarea>
                                                </td>
                                                <td>
                                                    <input type="text" name="excellents[]" class="custom-input" value="{{ $criteria->excellent }}" data-oldVal={{ $criteria->excellent }}>
                                                </td>
                                                <td>
                                                    <input type="text" name="goods[]" class="custom-input" value="{{ $criteria->good }}" data-oldVal={{ $criteria->good }}>
                                                </td>
                                                <td>
                                                    <input type="text" name="meet_standards[]" class="custom-input" value="{{ $criteria->meet_standard }}" data-oldVal={{ $criteria->meet_standard }}>
                                                </td>

                                                <td>
                                                    <input type="text" name="below_standards[]" class="custom-input" value="{{ $criteria->below_standard }}" data-oldVal={{ $criteria->below_standard }}>
                                                </td>
                                                <td>
                                                    <input type="text" name="weaks[]" class="custom-input" value="{{ $criteria->weak }}" data-oldVal={{ $criteria->weak }}>
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
                                                <td><span id="total_excellent">{{ $total_excellent }}</span> <input type="hidden" name="total_excellent" /></td>
                                                <td><span id="total_good">{{ $total_good }}</span> <input type="hidden" name="total_good" /></td>
                                                <td><span id="total_meet_standard">{{ $total_meet_standard }}</span> <input type="hidden" name="total_meet_standard" /></td>
                                                <td><span id="total_below_standard">{{ $total_below_standard }}</span> <input type="hidden" name="total_below_standard" /></td>
                                                <td><span id="total_weak">{{ $total_weak }}</span> <input type="hidden" name="total_weak" /></td>

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

                            <button type="button" class="btn btn-primary btn-sm rounded-0 update_btns">Update</button>
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


<!-- End MODAL AREA -->
@endsection
@section('js')
<script>
    $(document).ready(function() {


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



        $("#position_level_ids").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 9,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Status',
            searchField: ["value", "label"]
        });



        $("#attach_form_type_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Status',
            searchField: ["value", "label"]
        });

        $("#location_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Status',
            searchField: ["value", "label"]
        });

       {{-- Start Update Btn --}}

       $('.update_btns').click(function(e){
            Swal.fire({
                title: "Are you sure you want to update Criteria Set",
                text: "",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, update it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#assformcats_form').submit();
                }
            });



        });
       {{-- End Update Btn --}}

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
                            <input type="text" name="newnames[]" class="custom-input-lg" value="" placeholder="Write Something....">
                        </td>

                        <td><input type="text" name="newexcellents[]" class="custom-input" value=""></td>
                        <td><input type="text" name="newgoods[]" class="custom-input" value=""></td>
                        <td><input type="text" name="newmeet_standards[]" class="custom-input" value=""></td>
                        <td><input type="text" name="newbelow_standards[]" class="custom-input" value=""></td>
                        <td><input type="text" name="newweaks[]" class="custom-input" value=""></td>

                        <td>
                            <input type="checkbox" name="newstatus_ids[]" class="status_ids newstatus_ids" value="1" checked>
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

                    if($(this).hasClass('newstatus_ids')){
                        $(this).after('<input type="hidden" name="newstatus_ids[]" value="2">');
                    }else{
                        $(this).after('<input type="hidden" name="status_ids[]" value="2">');
                    }
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


    const max_total_excellent = 100;
    const max_total_good = 84;
    const max_total_meet_standard = 67;
    const max_total_below_standard = 40;
    const max_total_weak = 19;
    console.log(max_total_excellent);
    function updateTotals() {
        let total_excellent = 0;
        let total_good = 0;
        let total_meet_standard = 0;
        let total_below_standard = 0;
        let total_weak = 0;

        // Loop through each input and sum values
        $("input[name='excellents[]'], input[name='newexcellents[]']").each(function() {
            total_excellent += parseInt($(this).val()) || 0;
        });

        $("input[name='goods[]'], input[name='newgoods[]']").each(function() {
            total_good += parseInt($(this).val()) || 0;
        });

        $("input[name='meet_standards[]'], input[name='newmeet_standards[]']").each(function() {
            total_meet_standard += parseInt($(this).val()) || 0;
        });

        $("input[name='below_standards[]'], input[name='newbelow_standards[]']").each(function() {
            total_below_standard += parseInt($(this).val()) || 0;
        });

        $("input[name='weaks[]'], input[name='newweaks[]']").each(function() {
            total_weak += parseInt($(this).val()) || 0;
        });


        // Validate limits
        if (total_excellent > max_total_excellent) {
            {{-- alert("Total Excellent cannot exceed " + max_total_excellent); --}}
            Swal.fire({
                title: "Invalid Value",
                text: "Total Excellent cannot exceed " + max_total_excellent,
                icon: "error"
           });
            return false;
        }

        if (total_good > max_total_good) {
            {{-- alert("Total Good cannot exceed " + max_total_good); --}}
            Swal.fire({
                title: "Invalid Value",
                text: "Total Good cannot exceed " + max_total_good,
                icon: "error"
           });
            return false;
        }

        if (total_meet_standard > max_total_meet_standard) {
            {{-- alert("Total Meet Standard cannot exceed " + max_total_meet_standard); --}}
            Swal.fire({
                title: "Invalid Value",
                text: "Total Meet Standard cannot exceed " + max_total_meet_standard,
                icon: "error"
           });
            return false;
        }

        if (total_below_standard > max_total_below_standard) {
            {{-- alert("Total Below Standard cannot exceed " + max_total_below_standard); --}}
            Swal.fire({
                title: "Invalid Value",
                text: "Total Below Standard cannot exceed " + max_total_below_standard,
                icon: "error"
           });
            return false;
        }

        if (total_weak > max_total_weak) {
            {{-- alert("Total Weak cannot exceed " + max_total_weak); --}}
            Swal.fire({
                title: "Invalid Value",
                text: "Total Weak cannot exceed " + max_total_weak,
                icon: "error"
           });
            return false;
        }

        // Update UI with new totals
        $("#total_excellent").text(total_excellent);
        $("#total_good").text(total_good);
        $("#total_meet_standard").text(total_meet_standard);
        $("#total_below_standard").text(total_below_standard);
        $("#total_weak").text(total_weak);
        return true;
    }

    // Call updateTotals() when an input changes
    {{-- $(".custom-input").on("input", function () {
        const oldValue = $(this).data('oldval') ;
        console.log(oldValue);
        const isValid = updateTotals();

        if (!isValid) {
            $(this).val(oldValue);
            return;
        }

        $(this).data('oldVal', $(this).val());
    }); --}}


    $(document).on('input', '.custom-input', function() {
        const oldValue = $(this).data('oldval');
        console.log(oldValue);
        const isValid = updateTotals();

        if (!isValid) {
            $(this).val(oldValue);
            return;
        }

        $(this).data('oldVal', $(this).val());
    });
</script>
@stop
