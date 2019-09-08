@extends('layouts.app')

@section('page-title', trans('app.edit_category'))

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                {{ $category->category_name }}
                <small>@lang('app.edit_category_details')</small>
                <div class="pull-right">
                    <ol class="breadcrumb">
                        <li><a href="{{ route('dashboard') }}">@lang('app.home')</a></li>
                        <li><a href="{{ route('category.index') }}">@lang('app.low_categories')</a></li>
                        <li class="active">@lang('app.edit')</li>
                    </ol>
                </div>
            </h1>
        </div>
    </div>

    @include('partials.messages')

    {!! Form::open(['route' => ['category.update.detail',$category->id], 'method' => 'PUT','files' => true, 'id' => 'category-edit-form']) !!}
    <div class="row">
        <div class="col-md-8">
            @include('category.partials.details', ['edit' => $edit])
        </div>
    </div>
    {!! Form::close() !!}

@stop

@section('styles')
    {!! HTML::style('assets/css/bootstrap-datetimepicker.min.css') !!}
@stop

@section('scripts')
    {!! HTML::script('assets/js/moment.min.js') !!}
    {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}
    {!! JsValidator::formRequest('Vanguard\Http\Requests\Category\EditCategoryRequest', '#category-edit-form') !!}
@stop