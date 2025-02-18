@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            {{-- <span id="testing">hello world</span> --}}

            {{-- @if (Auth::guard()->user()->can('view-dashboard-return-total') || Auth::guard()->user()->can('view-dashboard-return-finish') || Auth::guard()->user()->can('view-dashboard-return-pending') || Auth::guard()->user()->can('view-dashboard-exchange-total') || Auth::guard()->user()->can('view-dashboard-exchange-finish') || Auth::guard()->user()->can('view-dashboard-exchange-pending') || Auth::guard()->user()->can('view-dashboard-overdue-exchange-document') ) --}}
            <div class="col-lg-4">
                <div class="card card-transparent card-block card-stretch card-height border-none">
                    <div class="card-body p-0 mt-lg-2 mt-0">
                        <h3 class="mb-3">Hello , {{Auth::user()->name}}</h3>
                        <p class="mb-3"><strong>
                                @foreach ($branches as $branch)
                                {{ $branch->branches->branch_name }} <br>
                                @endforeach
                            </strong></p>
                    </div>
                </div>
            </div>
            {{-- @endif --}}




        <!-- Page end  -->
    </div>
</div>
{{-- <script>
    $(document).ready(function() {
        var $key = '';

        $(document).keypress(function(e) {
            if (e.key === 'Enter') {
                $('#testing').text($key);
                $key = '';
            } else {
                $key += e.key;
            }
            console.log($key);
        });
    });
</script> --}}
@endsection
