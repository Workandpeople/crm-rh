@extends('layouts.landing')

@section('title', 'Dashboard RH')

@section('content')
    {{-- Header --}}
    @include('partials.dashboard.header')
    {{-- Sidebar --}}
    @include('partials.dashboard.sidebar')
    {{-- Zone dynamique --}}
    <div id="dashboardContent">
        <h2 class="fw-bold" style="color: var(--color-primary);">Tableau de bord principal</h2>
        <p class="">Bienvenue dans votre tableau de bord RH.</p>
    </div>
@endsection
