@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="card shadow">
            <div class="card-header bg-primary text-white rounded-top peertopeers_headers">
                <h5 class="mb-0">An Assessor tag to Assessee(s)</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        @if (count($errors) > 0)
                        <div class="alert alert-danger alert-dismissible fade show alert-dismissible fade show">
                            <strong>Whoops!</strong> There were some problems with your input.<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close text-danger" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
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
                    </div>



                    <div class="col-lg-12 my-2 ">
                        <form id="peer_to_peer_form" action="{{ route('peertopeers.store') }}" method="POST">
                            {{ csrf_field() }}
                            <div class="row align-items-start">

                                <div class="col-md-4">
                                    <div class="form-group d-flex" style="white-space: nowrap">
                                        <label for="assessor_user_id" >An Assessor: </label>
                                        <select name="assessor_user_id" id="assessor_user_id" class="form-control form-control-sm rounded-0  ml-2" value="{{ request()->assessor_user_id }}">
                                            <option value="" selected disabled>Choose Assessor</option>
                                            @foreach($users as $user)
                                                <option value="{{$user['id']}}" {{ $user['id'] == old('assessor_user_id',request()->assessor_user_id) ? "selected" : "" }}>{{$user['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>

                                <div class="col-md-4">
                                    <div class="form-group d-flex" style="white-space: nowrap">

                                        <label for="appraisal_cycle_id">Appraisal Cycle: </label>
                                        <select name="appraisal_cycle_id" id="appraisal_cycle_id" class="form-control form-control-sm rounded-0 ml-2" value="{{ request()->appraisal_cycle_id }}">
                                            <option value="" selected disabled>Choose Appraisal Cycle</option>
                                            @foreach($appraisalcycles as $appraisalcycle)
                                                <option value="{{$appraisalcycle['id']}}" {{ $appraisalcycle['id'] == old('appraisal_cycle_id',request()->appraisal_cycle_id) ? "selected" : "" }}>{{$appraisalcycle['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group d-flex wrapped_assformcats" style="white-space: nowrap;">

                                        <label for="filter_ass_form_cat_id">Criteria Set</label>
                                        <select name="filter_ass_form_cat_id" id="filter_ass_form_cat_id" class="form-control form-control-sm rounded-0 ml-2" value="">
                                            <option value="" selected disabled>Choose Criteria Set</option>
                                            @foreach($assformcats as $assformcat)
                                                <option value="{{$assformcat['id']}}" {{ $assformcat['id'] == old('filter_ass_form_cat_id') ? "selected" : "" }}>{{$assformcat['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group d-flex" style="white-space: nowrap">

                                        <label for="filter_branch_id">Assessee(s) Branch: </label>
                                        <select name="filter_branch_id" id="filter_branch_id" class="form-control form-control-sm rounded-0 ml-2">
                                            <option value="" selected disabled>Choose Branch</option>
                                            @foreach($branches as $branch)
                                                <option value="{{$branch['branch_id']}}" {{ $branch['branch_id'] == old('filter_branch_id') ? "selected" : "" }}>{{$branch['branch_name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- <div class="col-md-4">
                                    <div class="form-group d-flex" style="white-space: nowrap">

                                        <label for="filter_department_id">Departments: </label>
                                        <select name="filter_department_id" id="filter_department_id" class="form-control form-control-sm rounded-0 ml-2">
                                            <option value="" selected disabled>Choose Department</option>
                                            @foreach($departments as $department)
                                                <option value="{{$department['id']}}" {{ $department['id'] == old('filter_department_id') ? "selected" : "" }}>{{$department['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div> --}}


                                {{-- <div class="col-md-4">
                                    <div class="form-group d-flex" style="white-space: nowrap">
                                           <label for="filter_subdepartment_id">Sub Department: </label>
                                            <select name="filter_subdepartment_id" id="filter_subdepartment_id" class="form-control form-control-sm rounded-0 ml-2">
                                            <option value="" selected disabled>Choose Sub Department</option>
                                            @foreach($subdepartments as $subdepartment)
                                                        <option value="{{$subdepartment['id']}}" {{ $subdepartment['id'] == request()->filter_subdepartment_id ? 'selected' : '' }}>{{$subdepartment['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div> --}}

                                {{-- <div class="col-md-4">
                                    <div class="form-group d-flex" style="white-space: nowrap">
                                           <label for="filter_section_id">Section: </label>
                                            <select name="filter_section_id" id="filter_section_id" class="form-control form-control-sm rounded-0 ml-2">
                                            <option value="" selected disabled>Choose Section</option>
                                            @foreach($sections as $section)
                                                        <option value="{{$section['id']}}" {{ $section['id'] == request()->filter_section_id ? 'selected' : '' }}>{{$section['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div> --}}

                                <div class="col-md-4">
                                    <div class="form-group d-flex" style="white-space: nowrap">
                                           <label for="filter_sub_section_id">Sub Section: </label>
                                            <select name="filter_sub_section_id" id="filter_sub_section_id" class="form-control form-control-sm rounded-0 ml-2">
                                            <option value="" selected disabled>Choose Section</option>
                                            @foreach($subsections as $subsection)
                                                        <option value="{{$subsection['id']}}" {{ $subsection['id'] == request()->filter_sub_section_id ? 'selected' : '' }}>{{$subsection['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3 d-flex justify-content-end">
                                    <button class="btn rounded-0 flex-fill mr-2 apply_btn" >
                                        <i class="fas fa-filter "></i> Apply
                                    </button>
                                    <button type="button" class="btn btn-danger rounded-0 flex-fill clear_btn">
                                        <i class="fas fa-times"></i> Clear
                                    </button>
                                </div>

                                <div class="col-lg-12 my-2">
                                    <div id="myloading-container" class=" d-none">
                                        <div class="text-center">
                                            <img src="{{ asset('images/spinner.gif') }}" id="myloading" class="myloading" alt="loading"/>
                                        </div>
                                    </div>
                                    <!-- Table -->
                                   <div class="table-responsive mt-3">
                                       <table id="assesseestable" class="table table-bordered">
                                           <thead class="table-dark">
                                               <tr>
                                                   <th><input type="checkbox" id="selectAll"></th>
                                                   <th>Assessee Code</th>
                                                   <th>Assessee</th>
                                                   {{-- <th>Department</th> --}}
                                                   <th>Sub Section</th>
                                                   <th>Branch</th>
                                                   <th>Position Level</th>
                                                   <th>Position</th>
                                                   <th>Criteria Set</th>
                                               </tr>
                                           </thead>
                                           <tbody id="assesseeTable">

                                           </tbody>
                                       </table>
                                   </div>
                               </div>



                                <div class="col-md-12 mt-2">

                                    <button type="button" id="back-btn" class="btn btn-light btn-sm rounded-0" onclick="window.history.back();">Back</button>

                                    <button type="button" class="btn btn-success btn-sm rounded-0 save-btns">Save Selection</button>
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
            placeholder: 'Choose Assessor',
            searchField: ["value", "label"]
        });

        $("#appraisal_cycle_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Appraisal Cycle',
            searchField: ["value", "label"]
        });

        $("#filter_ass_form_cat_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose  Criteria Set',
            searchField: ["value", "label"]
        });

        $("#filter_branch_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Branch',
            searchField: ["value", "label"]
        });

        {{-- $("#filter_subdepartment_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Sub Department',
            searchField: ["value", "label"]
        });

        $("#filter_section_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Section',
            searchField: ["value", "label"]
        }); --}}

        $("#filter_sub_section_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Section',
            searchField: ["value", "label"]
        });


        $("#beginning_date,#enddate").flatpickr({
            dateFormat: "Y-m-d",
            {{-- minDate: "today", --}}
            {{-- maxDate: new Date().fp_incr(30) --}}
       });







        {{-- Start Assessee Filter --}}
        let selectedAssessees = {}; // Store selected users persistently
        let curAssessees = []; // Declare globally to avoid reference errors

        let tableBody = document.querySelector("#assesseestable tbody");

        $('.apply_btn').click(function (e) {
            e.preventDefault();
            $.ajax({
                url: `/getfilteredassessees`,
                type: "GET",
                dataType: "json",
                data: $('#peer_to_peer_form').serialize(),
                beforeSend : function(){
                    $("#myloading-container").removeClass('d-none');
                },
                success: function (response) {
                    {{-- console.log(response); --}}
                    renderTable(response);
                },
                complete: function(){
                    $("#myloading-container").addClass('d-none');
                },
                error: function (response) {
                    console.log("Error:", response);
                }
            });
        });

        function renderTable(assesseeusers) {
            let html = '';

            // Update global variable
            curAssessees = [...assesseeusers];

            // Re-add previously selected users
            Object.keys(selectedAssessees).forEach(selectedID => {
                let alreadyInList = assesseeusers.some(user => user.id + '-' + user.assformcat.id == selectedID);
                if (!alreadyInList) {
                    curAssessees.push(selectedAssessees[selectedID]);
                }
            });


            curAssessees.forEach(function (assesseeuser) {
                let assesseeID = assesseeuser.id;
                let formcatID = assesseeuser.assformcat.id;
                let isChecked = selectedAssessees[assesseeID+ '-' +formcatID] ? "checked" : "";
                let isDisabled = selectedAssessees[assesseeID+ '-' +formcatID] ? "" : "disabled";


                html += `
                <tr>
                    <td>
                        <input type="checkbox" name="assessee_user_ids[]" class="assesseeCheckbox"
                            value="${assesseeID}" ${isChecked} data-id="${assesseeID}">
                        <input type="hidden" name="ass_form_cat_ids[]" class="ass_form_cat_ids"
                        value="${assesseeuser.assformcat ? assesseeuser.assformcat.id : ''}" ${isDisabled} data-id="${assesseeID}">
                    </td>
                    <td>${assesseeuser.employee.employee_code}</td>
                    <td>${assesseeuser.employee.employee_name}</td>
                    {{-- <td>${assesseeuser.employee.department.name}</td> --}}
                    {{-- <td>${assesseeuser.employee.subdepartment.name}</td> --}}
                    <td>${assesseeuser.employee.subsection.name}</td>
                    <td>${assesseeuser.employee.branch.branch_name}</td>
                    <td>${assesseeuser.employee.positionlevel.name}</td>
                    <td>${assesseeuser.employee.position.name}</td>
                    <td>${assesseeuser.assformcat ? assesseeuser.assformcat.name : ''}</td>
                </tr>`;
            });

            {{-- console.log(curAssessees); --}}



            tableBody.innerHTML = html; // Replace content instead of appending

            // Rebind checkbox change event
            $(".assesseeCheckbox").change(function () {
                let assesseeID = $(this).data("id");
                let nextInput = $(this).next(".ass_form_cat_ids");
                let formcatID = $(this).next(".ass_form_cat_ids").val();

                if ($(this).is(":checked")) {
                    selectedAssessees[assesseeID + '-' + formcatID ] = curAssessees.find(u => u.id == assesseeID && u.assformcat.id == formcatID);
                    $(this).next('.ass_form_cat_ids').removeAttr("disabled");
                    {{-- console.log($(this).next('.ass_form_cat_ids')); --}}
                } else {
                    delete selectedAssessees[assesseeID + '-' + formcatID];
                    $(this).next('.ass_form_cat_ids').attr("disabled", "disabled");
                }
                console.log(selectedAssessees);
            });
        }

        // Select All Checkbox Handler
        $(document).on("change", "#selectAll", function () {
            let isChecked = $(this).is(":checked");

            $(".assesseeCheckbox").each(function () {
                $(this).prop("checked", isChecked);
                let assesseeID = $(this).val();
                let nextInput = $(this).next(".ass_form_cat_ids");

                let formcatID = $(this).next(".ass_form_cat_ids").val();
                if (isChecked) {
                    selectedAssessees[assesseeID+ '-' +formcatID] = curAssessees.find(u => u.id == assesseeID);
                    nextInput.removeAttr("disabled");
                } else {
                    delete selectedAssessees[assesseeID+ '-' +formcatID];
                    nextInput.attr("disabled", "disabled");
                }
            });

            console.log(selectedAssessees);
        });

        {{-- End Assessee Filter --}}



        {{-- Start Clear Btn --}}
        $('.clear_btn').click(function(e){
            selectedAssessees = {};
            curAssessees = [];
            tableBody.innerHTML = '';
            Swal.fire({
                icon: "success",
                title: "Selected assessees cleared successfully",
                text: "Now, can collect new group of assessees.",
            });
        });
        {{-- End Clear Btn --}}


        {{-- Start Save Btn --}}
        $('.save-btns').click(function(e){
            Swal.fire({
                title: "Are you sure you want to save Peer To Peer",
                text: "",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, save it!"
            }).then((result) => {
                if (result.isConfirmed) {

                    $('#peer_to_peer_form').submit();
                }
            });
        });
        {{-- End Save Btn --}}
    });
</script>

@stop
