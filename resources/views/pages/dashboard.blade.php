@extends('layouts.landing')

@section('title', 'Dashboard RH')

@section('content')
    {{-- Header --}}
    @include('components.headerDashboard')
    {{-- Sidebar --}}
    @include('components.sidebarDashboard')
    {{-- Zone dynamique --}}
    <div id="dashboardContent">
        <p class="text-muted">Chargement du tableau de bord...</p>
    </div>
@endsection
