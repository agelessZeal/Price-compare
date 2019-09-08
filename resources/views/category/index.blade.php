@extends('layouts.app')

@section('page-title', trans('app.low_categories'))

@section('content')

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            {{ isset($user) ? $user->present()->nameOrEmail : trans('app.low_categories') }}
            <small>{{ isset($user) ? trans('app.list_of_categories') : trans('app.list_of_categories') }}</small>
            <div class="pull-right">
                <ol class="breadcrumb">
                    <li><a href="{{ route('dashboard') }}">@lang('app.home')</a></li>
                    @if (isset($user) && isset($adminView))
                        <li><a href="{{ route('category.index') }}">@lang('app.low_categories')</a></li>
                        <li class="active">{{ $user->present()->nameOrEmail }}</li>
                    @else
                        <li class="active">@lang('app.low_categories')</li>
                    @endif
                </ol>
            </div>

        </h1>
    </div>
</div>

@include('partials.messages')

<div class="row tab-search">
    <div class="col-md-2">
        <a href="{{ route('category.create') }}" class="btn btn-success" id="add-category">
            <i class="glyphicon glyphicon-plus"></i>
            @lang('app.add_category')
        </a>
    </div>
    <div class="col-md-5">

    </div>
    <form method="GET" action="" accept-charset="UTF-8" id="category-search-form">
        <div class="col-md-2">
            <select class="form-control" name="parent" id="parent-category">
                <option value="">@lang('app.all_parent_categories')</option>
                <?php
                $curParent = "";
                $searchParent = Input::get('parent');
                ?>
                @foreach($parent_categories as $parent_category)
                    <option value="{{ $parent_category->parent_category_name }}"
                            {{ ($searchParent==$parent_category->parent_category_name)? 'selected':"" }}>{{ $parent_category->parent_category_name }}</option>
                @endforeach
            </select>

        </div>
        <div class="col-md-3">
            <div class="input-group custom-search-form">
                <input type="text" class="form-control" name="search" value="{{ Input::get('search') }}"
                       placeholder="@lang('app.search_for_categories')">
                <span class="input-group-btn">
                <button class="btn btn-default" type="submit" id="search-users-btn">
                    <span class="glyphicon glyphicon-search"></span>
                </button>
                    @if (Input::has('search') && Input::get('search') != '')
                        <a href="{{ route('product.index') }}" class="btn btn-danger" type="button" >
                        <span class="glyphicon glyphicon-remove"></span>
                    </a>
                    @endif
            </span>
            </div>
        </div>
    </form>
</div>

<div class="table-responsive top-border-table">
    <table class="table">
        <thead>
        <th>@lang('app.title')</th>
        <th>@lang('app.keyword')</th>
        <th>@lang('app.parent_category')</th>
        <th>@lang('app.action')</th>
        </thead>
        <tbody>
        @if (count($category_list))
            @foreach($category_list as $category_item)
                <tr>
                    <td>{{$category_item->category_name}}</td>
                    <td>{{$category_item->keyword}}</td>
                    <td>{{$category_item->parent_category_name}}</td>
                    <td>
                        <a href="{{ route('category.edit', $category_item->category_id) }}"
                           class="btn btn-primary btn-circle edit" title="@lang('app.edit_category')"
                           data-toggle="tooltip" data-placement="top">
                            <i class="glyphicon glyphicon-edit"></i>
                        </a>
                        <a href="{{ route('category.delete', $category_item->category_id) }}"
                           class="btn btn-danger btn-circle" title="@lang('app.delete_category')"
                           data-toggle="tooltip"
                           data-placement="top"
                           data-method="DELETE"
                           data-confirm-title="@lang('app.please_confirm')"
                           data-confirm-text="@lang('app.are_you_sure_delete_category')"
                           data-confirm-delete="@lang('app.yes_delete_category')">
                            <i class="glyphicon glyphicon-trash"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="6"><em>@lang('app.no_records_found')</em></td>
            </tr>
        @endif
        </tbody>
    </table>
    {!! $category_list->render() !!}
</div>

@stop

@section('scripts')
<script>
    $("#parent-category").change(function () {
        $("#category-search-form").submit();
    });
</script>
@stop