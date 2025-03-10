@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex flex-wrap flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Performance Appraisal Assessment</h4>
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

           <div class="col-md-12 mb-2">
                <div class="row">


                    <div class="col-md-12">
                        <div class="header-bar mb-0">{{ $appraisalform->assformcat->name }}</div>

                        <form action="{{ route('appraisalforms.update',$appraisalform->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                        <div class="table-responsive">
                            <table class="table table-bordered my-0">
                                <thead class="appraisal_headers">
                                    <tr class="text-white">
                                        <th colspan="2">
                                            <label for="">Assessor Name:</label>
                                            <span class="ml-4">{{ $appraisalform->assessoruser->employee->employee_name }}</span>
                                        </th>

                                        <th class="text-center" colspan="7">
                                            <label for="">Employee Code: </label>
                                            <span class="ml-4">{{ $appraisalform->assessoruser->employee->employee_code }}</span>
                                        </th>
                                    </tr>


                                    <tr class="text-white">
                                        <th colspan="2">
                                                <label for="">Position: </label>
                                                <span class="ml-4">{{ $appraisalform->assessoruser->employee->position->name }}</span>
                                        </th>
                                        <th class="text-center" colspan="7" rowspan="2">
                                            <label for="">Assessee: </label>
                                        </th>
                                    </tr>

                                    <tr>
                                        <th colspan="2">
                                            <label for="">Department: </label>
                                            <span class="ml-4">{{ $appraisalform->assessoruser->employee->department->name }}</span>
                                        </th>
                                    </tr>
                                </thead>
                            </table>

                            <table id="mytable" class="table table-bordered custables">

                                <thead class=" m-0">


                                    <tr class="table_headers">
                                        <th>S/No</th>
                                        <th>CRITERIA Description</th>
                                        <th>Excellent</th>
                                        <th>Good</th>
                                        <th>Meet Standard</th>
                                        <th>Below Standard</th>
                                        <th>Weak</th>
                                        @foreach($assesseeusers as $assesseeuser)
                                        <th style="width:auto;">{{ $assesseeuser->employee->employee_name }}</th>
                                        @endforeach
                                    </tr>

                                </thead>
                                <tbody class="">

                                    @foreach ($criterias as $idx=>$criteria)
                                    <tr class="table_rows">
                                        <td>{{ ++$idx }}</td>
                                        <td>{{ $criteria->name }}</td>
                                        <td >{{$criteria->excellent}}</td>
                                        <td >{{$criteria->good}}</td>
                                        <td >{{$criteria->meet_standard}}</td>
                                        <td >{{$criteria->below_standard}}</td>
                                        <td >{{$criteria->weak}}</td>
                                        @foreach($assesseeusers as $assesseeuser)
                                            {{-- {{ dd($criteria->pluck('excellent')) }} --}}
                                            {{-- {{ dd($criteria->getRatingScaleAttribute()) }} --}}
                                            <td style="width:auto;">
                                                <input type="number" name="appraisalformresults[{{$assesseeuser->id}}][{{ $criteria->id }}]" class="custom-input" max="{{ $criteria->excellent }}" min="{{ $criteria->weak }}" value="{{ old('appraisalformresults') ? old('appraisalformresults')[$assesseeuser->id][$criteria->id] : '' }}" data-valids="{{ implode(',', $criteria->getRatingScaleAttribute()) }}"/>
                                            </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>


                            <div class="col-md-12 mt-2">

                                <button type="button" id="back-btn" class="btn btn-light btn-sm rounded-0" onclick="window.history.back();">Back</button>

                                <button type="submit" class="btn btn-success btn-sm rounded-0">Submit</button>
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



    <!-- start edit modal -->

      <!-- end edit modal -->

<!-- End MODAL AREA -->
@endsection
@section('js')
<script>
    $(document).ready(function() {


        {{-- Your rating-value doesn't match the given-rating-scale-values! --}}
        {{-- $(".custom-input").on("input", function () {
            let min = parseInt($(this).attr("min"));
            let max = parseInt($(this).attr("max"));
            let value = parseInt($(this).val());

            if (value > max) {

                Swal.fire({
                    icon: "warning",
                    title: "Greater than maximum value.",
                    text:"Value cannot be greater than " + max,
                });
                $(this).val(max);
            } else if (value < min) {
                Swal.fire({
                    icon: "warning",
                    title: "Less than minimum value.",
                    text: "Value cannot be less than " + min,
                });
                $(this).val(min);
            }
        }); --}}


        document.querySelectorAll('.custom-input').forEach(function (input) {
            input.addEventListener('input', function () {
                const allowed = this.dataset.valids.split(',').map(Number);
                const value = parseInt(this.value);

                if (this.value !== '' && !allowed.includes(value)) {
                    {{-- alert('Invalid value! Please enter one of: ' + allowed.join(', ')); --}}

                    Swal.fire({
                        icon: "warning",
                        title: "Your rating-value doesn't match the given-rating-scale-values!",
                        text:"Please enter one of: " + allowed.join(', '),
                    });
                    this.value = ''; // Clear invalid input
                }
            });
        });
    });
</script>
@stop
