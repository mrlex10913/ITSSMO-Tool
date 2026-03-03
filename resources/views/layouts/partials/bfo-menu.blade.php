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

<li>
    <x-end-user-nav-link
        href="{{ route('bfo.helpdesk') }}{{ request()->get('dept') ? '?dept=' . request()->get('dept') : '' }}"
        :active="request()->routeIs('bfo.helpdesk')"
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
