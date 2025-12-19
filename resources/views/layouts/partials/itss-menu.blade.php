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

<li class="pt-2 border-t border-blue-500 mt-2 text-blue-200 text-xs px-3">Admin</li>
<li>
    <x-end-user-nav-link
        href="{{ route('itss.sla.policies') }}{{ request()->get('dept') ? '?dept=' . request()->get('dept') : '' }}"
        :active="request()->routeIs('itss.sla.policies')"
        icon="schedule">
        SLA Policies
    </x-end-user-nav-link>
    <x-end-user-nav-link
        href="{{ route('itss.sla.escalations') }}{{ request()->get('dept') ? '?dept=' . request()->get('dept') : '' }}"
        :active="request()->routeIs('itss.sla.escalations')"
        icon="priority_high">
        SLA Escalations
    </x-end-user-nav-link>
    <x-end-user-nav-link
        href="{{ route('itss.sla.insights') }}{{ request()->get('dept') ? '?dept=' . request()->get('dept') : '' }}"
        :active="request()->routeIs('itss.sla.insights')"
        icon="insights">
        SLA Insights
    </x-end-user-nav-link>
    <x-end-user-nav-link
        href="{{ route('itss.escalations') }}{{ request()->get('dept') ? '?dept=' . request()->get('dept') : '' }}"
        :active="request()->routeIs('itss.escalations')"
        icon="priority">
        Escalations
    </x-end-user-nav-link>
    <x-end-user-nav-link
        href="{{ route('itss.canned') }}{{ request()->get('dept') ? '?dept=' . request()->get('dept') : '' }}"
        :active="request()->routeIs('itss.canned')"
        icon="chat">
        Canned Responses
    </x-end-user-nav-link>
    <x-end-user-nav-link
        href="{{ route('itss.macros') }}{{ request()->get('dept') ? '?dept=' . request()->get('dept') : '' }}"
        :active="request()->routeIs('itss.macros')"
        icon="bolt">
        Macros
    </x-end-user-nav-link>
    <x-end-user-nav-link
        href="{{ route('itss.assignment-rules') }}{{ request()->get('dept') ? '?dept=' . request()->get('dept') : '' }}"
        :active="request()->routeIs('itss.assignment-rules')"
        icon="assignment_ind">
        Assignment Rules
    </x-end-user-nav-link>
    <x-end-user-nav-link
        href="{{ route('itss.reports.iso-audit') }}{{ request()->get('dept') ? '?dept=' . request()->get('dept') : '' }}"
        :active="request()->routeIs('itss.reports.iso-audit')"
        icon="rule_folder">
        ISO Audit Report
    </x-end-user-nav-link>
</li>
