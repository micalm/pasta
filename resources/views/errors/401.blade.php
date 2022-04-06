@extends('layouts.error')

@section('title', __('Unauthorized'))
@section('code', '401')
@section('message', __('Unauthorized'))
@section('description')
    You don't have access to this part of the site.<br>Sign in and try again.
@endsection
