@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid add-form-list">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="pull-left">
                            <h4>{{__('user.add_new_user')}}</h4>
                        </div>
                    </div>
                    @if ($errors->any())
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
                    <div class="card-body">
                        {!! Form::open(array('route' => 'users.store','method'=>'POST')) !!}
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>{{__('user.name')}}:</strong>
                                {!! Form::text('name', null, array('placeholder' => '','class' => 'form-control')) !!}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>{{__('user.emp_id')}}:</strong>
                                {!! Form::text('employee_id', null, array('placeholder' => "",'class' => 'form-control')) !!}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>{{__('user.branch')}}:</strong>
                                <select class="form-control" id="branch_name" name="branch_id[]" multiple required focus>
                                    @foreach($branches as $branch)
                                    <option value="{{$branch->branch_id}}">{{ $branch->branch_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>{{__('user.phone')}}:</strong>
                                {!! Form::text('ph_no', null, array('placeholder' => '','class' => 'form-control')) !!}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>{{__('user.email')}}:</strong>
                                {!! Form::text('email', null, array('placeholder' => '','class' => 'form-control')) !!}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>{{__('user.password')}}:</strong>
                                {!! Form::password('password', array('placeholder' => '','class' => 'form-control')) !!}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>{{__('user.confirm_password')}}:</strong>
                                {!! Form::password('confirm-password', array('placeholder' => '','class' => 'form-control')) !!}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>{{__('user.role')}}:</strong>
                                {!! Form::select('roles[]', $roles,[], array('class' => 'form-control')) !!}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 text-left">
                            <button type="submit" class="btn btn-primary mr-2">{{__('button.submit')}}</button>
                            <a class="btn btn-light" href="{{ route('users.index') }}"> {{__('button.back')}}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
    @endsection
    @section('js')
    <script type="text/javascript">
        $('#branch_name').select2({
            width: '100%',
            allowClear: true,
        });
        $(document).ready(function() {
            $('#branch_name').change(function() {
                var id = $(this).val();
                $('#dept_name').find('option').not(':first').remove();
                $.ajax({
                    url: '/users/create/' + id,
                    type: 'get',
                    dataType: 'json',
                    success: function(response) {
                        var len = 0;
                        if (response.data != null) {
                            len = response.data.length;
                        }
                        if (len > 0) {
                            for (var i = 0; i < len; i++) {
                                var id = response.data[i].id;
                                var name = response.data[i].name;
                                var option = "<option value='" + id + "'>" + name + "</option>";
                                $("#dept_name").append(option);
                            }
                        }
                    }
                })
            });
        });
    </script>
    @endsection