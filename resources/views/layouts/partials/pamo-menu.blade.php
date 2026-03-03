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

<li>
    <x-end-user-nav-link
        href="{{ route('pamo.helpdesk') }}{{ request()->get('dept') ? '?dept=' . request()->get('dept') : '' }}"
        :active="request()->routeIs('pamo.helpdesk')"
        icon="support_agent">
        Helpdesk
    </x-end-user-nav-link>
    </li>

<li class="pt-2 border-t border-blue-500 mt-2 text-blue-200 text-xs px-3">Documents</li>
<li>
    <x-end-user-nav-link
        href="{{ route('document-library.dashboard') }}{{ request()->get('dept') ? '?dept=' . request()->get('dept') : '' }}"
        :active="request()->routeIs('document-library.dashboard')"
        icon="folder_managed">
        Document Library
    </x-end-user-nav-link>
</li>
<li>
    <x-end-user-nav-link
        href="{{ route('document-library.upload') }}{{ request()->get('dept') ? '?dept=' . request()->get('dept') : '' }}"
        :active="request()->routeIs('document-library.upload')"
        icon="upload_file">
        Upload Document
    </x-end-user-nav-link>
</li>
<li>
    <x-end-user-nav-link
        href="{{ route('document-library.search') }}{{ request()->get('dept') ? '?dept=' . request()->get('dept') : '' }}"
        :active="request()->routeIs('document-library.search')"
        icon="search">
        Search Documents
    </x-end-user-nav-link>
</li>
