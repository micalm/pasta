@extends('layouts.app')

@section('body')
<div class="pasta-show">
    <div class="pasta-container">
        <textarea class="pasta" id="pasta" name="pasta">{{ $pasta->content }}</textarea>
    </div>
    <div class="pasta-info">
        @include('app.pastainfo')
    </div>
</div>
@endsection
