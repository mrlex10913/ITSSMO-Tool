<?php

namespace App\Livewire\ITSS\Reports;

use App\Services\Helpdesk\HelpdeskReportingService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.enduser')]
class HelpdeskReports extends Component
{
    public string $period = 'monthly';

    public string $activeTab = 'overview';

    protected HelpdeskReportingService $reportService;

    public function boot(HelpdeskReportingService $reportService): void
    {
        $this->reportService = $reportService;
    }

    public function setPeriod(string $period): void
    {
        if (in_array($period, ['daily', 'weekly', 'monthly', 'yearly'])) {
            $this->period = $period;
        }
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        $summary          = $this->reportService->getSummaryStats($this->period);
        $volumeTrends     = $this->reportService->getTicketVolumeTrends($this->period);
        $agentPerformance = $this->reportService->getAgentPerformance($this->period);
        $slaCompliance    = $this->reportService->getSlaCompliance($this->period);
        $topCategories    = $this->reportService->getTopCategories($this->period);

        return view('livewire.i-t-s-s.reports.helpdesk-reports', [
            'summary'          => $summary,
            'volumeTrends'     => $volumeTrends,
            'agentPerformance' => $agentPerformance,
            'slaCompliance'    => $slaCompliance,
            'topCategories'    => $topCategories,
        ]);
    }
}
