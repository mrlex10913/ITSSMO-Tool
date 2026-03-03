<?php

namespace App\Livewire\ITSS\Reports;

use App\Services\Helpdesk\HelpdeskReportingService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.enduser')]
class HelpdeskReports extends Component
{
    public int $periodDays = 30;

    public string $activeTab = 'overview';

    protected HelpdeskReportingService $reportService;

    public function boot(HelpdeskReportingService $reportService): void
    {
        $this->reportService = $reportService;
    }

    public function setPeriod(int $days): void
    {
        $this->periodDays = $days;
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        $summary = $this->reportService->getSummaryStats($this->periodDays);
        $volumeTrends = $this->reportService->getTicketVolumeTrends($this->periodDays);
        $agentPerformance = $this->reportService->getAgentPerformance($this->periodDays);
        $slaCompliance = $this->reportService->getSlaCompliance($this->periodDays);
        $topCategories = $this->reportService->getTopCategories($this->periodDays);

        return view('livewire.i-t-s-s.reports.helpdesk-reports', [
            'summary' => $summary,
            'volumeTrends' => $volumeTrends,
            'agentPerformance' => $agentPerformance,
            'slaCompliance' => $slaCompliance,
            'topCategories' => $topCategories,
        ]);
    }
}
