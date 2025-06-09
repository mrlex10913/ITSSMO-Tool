<li>
    <x-end-user-nav-link
        href="{{ route('itss.dashboard') }}{{ request()->get('dept') ? '?dept=' . request()->get('dept') : '' }}"
        :active="request()->routeIs('itss.dashboard')"
        icon="dashboard">
        Dashboard
    </x-end-user-nav-link>
</li>
<li>
    <x-end-user-nav-link
        href="{{ route('itss.id-production') }}{{ request()->get('dept') ? '?dept=' . request()->get('dept') : '' }}"
        :active="request()->routeIs('itss.id-production')"
        icon="badge">
        ID Production
    </x-end-user-nav-link>
</li>
<li>
    <x-end-user-nav-link
        href="{{ route('itss.helpdesk') }}{{ request()->get('dept') ? '?dept=' . request()->get('dept') : '' }}"
        :active="request()->routeIs('itss.helpdesk')"
        icon="support_agent">
        Helpdesk
    </x-end-user-nav-link>
</li>
