@extends('layouts.app')

@section('page-title', trans('app.products'))

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                {{ isset($user) ? $user->present()->nameOrEmail : trans('app.products') }}
                <small>{{ isset($user) ? trans('app.list_of_products') : trans('app.list_of_products') }}</small>
                <div class="pull-right">
                    <ol class="breadcrumb">
                        <li><a href="{{ route('dashboard') }}">@lang('app.home')</a></li>
                        @if (isset($user) && isset($adminView))
                            <li><a href="{{ route('activity.index') }}">@lang('app.products')</a></li>
                            <li class="active">{{ $user->present()->nameOrEmail }}</li>
                        @else
                            <li class="active">@lang('app.products')</li>
                        @endif
                    </ol>
                </div>

            </h1>
        </div>
    </div>

    @include('partials.messages')

    <div class="row tab-search">
        <div class="col-md-2">
            <a href="{{ route('product.create') }}" class="btn btn-success" id="add-product">
                <i class="glyphicon glyphicon-plus"></i>
                @lang('app.add_product')
            </a>
        </div>
        <div class="col-md-5">
            <button class="btn btn-primary"
                    title="@lang('app.approve_all_tooltip')"
                    data-toggle="tooltip"
                    data-placement="top"
                    data-confirm-alert-title="@lang('app.please_approve_bulk_confirm')"
                    id="active-all-product">
                <i class="glyphicon glyphicon-refresh"></i>
                @lang('app.approve')
            </button>
            <button class="btn btn-danger"
                    title="@lang('app.remove_all_tooltip')"
                    data-toggle="tooltip"
                    data-placement="top"
                    data-confirm-alert-title="@lang('app.please_delete_bulk_confirm')"
                    id="delete-all-product">
                <i class="glyphicon glyphicon-remove"></i>
                @lang('app.remove')
            </button>
        </div>
        <form method="GET" action="" accept-charset="UTF-8" id="products-form">
            <div class="col-md-2">
                <select class="form-control" name="category" id="category">
                    <option value="">@lang('app.all_categories')</option>
                    <?php
                    $curParent = "";
                    $searchCategory = Input::get('category');
                    foreach ($categories as $category):
                        if($curParent!=$category->parent_category_name){
                            if($curParent!=''){
                                echo '</optgroup>';
                            }
                            echo '<optgroup label="'.$category->parent_category_name.'">';
                            $curParent = $category->parent_category_name;
                        }
                        if($searchCategory!=null&&$searchCategory==($category->category_name)){

                            echo '<option value="'.$category->category_name.'" selected>'.$category->category_name.'</option>';
                        }
                        echo '<option value="'.$category->category_name.'">'.$category->category_name.'</option>';
                    endforeach;
                    if($curParent!="") echo '</optgroup>';
                    ?>
                </select>
            </div>
            <div class="col-md-3">
                <div class="input-group custom-search-form">
                    <input type="text" class="form-control" name="search" value="{{ Input::get('search') }}"
                           placeholder="@lang('app.search_for_products')">
                    <span class="input-group-btn">
                <button class="btn btn-default" type="submit" id="search-users-btn">
                    <span class="glyphicon glyphicon-search"></span>
                </button>
                        @if (Input::has('search') && Input::get('search') != '')
                            <a href="{{ route('category.index') }}" class="btn btn-danger" type="button">
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
            <th>
                <div class="checkbox">
                    <input name="select-all" type="checkbox" id="select-all-product">
                    <label class="no-content"></label>
                </div>
            </th>
            <th>@lang('app.title')</th>
            <th>@lang('app.category')</th>
            <th>@lang('app.image')</th>
            <th>@lang('app.price')</th>
            <th>@lang('app.shop')</th>
            <th>@lang('app.status')</th>
            <th>@lang('app.action')</th>
            </thead>
            <tbody>
            @if (count($products))
                @foreach($products as $product)
                    <tr>
                        <td>
                            <div class="checkbox">
                                <input name="select-all" type="checkbox" class="sel-product-chk" data-product="{{$product->id}}-{{$product->pdt_title}}">
                                <label class="no-content"></label>
                            </div>
                        </td>
                        <td>{{$product->pdt_title}}</td>
                        <td>{{$product->pdt_category_name}}</td>
                        <td><a href="#"  {{ ($product->pdt_imgurl!="") ? "data-featherlight=".url($product->pdt_imgurl):""}}>@lang('app.view_product_image')</a></td>
                        <td>{{$product->pdt_price}} USD</td>
                        <td><a href="{{$product->pdt_link}}" target="_blank">@lang('app.go_to_shop')</a></td>
                        <td>
                            <span class="label label-{{ ($product->pdt_status=="Active")?"success":"warning" }}">{{ trans("app.{$product->pdt_status}") }} </span>
                        </td>
                        <td>
                            <a href="{{ route('product.edit', $product->id) }}"
                               class="btn btn-primary btn-circle edit" title="@lang('app.edit_product')"
                               data-toggle="tooltip" data-placement="top">
                                <i class="glyphicon glyphicon-edit"></i>
                            </a>
                            <a href="{{ route('product.delete', $product->id) }}"
                               class="btn btn-danger btn-circle" title="@lang('app.delete_product')"
                               data-toggle="tooltip"
                               data-placement="top"
                               data-method="DELETE"
                               data-confirm-title="@lang('app.please_confirm')"
                               data-confirm-text="@lang('app.are_you_sure_delete_product')"
                               data-confirm-delete="@lang('app.yes_delete_product')">
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
        {!! $products->render() !!}
    </div>

