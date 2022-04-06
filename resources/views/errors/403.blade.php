@extends('layouts.error')

@section('title', __('Forbidden'))
@section('code', '403')
@section('message', __('Forbidden'))
@section('description')
    You don't have access to this part of the site.
@endsection
