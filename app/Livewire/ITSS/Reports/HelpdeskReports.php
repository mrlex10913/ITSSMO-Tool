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

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public bool $useCustomRange = false;

    protected HelpdeskReportingService $reportService;

    public function boot(HelpdeskReportingService $reportService): void
    {
        $this->reportService = $reportService;
    }

    public function setPeriod(string $period): void
    {
        if (in_array($period, ['daily', 'weekly', 'monthly', 'yearly'])) {
            $this->period         = $period;
            $this->useCustomRange = false;
            $this->dateFrom       = null;
            $this->dateTo         = null;
        }
    }

    public function applyDateRange(): void
    {
        if ($this->dateFrom && $this->dateTo && $this->dateFrom <= $this->dateTo) {
            $this->useCustomRange = true;
        }
    }

    public function clearDateRange(): void
    {
        $this->useCustomRange = false;
        $this->dateFrom       = null;
        $this->dateTo         = null;
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        $period = $this->useCustomRange ? 'custom' : $this->period;
        $from   = $this->useCustomRange ? $this->dateFrom : null;
        $to     = $this->useCustomRange ? $this->dateTo   : null;

        $summary          = $this->reportService->getSummaryStats($period, $from, $to);
        $volumeTrends     = $this->reportService->getTicketVolumeTrends($period, $from, $to);
        $agentPerformance = $this->reportService->getAgentPerformance($period, $from, $to);
        $slaCompliance    = $this->reportService->getSlaCompliance($period, $from, $to);
        $topCategories    = $this->reportService->getTopCategories($period, 10, $from, $to);

        return view('livewire.i-t-s-s.reports.helpdesk-reports', [
            'summary'          => $summary,
            'volumeTrends'     => $volumeTrends,
            'agentPerformance' => $agentPerformance,
            'slaCompliance'    => $slaCompliance,
            'topCategories'    => $topCategories,
        ]);
    }
}