@stop

@section('scripts')
    <script>
        var activeBulkURL = '{{route('product.active.bulk')}}';
        var deleteBulkURL = '{{route('product.delete.bulk')}}';
        $("#category").change(function () {
            $("#products-form").submit();
        });
        $('#select-all-product').change(function () {

            if (this.checked) {
                $('.sel-product-chk').prop('checked', true);

            } else {
                $('.sel-product-chk').prop('checked', false);
            }

        });
        $('#active-all-product').click(function () {
            var selItems = getCheckedProducts();
            var alertTitle = $(this).data('confirm-alert-title')
            if (!confirm(alertTitle)) {
                return true;
            }
            $.ajax({
                url: activeBulkURL,
                type: 'post',
                dataType: 'json',
                data: {_token: tokenStr, pdt_infos: selItems},
                beforeSend: function () {
                    $('#active-all-product i').removeClass('glyphicon glyphicon-refresh');
                    $('#active-all-product i').addClass('fa fa-refresh fa-spin');
                    $('#active-all-product').prop("disabled", true);
                }
            }).done(function (res) {
                console.log(res.data);
                if (res.status == 'success') {
                    window.location.reload();
                }

            }).fail(function (res) {
                alert('Server Error');
            }).always(function (res) {
                $('#active-all-product i').removeClass('fa fa-refresh fa-spin');
                $('#active-all-product i').addClass('glyphicon glyphicon-refresh');
                $('#active-all-product').prop("disabled", false);
            });

        });
        $('#delete-all-product').click(function () {
            var selItems = getCheckedProducts();
            var alertTitle = $(this).data('confirm-alert-title');
            if (!confirm(alertTitle)) {
                return true;
            }
            $.ajax({
                url: deleteBulkURL,
                type: 'post',
                dataType: 'json',
                data: {_token: tokenStr, pdt_infos: selItems},
                beforeSend: function () {
                    $('#delete-all-product i').removeClass('glyphicon glyphicon-remove');
                    $('#delete-all-product i').addClass('fa fa-spinner fa-spin');
                    $('#delete-all-product').prop("disabled", true);
                }
            }).done(function (res) {
                console.log(res.data);
                if (res.status == 'success') {
                    window.location.reload();
                }

            }).fail(function (res) {
                alert('Server Error');
            }).always(function (res) {
                $('#delete-all-product i').removeClass('fa fa-spinner fa-spin');
                $('#delete-all-product i').addClass('glyphicon glyphicon-remove');
                $('#delete-all-product').prop("disabled", false);
            });
        });
        function getCheckedProducts() {

            var selProduct = [];
            $('.sel-product-chk').each(function () {
                if (this.checked) {
                    selProduct.push($(this).data('product'));
                }
            });
            return selProduct;

        }
    </script>
@stop