@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid add-form-list">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Add New FAQ</h4>
                        </div>
                    </div>

                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="card-body">
                        <form action="{{ route('faqs.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <strong> English Name:</strong>
                                        <input type="text" name="name_eng" class="form-control"
                                            rows="3">{{old('name_eng')}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <strong> Myanmar Name:</strong>
                                        <input type="text" name="name_mm" class="form-control"
                                            rows="3">{{old('name_eng')}}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <strong> English Description:</strong>
                                        <textarea name="description_eng" class="form-control"
                                            rows="3">{{old('description_eng')}}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <strong>Myanmar Description:</strong>
                                        <textarea name="description_mm" class="form-control"
                                            rows="3">{{old('description_mm')}}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <strong> English Question :</strong>
                                        <textarea name="question_eng" class="form-control"
                                            rows="3">{{old('question_eng')}}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <strong>Myanmar Question:</strong>
                                        <textarea name="question_mm" class="form-control"
                                            rows="3">{{old('question_mm')}}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <strong> English Answer:</strong>
                                        <textarea name="answer_eng" class="form-control"
                                            rows="3">{{old('answer_eng')}}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <strong>Myanmar Answer:</strong>
                                        <textarea name="answer_mm" class="form-control"
                                            rows="3">{{old('answer_mm')}}</textarea>
                                    </div>
                                </div>
                                <div class="pull-right col-xs-12 col-sm-12 col-md-12">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <a class="btn btn-light" href="{{ route('faqs.index') }}"> Back</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
