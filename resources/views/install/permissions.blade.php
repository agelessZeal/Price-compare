@extends('layouts.install')

@section('content')

    @include('install.steps', ['steps' => [
        'welcome' => 'selected done',
        'requirements' => 'selected done',
        'permissions' => 'selected'
    ]])

    <div class="step-content">
        <h3>Permissions</h3>
        <hr>
        <ul class="list-group">
            @foreach($folders as $path => $isWritable)
                <li class="list-group-item">
                    {{ $path }}
                    @if ($isWritable)
                        <span class="label label-default pull-right">775</span>
                        <span class="badge badge-success"><i class="fa fa-check"></i></span>
                    @else
                        <span class="label label-default pull-right">775</span>
                        <span class="badge badge-danger"><i class="fa fa-times"></i></span>
                    @endif
                </li>
            @endforeach
        </ul>
        <a class="btn btn-green pull-right" href="{{ route('install.database') }}">
            Next
            <i class="fa fa-arrow-right"></i>
        </a>
        <div class="clearfix"></div>
    </div>

@stop