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
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <!-- Scripts -->
        <script>
            // Early theme init to avoid flash of incorrect theme
            (function () {
                try {
                    var html = document.documentElement;
                    var saved = localStorage.getItem('theme');
                    if (saved === 'dark' || (!saved && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                        html.classList.add('dark');
                    } else {
                        html.classList.remove('dark');
                    }
                } catch (e) { /* no-op */ }
            })();
        </script>
        <link rel="stylesheet" href="{{asset('css/style.css')}}">
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    </head>
    <body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white transition-colors duration-300 overflow-hidden">
        {{-- <x-banner /> --}}

        <div class="h-screen flex flex-col overflow-hidden">
            @livewire('navigation-menu')
            @auth
                @livewire('csat.overlay')
            @endauth

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

            if (!window.__toastrHandlersBound) {
                window.__toastrHandlersBound = true;
                window.addEventListener('success', event => {
                    toastr.success(event.detail.message);
                });
                window.addEventListener('error', event => {
                    toastr.error(event.detail.message || 'An error occurred');
                });
            }

            // Real-time subscription for authenticated admin/agent users
            (function(){
                try {
                    if (typeof Echo !== 'undefined' && window.Pusher) {
                        window.Echo = new Echo({
                            broadcaster: 'pusher',
                            key: '{{ env('REVERB_APP_KEY', env('PUSHER_APP_KEY', 'local')) }}',
                            cluster: '{{ env('PUSHER_APP_CLUSTER', 'mt1') }}',
                            wsHost: (function(){
                                try {
                                    var h = '{{ env('REVERB_HOST', request()->getHost()) }}';
                                    if (h === '127.0.0.1' || h === 'localhost' || !h) { return window.location.hostname; }
                                    return h;
                                } catch(_) { return window.location.hostname; }
                            })(),
                            wsPort: {{ (int) env('REVERB_PORT', 6001) }},
                            wssPort: {{ (int) env('REVERB_PORT', 6001) }},
                            forceTLS: {{ env('REVERB_SCHEME','ws') === 'wss' ? 'true' : 'false' }},
                            disableStats: true,
                            enabledTransports: ['ws','wss'],
                            authorizer: (channel, options) => ({
                                authorize: (socketId, callback) => {
                                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                                    fetch('/broadcasting/auth', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': token,
                                            'X-Requested-With': 'XMLHttpRequest',
                                        },
                                        credentials: 'same-origin',
                                        body: JSON.stringify({ socket_id: socketId, channel_name: channel.name })
                                    }).then(async (resp) => {
                                        if (!resp.ok) throw new Error('Auth failed');
                                        const data = await resp.json();
                                        callback(false, data);
                                    }).catch(err => callback(true, err));
                                }
                            }),
                        });
                    }
                    const uid = {{ (int) (auth()->id() ?? 0) }};
                    // Compute role robustly: use relation if present, otherwise fallback to direct lookup by role_id
                    @php
                        $user = auth()->user();
                        $roleSlug = '';
                        if ($user) {
                            $roleSlug = strtolower(optional($user->role)->slug ?? '');
                            if (!$roleSlug && $user->role_id) {
                                $roleModel = \App\Models\Roles::find($user->role_id);
                                $roleSlug = $roleModel ? strtolower($roleModel->slug) : '';
                            }
                        }
                        $canTicketsBool = in_array($roleSlug, ['itss','administrator','developer']);
                    @endphp
                    const canTickets = {{ $canTicketsBool ? 'true' : 'false' }};
                    if (window.Echo && uid) {
                        window.Echo.private('user.'+uid).listen('TicketCommentCreated', (e) => {
                            // Show a toast to admins/agents; components can also listen for this event
                            toastr.info(`New comment on Ticket #${e.ticketNo} by ${e.byName || 'user'}`);
                            window.dispatchEvent(new CustomEvent('helpdesk-comment-created', { detail: e }));
                        });
                        // Mention notifications for admins/agents
                        window.Echo.private('user.'+uid).listen('MentionedInTicket', (e) => {
                            try {
                                toastr.info(`${e.byName || 'Someone'} mentioned you on Ticket #${e.ticketNo}`);
                            } catch(_) {}
                        });
                        // Also subscribe to global ITSS tickets channel to update lists (only for allowed roles)
                        if (canTickets) {
                            window.Echo.private('tickets').listen('TicketChanged', (e) => {
                                // Optional visibility to confirm realtime is firing
                                console.debug('TicketChanged received', e);
                                window.dispatchEvent(new CustomEvent('helpdesk-ticket-changed', { detail: e }));
                            });
                        }
                    }
                } catch (e) { /* noop */ }
            })();
        </script>
        @stack('scripts')
        @livewireScripts
    </body>
</html>
