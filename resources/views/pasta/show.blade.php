@extends('layouts.app')

@section('body')
<div class="pasta-show">
    <div class="pasta-container">
        <input type="hidden" class="parentid-input" name="parent_id" value="{{ $pasta->uuid }}">
        <textarea class="pasta" id="pasta" name="pasta">{{ $pasta->content }}</textarea>
    </div>
    <div class="pasta-info">
        @include('app.pastainfo')
    </div>
</div>
@endsection
