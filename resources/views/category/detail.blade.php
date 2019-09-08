@extends('layouts.frontend')

@section('page-title', trans('app.home'))

@section('content')
    <div class="container">
        <div class="row">
            @foreach ($parentCategories as $key=>$categorySet)
                <div class="col-md-12">
                    <div class="category-details-item">
                        <h4 class="category-item-title">
                            <svg-icon><src href="{{ url('assets/img/svg/'.$categorySet[0]['svg'].'.svg')}}"/></svg-icon>
                            {{ trim($categorySet[0]['parent'])}}
                        </h4>
                        <div class="sub-category-items">
                            @foreach($categorySet as $index=>$item)
                                <div class="category-sub-item" data-keyword="{{ trim($item['sub_keyword']) }}">
                                    {{ trim($item['sub']) }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@stop

@section('scripts')

@stop