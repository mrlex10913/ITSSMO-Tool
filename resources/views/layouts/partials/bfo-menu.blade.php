<li>
    <x-end-user-nav-link
        href="{{ route('bfo.dashboard') }}{{ request()->get('dept') ? '?dept=' . request()->get('dept') : '' }}"
        :active="request()->routeIs('bfo.dashboard')"
        icon="dashboard">
        Overview
    </x-end-user-nav-link>
</li>
<li>
    <x-end-user-nav-link
        href="{{ route('bfo.cheque') }}{{ request()->get('dept') ? '?dept=' . request()->get('dept') : '' }}"
        :active="request()->routeIs('bfo.cheque')"
        icon="receipt_long">
        Cheque Management
    </x-end-user-nav-link>
</li>
{{-- <li>
    <x-end-user-nav-link
        href="{{ route('bfo.cheque-list') }}{{ request()->get('dept') ? '?dept=' . request()->get('dept') : '' }}"
        :active="request()->routeIs('bfo.cheque-list')"
        icon="format_list_bulleted">
        Cheque List
    </x-end-user-nav-link>
</li> --}}
