@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="batch-hero">
            <div>
                <a href="{{ route('appraisalcycles.edit', $appraisalcycle->id) }}" class="back-link">
                    <i class="fas fa-arrow-left"></i> Back to Appraisal Cycle
                </a>
                <h3>Batch Send Appraisal Forms</h3>
                <p>{{ $appraisalcycle->name }} · Search assessors, review their assignments, then send once.</p>
            </div>
            <div class="hero-icon"><i class="far fa-paper-plane"></i></div>
        </div>

        @if(session('success'))
            <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger shadow-sm">{{ $errors->first() }}</div>
        @endif

        <div class="filter-card">
            <div class="filter-title">
                <span><i class="fas fa-filter"></i></span>
                <div>
                    <h5>Search Assessors</h5>
                    <small>Only the search results shown below can be selected and sent.</small>
                </div>
            </div>
            <form method="GET" action="{{ route('appraisalcycles.batchsend', $appraisalcycle->id) }}">
                <div class="row align-items-end">
                    <div class="col-lg-5 col-md-6">
                        <div class="form-group">
                            <label>Employee Code / Name</label>
                            <div class="input-icon">
                                <i class="fas fa-search"></i>
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search assessor...">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <label>Branch</label>
                            <select name="branch_id" class="form-control">
                                <option value="">All Branches</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->branch_id }}" {{ (string) request('branch_id') === (string) $branch->branch_id ? 'selected' : '' }}>
                                        {{ $branch->branch_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <label>Criteria Set</label>
                            <select name="ass_form_cat_id" class="form-control">
                                <option value="">All Criteria Sets</option>
                                @foreach($assformcats as $assformcat)
                                    <option value="{{ $assformcat->id }}" {{ (string) request('ass_form_cat_id') === (string) $assformcat->id ? 'selected' : '' }}>
                                        {{ $assformcat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="form-group d-flex">
                            <button class="btn btn-primary flex-fill mr-2"><i class="fas fa-search mr-1"></i> Search</button>
                            <a href="{{ route('appraisalcycles.batchsend', $appraisalcycle->id) }}" class="btn btn-light" title="Reset"><i class="fas fa-undo"></i></a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <form id="batch-send-form" method="POST" action="{{ route('appraisalcycles.batchsend.store', $appraisalcycle->id) }}">
            @csrf
            <div class="result-toolbar">
                <div>
                    <h5>Form Assignments</h5>
                    <span>{{ $assignments->count() }} form(s) found</span>
                </div>
                <label class="select-all-label">
                    <input type="checkbox" id="select-all"> Select all search results
                </label>
            </div>

            <div class="assignment-grid">
                @forelse($assignments as $assignmentKey => $peers)
                    @php
                        $first = $peers->first();
                        $assessor = $first->assessoruser;
                    @endphp
                    <label class="assignment-card">
                        <input type="checkbox" class="assignment-check" name="assignments[]" value="{{ $assignmentKey }}">
                        <span class="custom-check"><i class="fas fa-check"></i></span>
                        <div class="assignment-head">
                            <div class="avatar">{{ strtoupper(substr(optional($assessor->employee)->employee_name ?? $assessor->name, 0, 1)) }}</div>
                            <div>
                                <h6>{{ optional($assessor->employee)->employee_name ?? $assessor->name }}</h6>
                                <small>{{ optional($assessor->employee)->employee_code ?? 'No employee code' }}</small>
                            </div>
                        </div>
                        <div class="info-row">
                            <span>Appraisal</span>
                            <strong>{{ $appraisalcycle->name }}</strong>
                        </div>
                        <div class="info-row">
                            <span>Criteria Set</span>
                            <strong>{{ optional($first->assformcat)->name ?? 'N/A' }}</strong>
                        </div>
                        <div class="info-row">
                            <span>Branch</span>
                            <strong>{{ optional(optional($assessor->employee)->branch)->branch_name ?? 'N/A' }}</strong>
                        </div>
                        <div class="assessee-block">
                            <span><i class="fas fa-users mr-1"></i> Assessees ({{ $peers->unique('assessee_user_id')->count() }})</span>
                            <p>
                                {{ $peers->unique('assessee_user_id')->map(function($peer) {
                                    return optional(optional($peer->assesseeuser)->employee)->employee_name
                                        ?? optional($peer->assesseeuser)->name
                                        ?? 'N/A';
                                })->implode(', ') }}
                            </p>
                        </div>
                    </label>
                @empty
                    <div class="empty-state">
                        <i class="far fa-folder-open"></i>
                        <h5>No form assignments found</h5>
                        <p>Change the search filters and try again.</p>
                    </div>
                @endforelse
            </div>

            @if($assignments->isNotEmpty())
                <div class="send-bar">
                    <div><strong id="selected-count">0</strong> form(s) selected</div>
                    <button type="button" id="send-selected" class="btn">
                        <i class="far fa-paper-plane mr-2"></i>Send Selected Forms
                    </button>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection

@section('css')
<style>
    .batch-hero { display:flex; justify-content:space-between; align-items:center; padding:28px; margin-bottom:20px; border-radius:18px; color:#fff; background:linear-gradient(135deg,#2458c6,#3978f6 60%,#4b8df8); box-shadow:0 12px 28px rgba(36,88,198,.22); }
    .batch-hero h3 { margin:10px 0 6px; font-weight:700; }
    .batch-hero p { margin:0; color:rgba(255,255,255,.82); }
    .back-link { color:#fff; opacity:.9; }
    .hero-icon { width:72px; height:72px; border-radius:20px; line-height:72px; text-align:center; font-size:30px; background:rgba(255,255,255,.15); }
    .filter-card { padding:22px 22px 6px; margin-bottom:22px; border:1px solid #e5ebf4; border-radius:16px; background:#fff; box-shadow:0 8px 24px rgba(31,55,90,.07); }
    .filter-title { display:flex; gap:12px; align-items:center; margin-bottom:18px; }
    .filter-title > span { width:40px; height:40px; border-radius:11px; line-height:40px; text-align:center; color:#3978f6; background:#edf4ff; }
    .filter-title h5 { margin:0; font-weight:700; color:#172b4d; }
    .filter-title small { color:#7a889e; }
    .filter-card label { color:#52647f; font-size:12px; font-weight:600; }
    .filter-card .form-control { height:40px; border-color:#dce4ef; border-radius:9px; }
    .input-icon { position:relative; }
    .input-icon i { position:absolute; left:13px; top:13px; color:#98a6ba; }
    .input-icon input { padding-left:38px; }
    .result-toolbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; }
    .result-toolbar h5 { margin:0; color:#172b4d; font-weight:700; }
    .result-toolbar span { color:#7a889e; font-size:12px; }
    .select-all-label { padding:9px 13px; border:1px solid #dce4ef; border-radius:9px; background:#fff; cursor:pointer; }
    .assignment-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:16px; padding-bottom:90px; }
    .assignment-card { position:relative; padding:18px; margin:0; border:1px solid #e1e7f0; border-radius:14px; background:#fff; box-shadow:0 5px 16px rgba(31,55,90,.05); cursor:pointer; transition:.2s; }
    .assignment-card:hover { transform:translateY(-2px); border-color:#9fbcf5; box-shadow:0 10px 22px rgba(31,55,90,.10); }
    .assignment-card:has(.assignment-check:checked) { border-color:#3978f6; background:#f8fbff; box-shadow:0 0 0 2px rgba(57,120,246,.10); }
    .assignment-check { position:absolute; opacity:0; }
    .custom-check { position:absolute; top:16px; right:16px; width:23px; height:23px; border:2px solid #cbd5e1; border-radius:7px; color:transparent; text-align:center; line-height:19px; }
    .assignment-check:checked + .custom-check { color:#fff; border-color:#3978f6; background:#3978f6; }
    .assignment-head { display:flex; align-items:center; gap:11px; padding-right:32px; margin-bottom:16px; }
    .avatar { width:43px; height:43px; border-radius:12px; line-height:43px; text-align:center; font-weight:700; color:#fff; background:linear-gradient(135deg,#3978f6,#6c5ce7); }
    .assignment-head h6 { margin:0 0 3px; font-weight:700; color:#243b5a; }
    .assignment-head small { color:#8795aa; }
    .info-row { display:flex; justify-content:space-between; gap:10px; padding:8px 0; border-top:1px solid #f0f3f8; font-size:12px; }
    .info-row span { color:#8795aa; }
    .info-row strong { color:#354966; text-align:right; }
    .assessee-block { padding:11px; margin-top:9px; border-radius:10px; background:#f4f7fb; }
    .assessee-block span { color:#52709d; font-size:12px; font-weight:700; }
    .assessee-block p { margin:5px 0 0; color:#63748c; font-size:12px; line-height:1.55; }
    .send-bar { position:fixed; right:30px; bottom:20px; z-index:50; display:flex; align-items:center; gap:30px; padding:13px 16px 13px 22px; border:1px solid #dfe6f0; border-radius:14px; background:#fff; box-shadow:0 12px 32px rgba(31,55,90,.18); }
    .send-bar .btn { padding:10px 18px; border-radius:9px; color:#fff; background:linear-gradient(135deg,#24a66a,#16834f); }
    .empty-state { grid-column:1/-1; padding:60px; border:1px dashed #ccd6e5; border-radius:15px; text-align:center; color:#8290a4; background:#fff; }
    .empty-state i { font-size:42px; margin-bottom:12px; }
    @media(max-width:991px){ .assignment-grid{grid-template-columns:repeat(2,minmax(0,1fr));} }
    @media(max-width:767px){ .batch-hero{padding:20px}.hero-icon{display:none}.assignment-grid{grid-template-columns:1fr}.result-toolbar{align-items:flex-start;gap:10px;flex-direction:column}.send-bar{left:15px;right:15px;bottom:10px;justify-content:space-between}.select-all-label{width:100%} }
</style>
@endsection

@section('js')
<script>
$(function () {
    function updateSelectedCount() {
        $('#selected-count').text($('.assignment-check:checked').length);
        $('#select-all').prop(
            'checked',
            $('.assignment-check').length > 0 && $('.assignment-check:checked').length === $('.assignment-check').length
        );
    }

    $('#select-all').on('change', function () {
        $('.assignment-check').prop('checked', this.checked);
        updateSelectedCount();
    });
    $('.assignment-check').on('change', updateSelectedCount);

    $('#send-selected').on('click', function () {
        const count = $('.assignment-check:checked').length;
        if (!count) {
            Swal.fire('Select forms', 'Please select at least one form assignment.', 'info');
            return;
        }

        Swal.fire({
            title: `Send ${count} selected form(s)?`,
            text: 'Existing forms will be skipped to prevent duplicate sending.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, send forms'
        }).then(function (result) {
            if (result.isConfirmed) {
                $('#batch-send-form').submit();
            }
        });
    });
});
</script>
@stop
