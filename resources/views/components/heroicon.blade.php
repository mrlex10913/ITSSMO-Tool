@props([
    'name',
    'class' => 'w-5 h-5',
    'variant' => 'outline', // outline|solid (only outline implemented for now)
])

@php
    // Normalize name to kebab-case
    $n = str_replace('_', '-', strtolower($name ?? ''));
@endphp

@switch($n)
    {{-- chart-bar (analytics) --}}
    @case('chart-bar')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M3 19.5h18"/>
            <rect x="5" y="12" width="3" height="6" rx="1.5"/>
            <rect x="10.5" y="9" width="3" height="9" rx="1.5"/>
            <rect x="16" y="6.5" width="3" height="11.5" rx="1.5"/>
        </svg>
    @break

    {{-- squares-2x2 (category) --}}
    @case('squares-2x2')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="{{ $class }}">
            <rect x="4.5" y="4" width="7" height="7" rx="1.5"/>
            <rect x="12.5" y="4" width="7" height="7" rx="1.5"/>
            <rect x="4.5" y="12" width="7" height="7" rx="1.5"/>
            <rect x="12.5" y="12" width="7" height="7" rx="1.5"/>
        </svg>
    @break

    {{-- lifebuoy (support_agent) --}}
    @case('lifebuoy')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <circle cx="12" cy="12" r="7.5"/>
            <circle cx="12" cy="12" r="3.5"/>
            <path d="M12 4.5v3"/>
            <path d="M19.5 12h-3"/>
            <path d="M12 19.5v-3"/>
            <path d="M4.5 12h3"/>
        </svg>
    @break

    {{-- plus --}}
    @case('plus')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M12 4.5v15M19.5 12h-15"/>
        </svg>
    @break

    {{-- camera (photo_camera) --}}
    @case('camera')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M7.5 7h9a3 3 0 0 1 3 3v6.5a3 3 0 0 1-3 3h-9a3 3 0 0 1-3-3V10a3 3 0 0 1 3-3Z"/>
            <path d="M9 7l1-1.5a1.5 1.5 0 0 1 1.28-.7h2.44a1.5 1.5 0 0 1 1.28.7L16 7"/>
            <circle cx="12" cy="13" r="3.25"/>
        </svg>
    @break

    {{-- paper-clip (attach_file) --}}
    @case('paper-clip')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M9.75 17.25 16.5 10.5a3 3 0 1 0-4.243-4.243L6.22 12.293a4.5 4.5 0 1 0 6.364 6.364l7.071-7.07"/>
        </svg>
    @break

    {{-- identification (badge) --}}
    @case('identification')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <rect x="3.5" y="4.5" width="17" height="15" rx="2"/>
            <circle cx="9" cy="11" r="2.25"/>
            <path d="M5.5 8h6.5M5.5 16h7M13.5 11.5h5M13.5 14.5h5"/>
        </svg>
    @break

    {{-- document-text (description) --}}
    @case('document-text')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M8.5 3.5h5.379a2 2 0 0 1 1.414.586l3.621 3.621A2 2 0 0 1 19.5 9.121V18.5a2 2 0 0 1-2 2h-9a2 2 0 0 1-2-2v-13a2 2 0 0 1 2-2Z"/>
            <path d="M13.5 3.5V7a2 2 0 0 0 2 2h3.5"/>
            <path d="M8.5 12.5h7M8.5 15.5h7"/>
        </svg>
    @break

    {{-- information-circle (info) --}}
    @case('information-circle')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <circle cx="12" cy="12" r="9"/>
            <path d="M12 8.25h.008M11 11.25h2V16.5"/>
        </svg>
    @break

    {{-- lock-closed (shield_locked/privacy) --}}
    @case('lock-closed')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <rect x="5.25" y="10.5" width="13.5" height="9" rx="2"/>
            <path d="M8.25 10.5V8a3.75 3.75 0 0 1 7.5 0v2.5"/>
        </svg>
    @break

    {{-- bell (notifications) --}}
    @case('bell')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M14.5 18.5a2.5 2.5 0 0 1-5 0M4.75 18.5h14.5c.414 0 .75-.336.75-.75 0-.2-.08-.392-.221-.532l-.659-.659A6.75 6.75 0 0 1 17.25 11V9.75a5.25 5.25 0 1 0-10.5 0V11c0 1.79-.711 3.507-1.971 4.759l-.659.659a.75.75 0 0 0 .53 1.282Z"/>
        </svg>
    @break

    {{-- clock (schedule) --}}
    @case('clock')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <circle cx="12" cy="12" r="9"/>
            <path d="M12 7.5v5l3 1.5"/>
        </svg>
    @break

    {{-- exclamation-triangle (warning) --}}
    @case('exclamation-triangle')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M12 3.5 21 19.5H3L12 3.5Z"/>
            <path d="M12 9v5"/>
            <circle cx="12" cy="16.5" r=".75" fill="currentColor"/>
        </svg>
    @break

    {{-- heart (monitor_heart) --}}
    @case('heart')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M12 20s-6.5-3.8-8.5-7.1C1.7 9.2 3.2 6.5 6 6.5c1.6 0 2.9.9 3.6 2 .7-1.1 2-2 3.6-2 2.8 0 4.3 2.7 2.5 6.4C18.5 16.2 12 20 12 20Z"/>
        </svg>
    @break

    {{-- bars-3 (menu) --}}
    @case('bars-3')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" class="{{ $class }}">
            <path d="M3.5 6.5h17"/>
            <path d="M3.5 12h17"/>
            <path d="M3.5 17.5h17"/>
        </svg>
    @break

    {{-- x-mark (close) --}}
    @case('x-mark')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" class="{{ $class }}">
            <path d="M6 6l12 12M18 6 6 18"/>
        </svg>
    @break

    {{-- user (person) --}}
    @case('user')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" class="{{ $class }}">
            <circle cx="12" cy="8" r="3.25"/>
            <path d="M5 19a7 7 0 0 1 14 0"/>
        </svg>
    @break

    {{-- arrow-right-on-rectangle (logout) --}}
    @case('arrow-right-on-rectangle')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M12 6.5H7a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h5"/>
            <path d="M12 12h8M16 8l4 4-4 4"/>
        </svg>
    @break

    {{-- envelope (email) --}}
    @case('envelope')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" class="{{ $class }}">
            <rect x="3.5" y="5.5" width="17" height="13" rx="2"/>
            <path d="M4.5 7.5 12 12l7.5-4.5"/>
        </svg>
    @break

    {{-- briefcase (work) --}}
    @case('briefcase')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <rect x="3.5" y="7.5" width="17" height="11" rx="2"/>
            <path d="M9 7.5V6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v1.5"/>
            <path d="M3.5 12h17"/>
        </svg>
    @break

    {{-- building-office (business) --}}
    @case('building-office')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="{{ $class }}">
            <rect x="4.5" y="3.5" width="10" height="16" rx="1.5"/>
            <rect x="7" y="6" width="2" height="2"/><rect x="11" y="6" width="2" height="2"/>
            <rect x="7" y="10" width="2" height="2"/><rect x="11" y="10" width="2" height="2"/>
            <path d="M14.5 9.5h5v10h-14"/>
        </svg>
    @break

    {{-- hashtag (numbers) --}}
    @case('hashtag')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="{{ $class }}">
            <path d="M9 3.5 6.5 20.5M17.5 3.5 15 20.5M4 9h16M3 15h16"/>
        </svg>
    @break

    {{-- check (save/confirm) --}}
    @case('check')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" class="{{ $class }}">
            <path d="M5 12.5 10 17.5 19 7.5"/>
        </svg>
    @break

    {{-- archive-box (inventory) --}}
    @case('archive-box')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="{{ $class }}">
            <rect x="4" y="7" width="16" height="12" rx="2"/>
            <rect x="6" y="4" width="12" height="3" rx="1"/>
            <path d="M9 12h6"/>
        </svg>
    @break

    {{-- map-pin (location_on) --}}
    @case('map-pin')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="{{ $class }}">
            <path d="M12 21s6-6.5 6-10a6 6 0 1 0-12 0c0 3.5 6 10 6 10Z"/>
            <circle cx="12" cy="11" r="2"/>
        </svg>
    @break

    {{-- magnifying-glass (search/find_in_page) --}}
    @case('magnifying-glass')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="{{ $class }}">
            <circle cx="10.5" cy="10.5" r="5.5"/>
            <path d="M14.5 14.5 20 20"/>
        </svg>
    @break

    {{-- shield-check (verified_user/admin_panel_settings) --}}
    @case('shield-check')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M12 3.5 19.5 6v6.5c0 4.5-3.5 7.5-7.5 8.5-4-1-7.5-4-7.5-8.5V6L12 3.5Z"/>
            <path d="M8.5 12.5 11 15l4.5-4.5"/>
        </svg>
    @break

    {{-- document-plus (note_add) --}}
    @case('document-plus')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M8.5 3.5h5.379a2 2 0 0 1 1.414.586l3.621 3.621A2 2 0 0 1 19.5 9.121V18.5a2 2 0 0 1-2 2h-9a2 2 0 0 1-2-2v-13a2 2 0 0 1 2-2Z"/>
            <path d="M13.5 3.5V7a2 2 0 0 0 2 2h3.5"/>
            <path d="M9 13h3M10.5 11.5v3"/>
        </svg>
    @break

    {{-- arrow-left (back) --}}
    @case('arrow-left')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M20 12H6"/>
            <path d="M10 8l-4 4 4 4"/>
        </svg>
    @break

    {{-- arrow-down-tray (download) --}}
    @case('arrow-down-tray')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M12 4v10"/>
            <path d="M8.5 10.5 12 14l3.5-3.5"/>
            <rect x="4.5" y="15" width="15" height="4.5" rx="1"/>
        </svg>
    @break

    {{-- arrow-up-tray (upload) --}}
    @case('arrow-up-tray')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M12 14V4"/>
            <path d="M8.5 7.5 12 4l3.5 3.5"/>
            <rect x="4.5" y="15" width="15" height="4.5" rx="1"/>
        </svg>
    @break

    {{-- eye (visibility) --}}
    @case('eye')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/>
            <circle cx="12" cy="12" r="2.5"/>
        </svg>
    @break

    {{-- pencil-square (edit) --}}
    @case('pencil-square')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M14 5.5 18.5 10l-8 8H6v-4.5l8-8Z"/>
            <rect x="4.5" y="4.5" width="12" height="12" rx="2"/>
        </svg>
    @break

    {{-- folder --}}
    @case('folder')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="{{ $class }}">
            <path d="M3.5 7.5a2 2 0 0 1 2-2H9l2 2h7.5a2 2 0 0 1 2 2V17a2 2 0 0 1-2 2H5.5a2 2 0 0 1-2-2v-9.5Z"/>
        </svg>
    @break

    {{-- folder-open --}}
    @case('folder-open')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="{{ $class }}">
            <path d="M3.5 8.5h17a2 2 0 0 1 1.9 2.5l-1.8 6a2 2 0 0 1-1.9 1.5H5.2a2 2 0 0 1-1.9-1.5l-1.8-6a2 2 0 0 1 1.9-2.5Z"/>
            <path d="M5.5 7a2 2 0 0 1 2-2H9l2 2"/>
        </svg>
    @break

    {{-- share --}}
    @case('share')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" class="{{ $class }}">
            <circle cx="6" cy="12" r="2"/>
            <circle cx="18" cy="6" r="2"/>
            <circle cx="18" cy="18" r="2"/>
            <path d="M7.9 11l7.2-4M7.9 13l7.2 4"/>
        </svg>
    @break

    {{-- tag/label --}}
    @case('tag')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="{{ $class }}">
            <path d="M11 4.5h6.5L20 7.5v6.5l-7.5 5.5L4 14V7.5l7-3Z"/>
            <circle cx="15.5" cy="8.5" r="1" fill="currentColor"/>
        </svg>
    @break

    {{-- star --}}
    @case('star')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" class="{{ $class }}">
            <path d="M12 3.5 14.6 9l6 .4-4.6 3.6 1.6 5.5L12 15.9 6.4 18.5 8 13 3.5 9.4l6-.4L12 3.5Z"/>
        </svg>
    @break

    {{-- chart-pie (donut_small) --}}
    @case('chart-pie')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="{{ $class }}">
            <path d="M12 3.5A8.5 8.5 0 1 0 20.5 12H12V3.5Z"/>
            <path d="M12 3.5V12h8.5"/>
        </svg>
    @break

    {{-- arrow-trending-up (trending_up) --}}
    @case('arrow-trending-up')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
            <path d="M4 16l6-6 4 4 6-6"/>
            <path d="M14 8h6v6"/>
        </svg>
    @break

    {{-- pause-circle (pending) --}}
    @case('pause-circle')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="{{ $class }}">
            <circle cx="12" cy="12" r="9"/>
            <path d="M10 9v6M14 9v6"/>
        </svg>
    @break

    {{-- arrow-path (history/refresh/sync) --}}
    @case('arrow-path')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" class="{{ $class }}">
            <path d="M8 7H4V3"/>
            <path d="M4 7a8 8 0 1 1-1.8 4.9"/>
            <path d="M16 17h4v4"/>
            <path d="M20 17a8 8 0 1 1 1.8-4.9"/>
        </svg>
    @break

    {{-- users (groups) --}}
    @case('user-group')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="{{ $class }}">
            <circle cx="8" cy="9" r="2.5"/>
            <circle cx="16" cy="9" r="2"/>
            <path d="M4 19a6 6 0 0 1 8-4.5A6 6 0 0 1 20 19"/>
        </svg>
    @break

    {{-- user-plus (person_add) --}}
    @case('user-plus')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="{{ $class }}">
            <circle cx="10" cy="9" r="2.75"/>
            <path d="M4 19a6 6 0 0 1 12 0"/>
            <path d="M17 8v4M19 10h-4"/>
        </svg>
    @break

    {{-- trash (delete) --}}
    @case('trash')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="{{ $class }}">
            <path d="M4.5 7.5h15"/>
            <rect x="6.5" y="7.5" width="11" height="12" rx="1.5"/>
            <path d="M10 10.5v6M14 10.5v6"/>
            <path d="M9 7.5V6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v1.5"/>
        </svg>
    @break

    {{-- phone --}}
    @case('phone')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="{{ $class }}">
            <path d="M6.5 4.5h3l1.5 4-2 1a11 11 0 0 0 5 5l1-2 4 1.5v3a2 2 0 0 1-2 2c-8 0-12.5-4.5-12.5-12.5a2 2 0 0 1 2-2Z"/>
        </svg>
    @break

    {{-- qr-code --}}
    @case('qr-code')
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="{{ $class }}">
            <rect x="4" y="4" width="6" height="6"/>
            <rect x="14" y="4" width="6" height="6"/>
            <rect x="4" y="14" width="6" height="6"/>
            <path d="M16 14h4v6h-4v-3h2"/>
        </svg>
    @break

    {{-- Default: simple dot to avoid breakage --}}
    @default
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="{{ $class }}">
            <circle cx="12" cy="12" r="3" />
        </svg>
@endswitch
