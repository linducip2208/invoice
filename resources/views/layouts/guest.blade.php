<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>✦ {{ branding('app_name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <x-branding-head />
    </head>
    <body class="font-sans antialiased text-purple-900">
        <div class="hero-aurora min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative">
            <!-- Floating sparkle decorations -->
            <div class="absolute inset-0 pointer-events-none overflow-hidden">
                <span class="absolute top-[8%] left-[12%] text-3xl animate-unicorn-float" style="animation-delay:0s;">✦</span>
                <span class="absolute top-[18%] right-[15%] text-2xl animate-unicorn-twinkle" style="color:#FCD34D;">✧</span>
                <span class="absolute bottom-[20%] left-[8%] text-2xl animate-unicorn-float" style="animation-delay:1.2s; color:#A78BFA;">✦</span>
                <span class="absolute bottom-[12%] right-[10%] text-3xl animate-unicorn-twinkle" style="color:#7DD3FC;">✧</span>
                <span class="absolute top-[40%] left-[5%] text-xl animate-unicorn-float" style="animation-delay:0.8s; color:#FF7AC6;">✦</span>
                <span class="absolute top-[55%] right-[6%] text-xl animate-unicorn-twinkle" style="color:#86EFAC;">✧</span>
            </div>

            <div class="relative z-10 mb-2">
                <a href="/" class="flex flex-col items-center gap-3">
                    <span class="logo-glow flex items-center justify-center animate-unicorn-float">
                        @if(branding('logo_path'))
                            <img src="{{ asset('storage/' . branding('logo_path')) }}" alt="{{ branding('app_name') }}" class="h-16 w-auto drop-shadow-lg">
                        @else
                            <x-application-logo size="xl" class="drop-shadow-lg" />
                        @endif
                    </span>
                    <span class="font-display font-bold text-2xl text-rainbow tracking-tight">{{ branding('app_name') }}</span>
                </a>
            </div>

            <div class="relative z-10 w-full sm:max-w-md mt-4 mb-8 px-7 py-7 auth-card sm:rounded-3xl mx-4">
                {{ $slot }}
            </div>

            <p class="relative z-10 text-xs text-purple-400 mb-6">✦ {{ branding('tagline') }} ✦</p>
        </div>
    </body>
</html>
