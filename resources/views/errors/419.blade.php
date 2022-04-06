@extends('layouts.error')

@section('title', __('Page Expired'))
@section('code', '419')
@section('message', __('Page Expired'))
@section('description')
    Your session expired.<br>Please try again.
@endsection
