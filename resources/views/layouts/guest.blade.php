<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-t">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        {{-- A classe 'bg-bg-light' agora funciona --}}
        <div class="flex flex-col items-center justify-center min-h-screen px-4 pt-6 sm:pt-0 bg-bg-light">

            {{-- Logo com estilo moderno e profissional --}}
            <div class="mb-1">
                <a href="/" class="block">
                    <img class="w-30 h-24 object-contain shadow-lg p-1 bg-white ring-4 ring-offset-2 ring-blue-200 transition-all duration-300 hover:ring-blue-400"
                         src="{{ asset('images/ducatoys.png') }}"
                         alt="Logo DucaToys">
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
