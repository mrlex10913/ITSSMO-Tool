<?php

namespace App\Livewire\Dashboard;

use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class ItssIntroduction extends Component
{
    public function render()
    {
        // Metrics
        $totalUsers = User::query()->count();
        // Sessions active within last 15 minutes
        $activeSessions = DB::table('sessions')
            ->where('last_activity', '>=', now()->subMinutes(15)->timestamp)
            ->count();

        // Tickets metrics
        $openTickets = Ticket::query()
            ->whereNotIn('status', ['closed', 'resolved'])
            ->count();
        $slaBreached = Ticket::query()
            ->whereNotIn('status', ['closed', 'resolved'])
            ->whereNotNull('sla_due_at')
            ->where('sla_due_at', '<', now())
            ->count();
        $pendingRequests = $openTickets; // treat non-closed tickets as pending

        // Simple health as inverse of SLA breaches among open tickets
        $healthPct = 100.0 - min(100.0, round(($slaBreached / max(1, $openTickets)) * 100, 1));
        $healthLabel = $healthPct >= 98 ? 'Excellent' : ($healthPct >= 95 ? 'Good' : ($healthPct >= 90 ? 'Fair' : 'Degraded'));

        // Recent activities (ticket activity logs)
        $recentActivities = TicketActivityLog::query()
            ->latest('created_at')
            ->with(['user:id,name'])
            ->limit(6)
            ->get()
            ->map(function ($log) {
                return [
                    'title' => Str::limit($log->display_message ?? $log->message ?? ucfirst(str_replace('_', ' ', (string) $log->action)), 80),
                    'by' => optional($log->user)->name,
                    'at' => optional($log->created_at)->diffForHumans(),
                ];
            });

        // System statuses (non-invasive checks)
        $dbOnline = false;
        try {
            DB::select('select 1');
            $dbOnline = true;
        } catch (\Throwable $e) {
            $dbOnline = false;
        }
        $hasPublicDisk = Config::has('filesystems.disks.public');
        $hasMail = Config::has('mail.mailers.'.Config::get('mail.default'));

        return view('livewire.dashboard.itss-introduction', [
            'totalUsers' => $totalUsers,
            'activeSessions' => $activeSessions,
            'systemHealthPercent' => $healthPct,
            'systemHealthLabel' => $healthLabel,
            'pendingRequests' => $pendingRequests,
            'recentActivities' => $recentActivities,
            'status' => [
                'database' => $dbOnline ? 'Online' : 'Down',
                'api' => 'Online', // placeholder
                'storage' => $hasPublicDisk ? 'Online' : 'Unknown',
                'email' => $hasMail ? 'Online' : 'Unknown',
            ],
        ]);
    }
}
