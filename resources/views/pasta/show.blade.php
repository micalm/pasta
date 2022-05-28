@extends('layouts.app')

@section('body')
<input type="hidden" class="parentid-input" name="parent_id" value="{{ $pasta->uuid }}">
<textarea class="pasta" id="pasta" name="pasta">{{ $pasta->content }}</textarea>
@endsection
