<li>
    <x-end-user-nav-link
        href="{{ route('pamo.dashboard') }}{{ request()->get('dept') ? '?dept=' . request()->get('dept') : '' }}"
        :active="request()->routeIs('pamo.dashboard')"
        icon="dashboard">
        Overview
    </x-end-user-nav-link>
</li>
<li>
    <x-end-user-nav-link
        href="{{ route('pamo.inventory') }}{{ request()->get('dept') ? '?dept=' . request()->get('dept') : '' }}"
        :active="request()->routeIs('pamo.inventory')"
        icon="inventory_2">
        Inventory & Supplies
    </x-end-user-nav-link>
</li>
<li>
    <x-end-user-nav-link
        href="{{ route('pamo.assetTracker') }}{{ request()->get('dept') ? '?dept=' . request()->get('dept') : '' }}"
        :active="request()->routeIs('pamo.assetTracker')"
        icon="track_changes">
        Asset's Tracker
    </x-end-user-nav-link>
</li>
<li>
    <x-end-user-nav-link
        href="{{ route('pamo.barcode') }}{{ request()->get('dept') ? '?dept=' . request()->get('dept') : '' }}"
        :active="request()->routeIs('pamo.barcode')"
        icon="qr_code_scanner">
        Barcode Generator
    </x-end-user-nav-link>
</li>
<li>
    <x-end-user-nav-link
        href="{{ route('pamo.masterList') }}{{ request()->get('dept') ? '?dept=' . request()->get('dept') : '' }}"
        :active="request()->routeIs('pamo.masterList')"
        icon="groups">
        MasterList
    </x-end-user-nav-link>
</li>
