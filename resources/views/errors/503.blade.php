@extends('layouts.error')

@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message', __('Service Unavailable'))
@section('description')
    We're down for maintenance.<br>Try again in a few minutes.
@endsection
