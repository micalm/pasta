@extends('layouts.app')

@section('body')
<textarea class="pasta" id="pasta" name="pasta">{{ $pasta->content }}</textarea>
@endsection
