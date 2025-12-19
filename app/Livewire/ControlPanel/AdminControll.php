<?php

namespace App\Livewire\ControlPanel;

use App\Models\AppSetting;
use App\Models\SystemCsatResponse;
use App\Models\User;
use App\Services\CsatEnforcement;
use App\Events\CsatEnforcementChanged;
use Livewire\Component;

class AdminControll extends Component
{
    public ?string $csatEnforceSince = null;
    public int $csatPending = 0;

    public function mount(): void
    {
        $since = CsatEnforcement::enforceSince();
        $this->csatEnforceSince = $since ? $since->toDateTimeString() : null;
        $this->refreshCsatStats();
    }

    public function enableCsatEnforcement(): void
    {
        AppSetting::put(CsatEnforcement::SETTING_KEY, now()->toDateTimeString());
        $this->csatEnforceSince = now()->toDateTimeString();
        $this->dispatch('success', message: 'End-user CSAT enforcement enabled.');
        $this->refreshCsatStats();
    event(new CsatEnforcementChanged(true, $this->csatEnforceSince));
    }

    public function disableCsatEnforcement(): void
    {
        AppSetting::put(CsatEnforcement::SETTING_KEY, '');
        $this->csatEnforceSince = null;
        $this->dispatch('success', message: 'End-user CSAT enforcement disabled.');
        $this->refreshCsatStats();
    event(new CsatEnforcementChanged(false, null));
    }

    protected function refreshCsatStats(): void
    {
        $since = CsatEnforcement::enforceSince();
        if (! $since) {
            $this->csatPending = 0;
            return;
        }

        // Count end users without recent response
        $endUserIds = User::query()
            ->whereHas('role', function ($q) {
                $q->whereIn('slug', ['bfo', 'pamo', 'student', 'employee', 'generic']);
            })
            ->pluck('id');

        if ($endUserIds->isEmpty()) {
            $this->csatPending = 0;
            return;
        }

        $respondedIds = SystemCsatResponse::query()
            ->whereIn('user_id', $endUserIds)
            ->whereNotNull('submitted_at')
            ->where('submitted_at', '>=', $since)
            ->distinct()
            ->pluck('user_id');

        $this->csatPending = $endUserIds->diff($respondedIds)->count();
    }
    public function breadCrumbControlPanel(){
        session(['breadcrumb' => 'Control Panel /']);

    }
    public function render()
    {
        $this->breadCrumbControlPanel();
        return view('livewire.control-panel.admin-controll');
    }
}
