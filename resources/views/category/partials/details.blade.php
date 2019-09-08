<style>
    #pdt-description{

        min-height: 200px;

    }
</style>
<div class="panel panel-default">
    <div class="panel-heading">@lang('app.category_details')</div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="category">@lang('app.parent_category')</label>
                    <select class="form-control" name="parent" id="parent">
                        @foreach($parent_categories as $parent_category)
                            <option value="{{ $parent_category->id }}"
                                    {{ ($edit&&$category->parent_category_name==$parent_category->parent_category_name)? 'selected':"" }}>{{ $parent_category->parent_category_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="title">@lang('app.title')</label>
                    <input type="text" class="form-control" id="pdt-title"
                           name="title" placeholder="@lang('app.title')" value="{{ $edit ? $category->category_name : '' }}">
                </div>
                <div class="form-group">
                    <label for="title">@lang('app.keyword')</label>
                    <input type="text" class="form-control" id="pdt-title"
                           name="keyword" placeholder="@lang('app.keyword')" value="{{ $edit ? $category->keyword : '' }}">
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
