@extends('layouts.landing')

@section('title', 'Dashboard RH')

@section('content')
    {{-- Header --}}
    @include('components.headerDashboard')
    {{-- Sidebar --}}
    @include('components.sidebarDashboard')
    {{-- Zone dynamique --}}
    @include('components.sidebarContent.mainDashboard')
@endsection
