@extends('layouts.app')


@section('content')

<div class="content-page">
    <div class="container-fluid">
        <div class="row">

            <div class="col-lg-12 mx-auto">
                <div class="card" style="overflow: hidden">
                     <div class="header-section">
                        <h1 class="display-4 fw-bold mb-3">
                            <i class="fas fa-clipboard-check me-2"></i>
                            Assessment Portal
                        </h1>
                        <h3 class="mb-0 opacity-90">Welcome back, <b>{{Auth::user()->name}}</b>.Let's make this appraisal cycle impactful!</h3>
                    </div>
                    <!-- HAS TASKS STATE -->
                    <div id="activecycleinfo">



                    </div>
                </div>
            </div>

        </div>

        <div class="row mt-5">
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-book-open fa-2x text-primary mb-3"></i>
                        <h6>Assessment Guidelines</h6>
                        <p class="text-muted small">Best practices and evaluation criteria</p>
                        <button class="btn btn-outline-primary btn-sm" onclick="openGuidelines()">
                            View Guidelines
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-headset fa-2x text-success mb-3"></i>
                        <h6>Need Support?</h6>
                        <p class="text-muted small">Get help with the assessment process</p>
                        <button class="btn btn-outline-success btn-sm" onclick="contactSupport()">
                            Contact Support
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-comments fa-2x text-info mb-3"></i>
                        <h6>Assessor Community</h6>
                        <p class="text-muted small">Connect with fellow assessors</p>
                        <button class="btn btn-outline-info btn-sm" onclick="joinCommunity()">
                            Join Discussion
                        </button>
                    </div>
                </div>
            </div>
        </div>
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


@section('js')


<script>
	$.ajax({
		url: `/api/appraisalcyclesactivecycles`,
		method: 'GET',
		success:function(data){

            let activecycle = data.data;
            console.log(activecycle);

            let html = '';
            if(activecycle){
                html += `
                 <!-- Enhanced Appraisal Cycle Info Section -->
                        <div class="cycle-info-section">
                                <div class="cycle-info-card m-4">
                                    <div class="row align-items-center">
                                        <div class="col-lg-5 mb-3 mb-lg-0">
                                            <div class="cycle-header">
                                                <div class="cycle-status-badge">
                                                    <i class="fas fa-calendar-check me-2"></i>
                                                    ACTIVE CYCLE
                                                </div>
                                                <h4 class="cycle-title mb-1">${activecycle.name}</h4>
                                                {{-- <div class="cycle-phase">
                                                    <span class="phase-indicator">Phase 2</span>
                                                    <span class="phase-name">Assessor Review Period</span>
                                                </div> --}}
                                                <p class="cycle-period mb-0">
                                                    <i class="fas fa-clock me-1"></i>
                                                    Assessment Period: ${activecycle.action_start_date.slice(0, -6)} - ${activecycle.action_end_date}
                                                </p>

                                                <a href="{{ route('appraisalforms.notification') }}" class="btn btn-custom btn-primary-custom mt-4">
                                                    <i class="fas fa-play me-2"></i>
                                                    Start Assessments Now
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="cycle-metrics">
                                                <div class="row g-3">
                                                    <div class="col-md-4 mb-2">
                                                        <div class="date-info-card start-date">
                                                            <div class="date-icon">
                                                                <i class="fas fa-play-circle"></i>
                                                            </div>
                                                            <div class="date-content">
                                                                <h5 class="date-label">Cycle Start Date</h5>
                                                                <div class="date-value">${activecycle.action_start_date}</div>
                                                                <div class="date-time">12:00 AM</div>
                                                                <div class="date-status completed">
                                                                    <i class="fas fa-check-circle me-1"></i>
                                                                    Started Successfully
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="date-info-card end-date">
                                                            <div class="date-icon">
                                                                <i class="fas fa-flag-checkered"></i>
                                                            </div>
                                                            <div class="date-content">
                                                                <h5 class="date-label">Cycle End Date</h5>
                                                                <div class="date-value">${activecycle.action_end_date}</div>
                                                                <div class="date-time">12:00 PM</div>
                                                                <div class="date-status pending">
                                                                    <i class="fas fa-clock me-1"></i>
                                                                    19 Days Remaining
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mb-2">
                                                        <div class="date-info-card cycle-year">
                                                            <div class="date-icon">
                                                                <i class="fas fa-calendar-day"></i>
                                                            </div>
                                                            <div class="date-content">
                                                                <h5 class="date-label">Cycle Year</h5>
                                                                <div class="date-value">${activecycle.start_date.slice(0, -6)} - ${activecycle.end_date}</div>
                                                                <div class="date-time">360° Comprehensive Reviews</div>
                                                                <div class="date-status cycle">
                                                                    <i class="fas fa-check-circle me-1"></i>
                                                                   Full Year
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <!-- Timeline Section -->
                        <div class="timeline-section m-4">
                            <h4 class="mb-4">
                                <i class="fas fa-road me-2"></i>
                                Appraisal Cycle Timeline
                            </h4>

                            <div class="timeline-item">
                                <div class="timeline-icon completed">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Phase 1: Self-Assessment</h6>
                                    <small class="opacity-75">Completed - November 15, 2024</small>
                                </div>
                            </div>

                            <div class="timeline-item">
                                <div class="timeline-icon current">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Phase 2: Assessor Review</h6>
                                    <small class="opacity-75">In Progress - Due December 31, 2024</small>
                                </div>
                            </div>

                            <div class="timeline-item">
                                <div class="timeline-icon upcoming">
                                    <i class="fas fa-comments"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Phase 3: Feedback Sessions</h6>
                                    <small class="opacity-75">Starts January 5, 2025</small>
                                </div>
                            </div>

                            <div class="timeline-item">
                                <div class="timeline-icon upcoming">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Phase 4: Final Review</h6>
                                    <small class="opacity-75">Starts January 15, 2025</small>
                                </div>
                            </div>
                        </div>
                `;
            }else{
                html += `
                <section class="nothingtodo">

                    <div class="row">


                        <div class="col-md-6 image-container"
                            style="background-image: url('{{ asset('images/nothingtodobgcolorreduce.png') }}');
                                    background-position: center;
                                    background-repeat: no-repeat;
                                    object-fit:cover;">
                        </div>

                        <div class="col-md-6 text-center py-4">
                                <h1 class="main-title">Nothing To Do</h1>

                            <div class="decorative-line"></div>

                            <p class="subtitle-text">
                                You're completely free! No tasks are currently assigned to you.<br>
                                This is your time to breathe, relax, and enjoy the moment.
                            </p>

                            <div class="status-indicator">
                                All Tasks Completed Successfully
                            </div>
                        </div>
                    </div>

                    {{-- <div class="bg-opacity-50 rounded " style="background-color: rgba(0, 123, 255, 0.1);">
                        <h1 class="display-6">Furniture Collection</h1>
                        <p class="lead">Discover modern, stylish, and comfortable furniture for your home.</p>
                    </div> --}}
                </section>
                `;
            }
            $('#activecycleinfo').html(html);
		},
		error: function(){
			$('#usercount').text("Error loading data");
		}
	});
</script>


@endsection
