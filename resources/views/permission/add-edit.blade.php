@extends('layouts.app')

@section('page-title', trans('app.permissions'))

@section('content')

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            {{ $edit ? $permission->name : trans('app.create_new_permission') }}
            <small>{{ $edit ? trans('app.edit_permission_details') : trans('app.permission_details_sm') }}</small>
            <div class="pull-right">
                <ol class="breadcrumb">
                    <li><a href="{{ route('dashboard') }}">@lang('app.home')</a></li>
                    <li><a href="{{ route('permission.index') }}">@lang('app.permissions')</a></li>
                    <li class="active">{{ $edit ? trans('app.edit') : trans('app.create') }}</li>
                </ol>
            </div>
        </h1>
    </div>
</div>

@include('partials.messages')

@if ($edit)
    {!! Form::open(['route' => ['permission.update', $permission->id], 'method' => 'PUT', 'id' => 'permission-form']) !!}
@else
    {!! Form::open(['route' => 'permission.store', 'id' => 'permission-form']) !!}
@endif

<div class="row">
    <div class="col-lg-6 col-md-12 col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">@lang('app.permission_details')</div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="name">@lang('app.name')</label>
                    <input type="text" class="form-control" id="name"
                           name="name" placeholder="@lang('app.permission_name')" value="{{ $edit ? $permission->name : old('name') }}">
                </div>
                <div class="form-group">
                    <label for="display_name">@lang('app.display_name')</label>
                    <input type="text" class="form-control" id="display_name"
                           name="display_name" placeholder="@lang('app.display_name')" value="{{ $edit ? $permission->display_name : old('display_name') }}">
                </div>
                <div class="form-group">
                    <label for="description">@lang('app.description')</label>
                    <textarea name="description" id="description" class="form-control">{{ $edit ? $permission->description : old('description') }}</textarea>
                </div>
                </div>
            </div>
        </div>
    </div>

<div class="row">
    <div class="col-md-2">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i>
            {{ $edit ? trans('app.update_permission') : trans('app.create_permission') }}
        </button>
    </div>
</div>

@stop

@section('scripts')
    @if ($edit)
        {!! JsValidator::formRequest('Vanguard\Http\Requests\Permission\UpdatePermissionRequest', '#permission-form') !!}
    @else
        {!! JsValidator::formRequest('Vanguard\Http\Requests\Permission\CreatePermissionRequest', '#permission-form') !!}
    @endif
@stop