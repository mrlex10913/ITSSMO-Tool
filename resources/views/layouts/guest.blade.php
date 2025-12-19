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

        <!-- Scripts -->
        <script>
            // Early theme init to prevent FOUC
            (function() {
                try {
                    var t = localStorage.getItem('theme');
                    if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                } catch (e) {}
            })();
        </script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="min-h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white transition-colors duration-300 overflow-x-hidden">
        {{-- <x-banner /> --}}

        <!-- Theme toggle -->
        <div x-data="{ dark: document.documentElement.classList.contains('dark') }" class="fixed top-3 right-3 z-50">
            <button type="button" @click="dark = !dark; document.documentElement.classList.toggle('dark', dark); try{ localStorage.setItem('theme', dark ? 'dark' : 'light') }catch(e){}"
                class="inline-flex items-center gap-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-sm shadow hover:bg-gray-50 dark:hover:bg-gray-700">
                <span class="material-symbols-sharp text-gray-700 dark:text-gray-200" x-show="!dark">dark_mode</span>
                <span class="material-symbols-sharp text-gray-700 dark:text-gray-200" x-show="dark">light_mode</span>
                <span class="text-gray-700 dark:text-gray-200" x-text="dark ? 'Light' : 'Dark'"></span>
            </button>
        </div>

                <main class="p-4">
                    {{ $slot }}
                </main>




        @stack('modals')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

        <script>
            $(document).ready(function(){
                toastr.options = {
                    "progressBar" : true,
                    "positionClass": "toast-top-right"
                }
            });

            if (!window.__toastrHandlersBoundGuest) {
                window.__toastrHandlersBoundGuest = true;
                window.addEventListener('success', event => {
                    toastr.success(event.detail.message);
                });
                window.addEventListener('error', event => {
                    toastr.error(event.detail.message || 'An error occurred');
                });
            }
        </script>
        @stack('scripts')
        @livewireScripts
    </body>
</html>
