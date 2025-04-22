@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid add-form-list">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="pull-left">
                            <h4>{{ __('role.add_new_role')}}</h4>
                        </div>
                    </div>
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
                    <div class="card-body">
                        <form action="{{ route('roles.store') }}" method="POST" enctype="multipart/form-data" >
                            @csrf
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <strong>{{ __('role.name')}}:</strong>
                                        <input type="text" name="name" id="name" placeholder="Name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <strong>{{ __('role.profile_image')}} :</strong>
                                        <input type="file" name="profile_image" class="form-control image-file" accept="png">
                                    </div>
                                </div>



                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                    <strong>{{ __('role.user_permission')}}:</strong>
                                        <br />
                                        <table class="table">
                                            @foreach($user_permission->chunk(5) as $permissions)
                                                <tr>
                                                    @foreach( $permissions as $permission )
                                                        <td>
                                                            <input type="checkbox" name="permission[]" class="checkbox-input" value="{{$permission->id}}">
                                                            <strong>{{$permission->name}}</strong>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                    <strong>{{ __('role.role_permission')}}:</strong>
                                        <br />
                                        <table class="table">
                                            @foreach($role_permission->chunk(4) as $permissions)
                                                <tr>
                                                    @foreach( $permissions as $permission )
                                                        <td>
                                                            <input type="checkbox" name="permission[]" class="checkbox-input" value="{{$permission->id}}">
                                                            <strong>{{$permission->name}}</strong>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                    <strong>{{__('role.branch_permission')}} :</strong>
                                        <br />
                                        <table class="table">
                                            @foreach($branch_permission->chunk(4) as $permissions)
                                                <tr>
                                                    @foreach( $permissions as $permission )
                                                        <td>
                                                            <input type="checkbox" name="permission[]" class="checkbox-input" value="{{$permission->id}}">
                                                            <strong>{{$permission->name}}</strong>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>


                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                    <strong>{{__('role.fixed_analysis_permissions')}} :</strong>
                                        <br />
                                        <table class="table">
                                            @foreach($fixed_analysis_permissions->chunk(4) as $permissions)
                                                <tr>
                                                    @foreach( $permissions as $permission )
                                                        <td>
                                                            <input type="checkbox" name="permission[]" class="checkbox-input" value="{{$permission->id}}">
                                                            <strong>{{$permission->name}}</strong>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>

                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                    <strong>{{__('role.addon_permissions')}} :</strong>
                                        <br />
                                        <table class="table">
                                            @foreach($addon_permissions->chunk(4) as $permissions)
                                                <tr>
                                                    @foreach( $permissions as $permission )
                                                        <td>
                                                            <input type="checkbox" name="permission[]" class="checkbox-input" value="{{$permission->id}}">
                                                            <strong>{{$permission->name}}</strong>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12 text-left">
                                <button type="submit" class="btn btn-primary mr-2">{{__('button.submit')}}</button>
                                <a class="btn btn-light" href="{{ url()->previous() }}">{{__('button.back')}}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
