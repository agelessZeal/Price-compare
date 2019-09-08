@extends('layouts.frontend')

@section('page-title', trans('app.home'))
@section('product-title', 'Add New Product')
@section('product-description', '')
@section('product-keywords','')

@section('content')

<div class="add-product-section">
        <div class="container">
            {{-- This will simply include partials/messages.blade.php view here --}}
            @include('partials/messages')
            <form role="form" class="form-horizontal" method="POST"
                  action="{{route('product.store')}}"
                  autocomplete="off" id="product-add-form" enctype="multipart/form-data">
                <input type="hidden" value="<?= csrf_token() ?>" name="_token">
                <div class="form-group">
                    <label for="pdt-category" class="col-sm-2 control-label input-lg">@lang('app.category')</label>
                    <div class="col-sm-5">
                        <select class="form-control input-lg chosen" name="category" id="pdt-category">
                            <?php $curParent = "";
                                foreach ($categories as $key=>$category):
                                    if($curParent!=$category->parent_category_name){

                                            if($curParent!=''){
                                                echo '</optgroup>';
                                            }
                                            echo  '<optgroup label="'.$category->parent_category_name.'">';
                                            $curParent = $category->parent_category_name;
                                    }
                                    echo '<option value="'.$category->category_name.'-'.$category->parent_category_name.'">'.$category->category_name.'</option>';
                                endforeach;
                                if($curParent!="") echo '</optgroup>';
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="pdt-title" class="col-sm-2 control-label input-lg">@lang('app.title')</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control input-lg" name="title" id="pdt-title" placeholder="Product Title">
                    </div>
                </div>
                <div class="form-group">
                    <label for="pdt-description" class="col-sm-2 control-label input-lg">@lang('app.description')</label>
                    <div class="col-sm-10">
                        <textarea class="input-lg" id="pdt-description" name="description" placeholder="Product Description"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="pdt-title" class="col-sm-2 control-label input-lg">@lang('app.link')</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control input-lg" name="link" id="pdt-link" placeholder="http://example.com/product/">
                    </div>
                </div>
                <div class="form-group">
                    <label for="pdt-title" class="col-sm-2 control-label input-lg">@lang('app.price')</label>
                    <div class="col-sm-4">
                        <input type="number" class="form-control input-lg" name="price" id="pdt-price" min="0">
                    </div>
                    <label for="pdt-currency" class="col-sm-1 control-label input-lg">USD</label>
                </div>
                <div class="form-group">
                    <label for="pdt-image-btn" class="col-sm-2 control-label input-lg">@lang('app.add_picture')</label>
                    <div class="col-sm-4">
                        <input type="button" class="form-control" id="select-product-file" value="@lang('app.choose_image_file')">
                        @include('partials.cropperjs',[$edit=false])
                    </div>
                </div>
                <div class="form-group">
                    <label for="pdt-add-submit-btn" class="col-sm-2 control-label input-lg"></label>
                    <div class="col-sm-4">
                        <button type="submit" class="btn btn-primary input-lg" id="pdt-add-submit-btn" >@lang(('app.add_product'))</button>
                    </div>
                </div>
            </form>
            <hr class="split-line-add-pdt" style="display: none">
            <div class="form-horizontal" style="display: none">
                <div class="form-group">
                    <label for="pdt-bulk-upload" class="col-sm-2 control-label input-lg">@lang('app.shopify')</label>
                    <div class="col-sm-4">
                        <button class="btn btn-primary input-lg" id="pdt-bulk-upload" >@lang('app.upload_csv_file')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@section('scripts')
    <script type="text/javascript">
        $(".chosen").chosen();
    </script>
    {!! JsValidator::formRequest('Vanguard\Http\Requests\Product\AddProductRequest', '#product-add-form') !!}
@stop