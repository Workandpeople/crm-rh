<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="@yield('description', 'Page d’accueil générique pour votre projet.')" />
    <meta name="keywords" content="@yield('keywords', 'landing, projet, générique, exemple')" />
    <meta name="author" content="VotreProjet" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Landing Générique')</title>

    {{-- Open Graph / Réseaux sociaux --}}
    <meta property="og:title" content="@yield('og_title', 'Landing Générique')" />
    <meta property="og:description" content="@yield('og_description', 'Page d’accueil générique pour votre projet.')" />
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url()->current() }}" />

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="@yield('twitter_title', 'Landing Générique')" />
    <meta name="twitter:description" content="@yield('twitter_description', 'Page d’accueil générique pour votre projet.')" />
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/og-default.jpg'))" />

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}" />

    {{-- CSS / JS principaux --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('css')

    @stack('stylesFullCalendar')

    @include('partials.fullCalendar')
    {{-- Bloc JSON-LD principal (Organisation) --}}
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'VotreProjet',
        'url' => url('/'),
        'logo' => asset('images/logo.png'),
        'sameAs' => [],
        'description' => 'Page d’accueil générique pour votre projet.',
    ], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) !!}
    </script>

    {{-- Bloc JSON-LD spécifique à chaque page --}}
    @yield('jsonld')
</head>

<body class="bg-[--color-bg] text-[--color-text] font-body antialiased selection:bg-[--color-primary]/30">

    {{-- Contenu spécifique à la page --}}
    <main>
        @yield('content')
    </main>

    {{-- Scripts additionnels --}}

    @stack('js')


    @stack('scriptsFullCalendar')

    {{-- Modals globaux --}}
    @stack('modals')

    <!-- Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" id="toastContainer"></div>

</body>
</html>
