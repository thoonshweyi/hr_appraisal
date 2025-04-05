@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="card shadow">
            <div class="card-header bg-primary text-white rounded-top peertopeers_headers">
                <h5 class="mb-0">Assessment Form Entry</h5>
            </div>
            <div class="card-body">
                <div class="row">


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


                    <div class="col-lg-12 my-2 ">
                        <form id="appraisal_form" action="{{ route('appraisalforms.store') }}" method="POST">
                            {{ csrf_field() }}
                            <div class="row align-items-start">

                                <div class="col-md-4">
                                    <div class="form-group d-flex" style="white-space: nowrap">
                                        <label for="assessor_user_id" >An Assessor: </label>
                                        <select name="assessor_user_id" id="assessor_user_id" class="form-control form-control-sm rounded-0  ml-2" value="{{ request()->assessor_user_id }}">
                                            <option value="" selected disabled>Choose Assessor</option>
                                                <option value="{{$assessoruser['id']}}" {{ $assessoruser['id'] == old('assessor_user_id',request()->assessor_user_id) ? "selected" : "" }}>{{$assessoruser['name']}}</option>
                                        </select>
                                    </div>

                                </div>

                                <div class="col-md-4">
                                    <div class="form-group d-flex" style="white-space: nowrap">

                                        <label for="appraisal_cycle_id">Appraisal Cycle: </label>
                                        <select name="appraisal_cycle_id" id="appraisal_cycle_id" class="form-control form-control-sm rounded-0 ml-2" value="{{ request()->appraisal_cycle_id }}">
                                            <option value="" selected disabled>Choose Appraisal Cycle</option>
                                                <option value="{{$appraisalcycle['id']}}" {{ $appraisalcycle['id'] == old('appraisal_cycle_id',request()->appraisal_cycle_id) ? "selected" : "" }}>{{$appraisalcycle['name']}}</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group d-flex wrapped_assformcats" style="white-space: nowrap;">

                                        <label for="ass_form_cat_id">Assessment Form Category</label>
                                        <select name="ass_form_cat_id" id="ass_form_cat_id" class="form-control form-control-sm rounded-0 ml-2" value="">
                                            <option value="" selected disabled>Choose Attach Form Type</option>
                                            @foreach($assformcats as $assformcat)
                                                <option value="{{$assformcat['id']}}" {{ $assformcat['id'] == old('ass_form_cat_id') ? "selected" : "" }}>{{$assformcat['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-3 d-flex justify-content-end">
                                    <button class="btn rounded-0 flex-fill mr-2 fill_btn" >
                                        <i class="fas fa-list-ol"></i> Fill
                                    </button>
                                    <button type="button" class="btn btn-danger rounded-0 flex-fill remove_btn">
                                        <i class="fas fa-times"></i> Remove All
                                    </button>
                                </div>

                                <div class="col-lg-12 mt-4">
                                    <div id="myloading-container" class=" d-none">
                                        <div class="text-center">
                                            <img src="{{ asset('images/spinner.gif') }}" id="myloading" class="myloading" alt="loading"/>
                                        </div>
                                    </div>

                                    <div id="table_containers" class="container-box d-none">

                                    </div>
                                </div>



                                <div class="col-md-12 mt-2">

                                    <button type="button" id="back-btn" class="btn btn-light btn-sm rounded-0" onclick="window.history.back();">Back</button>

                                    <button type="button" class="btn btn-success btn-sm rounded-0 send_btn">Send Form</button>
                                </div>
                            </div>
                        </form>
                    </div>



                </div>
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
        $("#assessor_user_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Division',
            searchField: ["value", "label"]
        });

        $("#appraisal_cycle_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Division',
            searchField: ["value", "label"]
        });


        $("#ass_form_cat_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Assessment Form Category',
            searchField: ["value", "label"]
        });

    });






        {{-- Start Fill Btn --}}
        $('.fill_btn').click(function (e) {
            e.preventDefault();

            Swal.fire({
                title: "Are you sure you want to fill Assessment Form",
                text: "",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, fill it!"
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: `/fillform`,
                        type: "GET",
                        dataType: "json",
                        data: $('#appraisal_form').serialize(),
                        beforeSend : function(){
                            $("#myloading-container").removeClass('d-none');
                        },
                        success: function (response) {
                            console.log(response);

                            let ths = "";
                            const assesseeusers = response.assesseeusers;
                            assesseeusers.forEach(function(assesseeuser){
                                ths += `
                                    <th style="width:auto;">${assesseeuser.employee.employee_name}</th>
                                    <input type="hidden" name="assessee_user_ids[]" value="${assesseeuser.id}"/>
                                `;

                            });


                            let trs = "";
                            const criterias = response.criterias;
                            criterias.forEach(function(criteria,idx){
                                var tds = "";
                                const assesseeusers = response.assesseeusers;
                                assesseeusers.forEach(function(assesseeuser){
                                    tds += `<td></td>`;
                                });


                                trs += `
                                    <tr>
                                        <td>${++idx}</td>
                                        <td class="text-left">${criteria.name}</td>
                                        <td >${criteria.excellent}</td>
                                        <td >${criteria.good}</td>
                                        <td >${criteria.meet_standard}</td>
                                        <td >${criteria.below_standard}</td>
                                        <td >${criteria.weak}</td>

                                        ${tds}
                                    </tr>
                                `;
                            });


                            table = `

                            <div class="table-responsive">

                                <table id="mytable" class="table table-bordered custables">
                                    <thead>
                                        <tr>
                                            <th>S/No</th>
                                            <th>CRITERIA Description</th>
                                            <th>Excellent</th>
                                            <th>Good</th>
                                            <th>Meet Standard</th>
                                            <th>Below Standard</th>
                                            <th>Weak</th>
                                            ${ths}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${trs}

                                    </tbody>
                                </table>
                            </div>

                            `
                            $("#table_containers").removeClass('d-none');
                            $("#table_containers").append(table);

                            $('.fill_btn').attr('disabled','disabled')

                        },
                        complete: function(){
                            $("#myloading-container").addClass('d-none');
                        },
                        error: function (response) {
                            console.log("Error:", response);
                        }
                    });
                }
            });



        });
        {{-- End Fill Btn --}}


        {{-- Start Remove Btn --}}
        $('.remove_btn').click(function(e){
            Swal.fire({
                title: "Are you sure you want to remove Assessment Form",
                text: "",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, remove it!"
            }).then((result) => {
                if (result.isConfirmed) {

                    $("#table_containers").addClass('d-none');
                    $("#table_containers").html('');

                    $('.fill_btn').removeAttr('disabled')

                }
            });
        });
        {{-- End Remove Btn --}}

        {{--  --}}

        $('.ass_form_cat_id').change(function(e){
            $('.fill_btn').removeAttr('disabled')

        });


        {{-- Start Send Btn--}}

        $('.send_btn').click(function(e){
            e.preventDefault();
            Swal.fire({
                title: "Are you sure you want to send Assessment Form",
                text: "",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, send it!"
            }).then((result) => {
                if (result.isConfirmed) {

                    $('#appraisal_form').submit();
                }
            });
        });
        {{-- End Send Btn --}}
</script>
@stop
