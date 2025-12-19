<div x-data="{ open: false, ping: false, lastSoundAt: 0 }" @click.outside="open = false" class="relative">
    <button type="button" x-ref="btn" @click="open = !open; if(open){ $wire.markSeen(); } else { $wire.refresh(); }" class="relative p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700" aria-label="Notifications">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <!-- Visual ping on new realtime events -->
        <span x-show="ping" x-transition class="absolute -top-0.5 -right-0.5 inline-flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-600"></span>
        </span>
        @if($unread > 0)
            <span class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-medium leading-none text-white bg-red-600 rounded-full">{{ $unread }}</span>
        @endif
    </button>
    <!-- Audio for notification sound -->
    <audio x-ref="ding" src="{{ asset('sounds/notificationbell.wav') }}" preload="auto"></audio>

    <div x-cloak x-show="open" x-transition class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg overflow-hidden z-50">
        <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div class="text-sm font-semibold text-gray-700 dark:text-gray-200">Notifications</div>
            <button type="button" class="text-xs text-blue-600 dark:text-blue-400 hover:underline" wire:click="refresh">Refresh</button>
        </div>
        <div class="max-h-80 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($items as $item)
                <div class="px-4 py-3 flex gap-3">
                    <div class="flex-shrink-0">
                        @if(($item['type'] ?? '') === 'ticket')
                            <x-heroicon name="identification" class="w-5 h-5 text-blue-500" />
                        @elseif(($item['type'] ?? '') === 'asset')
                            <x-heroicon name="arrow-up-tray" class="w-5 h-5 text-green-500" />
                        @else
                            <x-heroicon name="bell" class="w-5 h-5 text-gray-400" />
                        @endif
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm text-gray-800 dark:text-gray-200 line-clamp-2">{{ $item['message'] }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            @if(!empty($item['ticket_no'])) Ticket #{{ $item['ticket_no'] }} · @endif{{ $item['when'] }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-sm text-gray-500 dark:text-gray-400 text-center">No recent notifications</div>
            @endforelse
        </div>
        <div class="px-4 py-2 bg-gray-50 dark:bg-gray-900 text-right">
            <a href="{{ route('controlPanel.reports.surveys') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">View Reports</a>
        </div>
    </div>

    <script>
        // Refresh and play sound when realtime events arrive
        (function(){
            const refreshAndDing = () => {
                // Refresh items and mark ping
                try { $wire.refresh(); } catch(_) {}
                try {
                    // Visual ping
                    if (!window.__notifyPingTimeout) {
                        $el.__x?.$data.ping = true;
                        window.__notifyPingTimeout = setTimeout(() => { $el.__x?.$data.ping = false; window.__notifyPingTimeout = null; }, 1600);
                    } else {
                        // restart timer
                        clearTimeout(window.__notifyPingTimeout);
                        window.__notifyPingTimeout = setTimeout(() => { $el.__x?.$data.ping = false; window.__notifyPingTimeout = null; }, 1600);
                        $el.__x?.$data.ping = true;
                    }
                } catch(_) {}

                // Throttle sound to avoid rapid repeats
                try {
                    const nowTs = Date.now();
                    const last = ($el.__x?.$data.lastSoundAt) || 0;
                    if (nowTs - last > 1200) {
                        const audio = $refs.ding;
                        if (audio) { audio.currentTime = 0; audio.play().catch(() => {}); }
                        if ($el.__x) { $el.__x.$data.lastSoundAt = nowTs; }
                    }
                } catch(_) {}
            };
            window.addEventListener('helpdesk-comment-created', () => { setTimeout(refreshAndDing, 120); });
            window.addEventListener('helpdesk-ticket-changed', () => { setTimeout(refreshAndDing, 120); });
        })();
    </script>
</div>
