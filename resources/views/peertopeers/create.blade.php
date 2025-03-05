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
                    {{-- <div class="col-lg-12">
                        <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-2">
                                <h4 class="mb-3 peertopeers_titles">An Assessor tag to Assessee(s)</h4>
                        </div>
                    </div> --}}

                    {{-- <div class="col-lg-12 my-2 ">
                        <h4>Assessor Filter</h4>
                        <form class="d-inline" action="{{ route('positions.index') }}" method="GET">
                            @csrf
                            <div class="row align-items-end">

                                <div class="col-md-2">
                                    <label for="filter_name">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="filter_name" id="filter_name" class="form-control form-control-sm rounded-0" placeholder="Enter Department Name" value="{{ request()->filter_name }}"/>
                                </div>

                                <div class="col-md-2">
                                    <label for="filter_division_id">Division</label>
                                    <select name="filter_division_id" id="filter_division_id" class="form-control form-control-sm rounded-0">
                                        <option value="" selected disabled>Choose Division</option>
                                        @foreach($divisions as $division)
                                            <option value="{{$division['id']}}" {{ $division['id'] == request()->filter_division_id ? 'selected' : '' }}>{{$division['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>



                            </div>

                        </form>
                        <hr>
                    </div> --}}



                    <div class="col-lg-12 my-2 ">
                        <form id="peer_to_peer_form" action="" method="POST">
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

                                        <label for="filter_ass_form_cat_id">Assessment Form Category</label>
                                        <select name="filter_ass_form_cat_id" id="filter_ass_form_cat_id" class="form-control form-control-sm rounded-0 ml-2" value="">
                                            <option value="" selected disabled>Choose Attach Form Type</option>
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

                                <div class="col-md-4">
                                    <div class="form-group d-flex" style="white-space: nowrap">

                                        <label for="filter_department_id">Departments: </label>
                                        <select name="filter_department_id" id="filter_department_id" class="form-control form-control-sm rounded-0 ml-2">
                                            <option value="" selected disabled>Choose Department</option>
                                            @foreach($departments as $department)
                                                <option value="{{$department['id']}}" {{ $department['id'] == old('filter_department_id') ? "selected" : "" }}>{{$department['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-3 d-flex justify-content-end">
                                    <button class="btn rounded-0 flex-fill mr-2 apply_btn" >
                                        <i class="fas fa-filter "></i> Apply
                                    </button>
                                    <button class="btn btn-danger rounded-0 flex-fill">
                                        <i class="fas fa-times"></i> Clear
                                    </button>
                                </div>

                                <div class="col-lg-12 my-2">
                                    <!-- Table -->
                                   <div class="table-responsive mt-3">
                                       <table id="assesseestable" class="table table-bordered">
                                           <thead class="table-dark">
                                               <tr>
                                                   <th><input type="checkbox" id="selectAll"></th>
                                                   <th>Assessee Code</th>
                                                   <th>Assessee</th>
                                                   <th>Department</th>
                                                   <th>Branch</th>
                                                   <th>Position Level</th>
                                                   <th>Position</th>
                                                   <th>Assessment-form Category</th>
                                               </tr>
                                           </thead>
                                           <tbody id="assesseeTable">

                                           </tbody>
                                       </table>
                                   </div>
                               </div>



                                <div class="col-md-12 mt-2">

                                    <button type="button" id="back-btn" class="btn btn-light btn-sm rounded-0" onclick="window.history.back();">Back</button>

                                    <button type="submit" class="btn btn-success btn-sm rounded-0">Save Selection</button>
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

        $("#filter_ass_form_cat_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Assessment Form Category',
            searchField: ["value", "label"]
        });

        $("#filter_branch_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Assessment Form Category',
            searchField: ["value", "label"]
        });

        $("#filter_department_id").selectize({
            plugins: ["restore_on_backspace", "remove_button"],
            delimiter: " - ",
            persist: true,
            maxItems: 1,
            valueField: "value",
            labelField: "label",
            placeholder: 'Choose Assessment Form Category',
            searchField: ["value", "label"]
        });

        $("#beginning_date,#enddate").flatpickr({
            dateFormat: "Y-m-d",
            {{-- minDate: "today", --}}
            {{-- maxDate: new Date().fp_incr(30) --}}
       });







        {{-- Start Assessee Filter --}}
        let selectedAssessees = {}; // Store selected users persistently
        let mergedAssessees = []; // Declare globally to avoid reference errors

        let tableBody = document.querySelector("#assesseestable tbody");

        $('.apply_btn').click(function (e) {
            e.preventDefault();
            $.ajax({
                url: `/getfilteredassessees`,
                type: "GET",
                dataType: "json",
                data: $('#peer_to_peer_form').serialize(),
                success: function (response) {
                    console.log(response);
                    renderTable(response);
                },
                error: function (response) {
                    console.log("Error:", response);
                }
            });
        });

        function renderTable(assesseeusers) {
            let html = '';

            // Update global variable
            mergedAssessees = [...assesseeusers];

            // Re-add previously selected users
            Object.keys(selectedAssessees).forEach(selectedID => {
                let alreadyInList = assesseeusers.some(user => user.id == selectedID);
                if (!alreadyInList) {
                    mergedAssessees.push(selectedAssessees[selectedID]);
                }
            });


            mergedAssessees.forEach(function (assesseeuser) {
                let assesseeID = assesseeuser.id;
                let isChecked = selectedAssessees[assesseeID] ? "checked" : "";
                let isDisabled = selectedAssessees[assesseeID] ? "" : "disabled";


                html += `
                <tr>
                    <td>


                        <input type="checkbox" name="asssessee_user_ids[]" class="assesseeCheckbox"
                            value="${assesseeID}" ${isChecked} data-id="${assesseeID}">
                        <input type="hidden" name="ass_form_cat_ids[]" class="ass_form_cat_ids"
                        value="${assesseeuser.assformcat.id}" ${isDisabled} data-id="${assesseeID}">
                    </td>
                    <td>${assesseeuser.employee.employee_code}</td>
                    <td>${assesseeuser.employee.employee_name}</td>
                    <td>${assesseeuser.employee.department.name}</td>
                    <td>${assesseeuser.employee.branch.branch_name}</td>
                    <td>${assesseeuser.employee.positionlevel.name}</td>
                    <td>${assesseeuser.employee.position.name}</td>
                    <td>${assesseeuser.assformcat.name}</td>
                </tr>`;
            });

            console.log(mergedAssessees);



            tableBody.innerHTML = html; // Replace content instead of appending

            // Rebind checkbox change event
            $(".assesseeCheckbox").change(function () {
                let assesseeID = $(this).data("id");

                if ($(this).is(":checked")) {
                    selectedAssessees[assesseeID] = mergedAssessees.find(u => u.id == assesseeID);
                    $(this).next('.ass_form_cat_ids').removeAttr("disabled");
                    {{-- console.log($(this).next('.ass_form_cat_ids')); --}}
                } else {
                    delete selectedAssessees[assesseeID];
                    $(this).next('.ass_form_cat_ids').attr("disabled", "disabled");
                }
                {{-- console.log(selectedAssessees); --}}
            });
        }

        // Select All Checkbox Handler
        document.getElementById("selectAll").addEventListener("change", function () {
            let checkboxes = document.querySelectorAll(".assesseeCheckbox");
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
                let assesseeID = cb.value;
                if (cb.checked) {
                    selectedAssessees[assesseeID] = mergedAssessees.find(u => u.id == assesseeID);
                    cb.next('.ass_form_cat_ids').removeAttr("disabled");

                } else {
                    delete selectedAssessees[assesseeID];
                    cb.next('.ass_form_cat_ids').attr("disabled", "disabled");

                }
            });
            {{-- console.log(selectedAssessees); --}}
        });

        {{-- End Assessee Filter --}}





    });
</script>

@stop
