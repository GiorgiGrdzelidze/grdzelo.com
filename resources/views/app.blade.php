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
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
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
