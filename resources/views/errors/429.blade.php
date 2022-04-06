@extends('layouts.error')

@section('title', __('Too Many Requests'))
@section('code', '429')
@section('message', __('Too Many Requests'))
@section('description')
    You're doing that too often.<br>Wait for a few minutes and try again.
@endsection
