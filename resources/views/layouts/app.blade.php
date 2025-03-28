<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ITSSMO-Tools') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap">
        <link
          rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />
        <!-- Scripts -->
        <link rel="stylesheet" href="{{asset('css/style.css')}}">
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white transition-colors duration-300 overflow-hidden">
        {{-- <x-banner /> --}}

        <div class="h-screen flex flex-col overflow-hidden">
            @livewire('navigation-menu')

            <div class="flex-1 mt-2 ml-2 flex flex-col md:flex-row overflow-hidden">
                <x-side-menu />

                <main class="flex-1 p-4 overflow-y-auto">
                    {{ $slot }}
                </main>

            </div>

        </div>

        @stack('modals')
        <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        {{-- <script src="{{asset('js/mainscript.js')}}"></script> --}}
        <script>
            $(document).ready(function(){
                toastr.options = {
                    "progressBar" : true,
                    "positionClass": "toast-top-right"
                }
            });

            window.addEventListener('success', event => {
                toastr.success(event.detail.message);
            });
        </script>
        @stack('scripts')
        @livewireScripts
    </body>
</html>
