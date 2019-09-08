<style>
    #pdt-description{

        min-height: 200px;

    }

</style>
<div class="panel panel-default">
    <div class="panel-heading">@lang('app.product_details')</div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="category">@lang('app.category')</label>
                    <select class="form-control" name="category" id="category">
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
                            if($searchCategory!=null&&$searchCategory==($category->category_name.'-'.$category->parent_category_name)){

                                echo '<option value="'.$category->category_name.'-'.$category->parent_category_name.'" selected>'.$category->category_name.'</option>';
                            }
                            echo '<option value="'.$category->category_name.'-'.$category->parent_category_name.'">'.$category->category_name.'</option>';
                        endforeach;
                        if($curParent!="") echo '</optgroup>';
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="title">@lang('app.title')</label>
                    <input type="text" class="form-control" id="pdt-title"
                           name="title" placeholder="@lang('app.title')" value="{{ $edit ? $product->pdt_title : '' }}">
                </div>
                <div class="form-group">
                    <label for="pdt-description">@lang('app.description')</label>
                    <textarea class="form-control" id="pdt-description"
                              name="description" placeholder="@lang('app.description_placeholder')" >{{ $edit ? $product->pdt_description : '' }}</textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="status">@lang('app.status')</label>
                    {!! Form::select('status', $statuses, $edit ? $product->pdt_status : '',
                        ['class' => 'form-control', 'id' => 'status']) !!}
                </div>
                <div class="form-group">
                    <label for="pdt-link">@lang('app.link')</label>
                    <input type="text" class="form-control" id="pdt-link"
                           name="link" placeholder="@lang('app.link_placeholder')" value="{{ $edit ? $product->pdt_link : '' }}">
                </div>
                <div class="form-group">
                    <label for="pdt-link">@lang('app.price')</label>
                    <input type="number" class="form-control" id="pdt-link"
                           name="price"  value="{{ $edit ? $product->pdt_price : '' }}">
                </div>
                <div class="form-group">
                    <label for="pdt-image">@lang('app.image')</label>
                    <input type="button" class="form-control" id="select-product-file" value="@lang('app.choose_image_file')">
                    @include('partials.cropperjs')
                </div>
            </div>
            @if ($edit)
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary" id="update-details-btn">
                        <i class="fa fa-refresh"></i>
                        @lang('app.update_details')
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
