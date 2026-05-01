<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: #FCFCFC;
            }

            html.dark {
                background-color: #0F1115;
            }
        </style>

        {{-- Adaptive SVG favicon: scales infinitely, responds to prefers-color-scheme --}}
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        {{-- Per-scheme PNG variants for browsers that don't render SVG favicons reliably.
             Baked from SVG via `npm run brand` — see scripts/bake-brand.mjs. --}}
        <link rel="icon" type="image/png" href="/favicon-light.png" sizes="512x512" media="(prefers-color-scheme: light)">
        <link rel="icon" type="image/png" href="/favicon-dark.png" sizes="512x512" media="(prefers-color-scheme: dark)">
        {{-- Legacy multi-size .ico fallback (16/32/48/64) --}}
        <link rel="alternate icon" type="image/x-icon" href="/favicon.ico" sizes="any">
        {{-- Safari pinned-tab silhouette --}}
        <link rel="mask-icon" href="/favicon.svg" color="#1BA85A">
        {{-- iOS home-screen — PNG primary (Apple's convention), SVG as alternate --}}
        <link rel="apple-touch-icon" href="/apple-touch-icon.png" sizes="180x180">
        <link rel="apple-touch-icon" type="image/svg+xml" href="/apple-touch-icon.svg">
        {{-- Theme color, paired with prefers-color-scheme --}}
        <meta name="theme-color" content="#FCFCFC" media="(prefers-color-scheme: light)">
        <meta name="theme-color" content="#0F1115" media="(prefers-color-scheme: dark)">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|jetbrains-mono:400,500|firago:400,500,600" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/devicon.min.css" />

        @vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        @php
            $seo = $page['props']['seo'] ?? [];
            $seoDefaults = $page['props']['seoDefaults'] ?? [];
            $seoTitle = $seo['title'] ?? null;
            $seoDescription = $seo['description'] ?? null;
            $seoCanonical = $seo['canonical'] ?? null;
            $seoRobots = $seo['robots'] ?? null;
            $og = $seo['og'] ?? [];
            $twitter = $seo['twitter'] ?? [];
            $schema = $seo['schema'] ?? null;
            $appName = config('app.name', 'Grdzelo');
            $twitterHandle = $seoDefaults['twitter_handle'] ?? null;
        @endphp
        <title>{{ $seoTitle ? "{$seoTitle} — {$appName}" : $appName }}</title>
        @if($seoDescription)
            <meta name="description" content="{{ $seoDescription }}">
        @endif
        @if($seoRobots)
            <meta name="robots" content="{{ $seoRobots }}">
        @endif
        @if($seoCanonical)
            <link rel="canonical" href="{{ $seoCanonical }}">
        @endif
        {{-- Hreflang alternates — emitted server-side so search engines see all locale variants on first crawl --}}
        @foreach(($page['props']['hreflang'] ?? []) as $alt)
            <link rel="alternate" hreflang="{{ $alt['hreflang'] }}" href="{{ $alt['href'] }}">
        @endforeach
        {{-- Open Graph --}}
        <meta property="og:site_name" content="{{ $appName }}">
        @if($og['title'] ?? null)
            <meta property="og:title" content="{{ $og['title'] }}">
        @endif
        @if($og['description'] ?? null)
            <meta property="og:description" content="{{ $og['description'] }}">
        @endif
        @if($og['image'] ?? null)
            <meta property="og:image" content="{{ $og['image'] }}">
            @if($og['image_width'] ?? null)
                <meta property="og:image:width" content="{{ $og['image_width'] }}">
            @endif
            @if($og['image_height'] ?? null)
                <meta property="og:image:height" content="{{ $og['image_height'] }}">
            @endif
            @if($og['image_alt'] ?? null)
                <meta property="og:image:alt" content="{{ $og['image_alt'] }}">
            @endif
        @endif
        @if($og['type'] ?? null)
            <meta property="og:type" content="{{ $og['type'] }}">
        @endif
        @if($seoCanonical)
            <meta property="og:url" content="{{ $seoCanonical }}">
        @endif
        {{-- Twitter Card --}}
        @if($twitter['card'] ?? null)
            <meta name="twitter:card" content="{{ $twitter['card'] }}">
        @endif
        @if($twitterHandle)
            <meta name="twitter:site" content="@{{ $twitterHandle }}">
        @endif
        @if($twitter['title'] ?? null)
            <meta name="twitter:title" content="{{ $twitter['title'] }}">
        @endif
        @if($twitter['description'] ?? null)
            <meta name="twitter:description" content="{{ $twitter['description'] }}">
        @endif
        @if($twitter['image'] ?? null)
            <meta name="twitter:image" content="{{ $twitter['image'] }}">
            @if($twitter['image_alt'] ?? null)
                <meta name="twitter:image:alt" content="{{ $twitter['image_alt'] }}">
            @endif
        @endif
        {{-- JSON-LD Schema --}}
        @if($schema)
            <script type="application/ld+json">{!! json_encode($schema) !!}</script>
        @endif
    </head>
    <body class="font-sans antialiased">
        <x-inertia::app />
    </body>
</html>
