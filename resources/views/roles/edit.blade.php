@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid add-form-list">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="pull-left">
                            <h4>{{__('role.edit_new_role')}}</h4>
                        </div>
                    </div>
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
                    <div class="card-body">
                        {!! Form::model($role, ['method' => 'PATCH','route' => ['roles.update', $role->id],'enctype'=>"multipart/form-data"]) !!}
                            @csrf
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <strong>{{ __('role.profile_image')}}:</strong><br>
                                        <img src="{{ asset('images/user/' . $role->name .'.png') }}" class="img-fluid rounded" style="width:100px" alt="user">
                                        <input type="file" name="profile_image" class="form-control image-file" accept="png">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <strong>{{ __('role.name')}}:</strong>
                                        <input type="text" name="name" id="" placeholder="Name" class="form-control" value="{{$role->name}}">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                    <strong>{{ __('role.dashboard_permission')}}:</strong>
                                        <br />
                                        <table class="table">
                                            @foreach($dashboard_permission->chunk(3) as $permissions)
                                                <tr>
                                                    @foreach( $permissions as $permission )
                                                        <td>
                                                            <input type="checkbox" name="permission[]" class="checkbox-input" value="{{$permission->id}}" @if($role->permissions->contains($permission)) checked @endif>
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
                                    <strong>{{ __('role.document_permission')}}:</strong>
                                        <br />
                                        <table class="table">
                                            @foreach($document_permission->chunk(4) as $permissions)
                                                <tr>
                                                    @foreach( $permissions as $permission )
                                                        <td>
                                                            <input type="checkbox" name="permission[]" class="checkbox-input" value="{{$permission->id}}" @if($role->permissions->contains($permission)) checked @endif>
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
                                    <strong>{{ __('role.document_remark_permission')}}:</strong>
                                        <br />
                                        <table class="table">
                                            @foreach($document_remark->chunk(4) as $permissions)
                                                <tr>
                                                    @foreach( $permissions as $permission )
                                                        <td>
                                                            <input type="checkbox" name="permission[]" class="checkbox-input" value="{{$permission->id}}" @if($role->permissions->contains($permission)) checked @endif>
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
                                    <strong>{{ __('role.document_attach_file_permission')}}:</strong>
                                        <br />
                                        <table class="table">
                                            @foreach($document_attach->chunk(4) as $permissions)
                                                <tr>
                                                    @foreach( $permissions as $permission )
                                                        <td>
                                                            <input type="checkbox" name="permission[]" class="checkbox-input" value="{{$permission->id}}" @if($role->permissions->contains($permission)) checked @endif>
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
                                    <strong>{{ __('role.document_export_permission')}}:</strong>
                                        <br />
                                        <table class="table">
                                            @foreach($document_export->chunk(4) as $permissions)
                                                <tr>
                                                    @foreach( $permissions as $permission )
                                                        <td>
                                                            <input type="checkbox" name="permission[]" class="checkbox-input" value="{{$permission->id}}" @if($role->permissions->contains($permission)) checked @endif>
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
                                    <strong>{{ __('role.document_update_status_permission')}}:</strong>
                                        <br />
                                        <table class="table">
                                            @foreach($document_update->chunk(4) as $permissions)
                                                <tr>
                                                    @foreach( $permissions as $permission )
                                                        <td>
                                                            <input type="checkbox" name="permission[]" class="checkbox-input" value="{{$permission->id}}" @if($role->permissions->contains($permission)) checked @endif>
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
                                    <strong>{{ __('role.product_permission')}}:</strong>
                                        <br />
                                        <table class="table">
                                            @foreach($product_permission->chunk(4) as $permissions)
                                                <tr>
                                                    @foreach( $permissions as $permission )
                                                        <td>
                                                            <input type="checkbox" name="permission[]" class="checkbox-input" value="{{$permission->id}}" @if($role->permissions->contains($permission)) checked @endif>
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
                                    <strong>{{ __('role.my_document_permission')}}:</strong>
                                        <br />
                                        <table class="table">
                                            @foreach($my_document->chunk(4) as $permissions)
                                                <tr>
                                                    @foreach( $permissions as $permission )
                                                        <td>
                                                            <input type="checkbox" name="permission[]" class="checkbox-input" value="{{$permission->id}}" @if($role->permissions->contains($permission)) checked @endif>
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
                                    <strong>{{ __('role.user_permission')}}:</strong>
                                        <br />
                                        <table class="table">
                                            @foreach($user_permission->chunk(5) as $permissions)
                                                <tr>
                                                    @foreach( $permissions as $permission )
                                                        <td>
                                                            <input type="checkbox" name="permission[]" class="checkbox-input" value="{{$permission->id}}" @if($role->permissions->contains($permission)) checked @endif>
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
                                    <strong>{{ __('role.user_permission')}}:</strong>
                                        <br />
                                        <table class="table">
                                            @foreach($role_permission->chunk(4) as $permissions)
                                                <tr>
                                                    @foreach( $permissions as $permission )
                                                        <td>
                                                            <input type="checkbox" name="permission[]" class="checkbox-input" value="{{$permission->id}}" @if($role->permissions->contains($permission)) checked @endif>
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
                                    <strong>{{ __('role.supplier_permission')}}:</strong>
                                        <br />
                                        <table class="table">
                                            @foreach($supplier_permission->chunk(4) as $permissions)
                                                <tr>
                                                    @foreach( $permissions as $permission )
                                                        <td>
                                                            <input type="checkbox" name="permission[]" class="checkbox-input" value="{{$permission->id}}" @if($role->permissions->contains($permission)) checked @endif>
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
                                    <strong>{{__('role.branch_permission')}}:</strong>
                                        <br />
                                        <table class="table">
                                            @foreach($branch_permission->chunk(4) as $permissions)
                                                <tr>
                                                    @foreach( $permissions as $permission )
                                                        <td>
                                                            <input type="checkbox" name="permission[]" class="checkbox-input" value="{{$permission->id}}" @if($role->permissions->contains($permission)) checked @endif>
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
                                    <strong>{{__('role.faqs_permission')}}:</strong>
                                        <br />
                                        <table class="table">
                                            @foreach($faqs_permissions->chunk(4) as $permissions)
                                                <tr>
                                                    @foreach( $permissions as $permission )
                                                        <td>
                                                            <input type="checkbox" name="permission[]" class="checkbox-input" value="{{$permission->id}}" @if($role->permissions->contains($permission)) checked @endif>
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
                                <button type="submit" class="btn btn-primary mr-2">{{__('button.update')}}</button>
                                <a class="btn btn-light" href="{{ route('roles.index') }}">{{__('button.back')}}</a>
                            </div>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               
                        {!! Form::close() !!}
                    </div>
                </div> 
            </div>
        </div>
    </div>
</div>
@endsection