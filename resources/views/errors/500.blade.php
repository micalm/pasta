@extends('layouts.error')

@section('title', __('Server Error'))
@section('code', '500')
@section('message', __('Server Error'))
@section('description')
    Something unexpected happened.<br>Try again in a few minutes.
@endsection
