<?php

use App\Http\Controllers\MasterFileController;
use App\Http\Controllers\TicketAttachmentController;
use App\Http\Controllers\UserRecords\FalcoData as UserRecordsFalcoData;
use App\Http\Middleware\DesktopBorrwersIpFilter;
use App\Livewire\Assets\AssetsCategory;
use App\Livewire\Assets\AssetsConsumable;
use App\Livewire\Assets\AssetsConsumableTracker;
use App\Livewire\Assets\AssetsLists;
use App\Livewire\Assets\AssetsTransfer;
use App\Livewire\BFO\BFODashboard;
use App\Livewire\BFO\Cheque;
use App\Livewire\BFO\ChequeList;
use App\Livewire\Borrowers\BorrowersForm;
use App\Livewire\Borrowers\BorrowersLogs;
use App\Livewire\Borrowers\BorrowersReturn;
use App\Livewire\Borrowers\BrfReservation;
use App\Livewire\BorrowersDesktop;
use App\Livewire\ChangePassword;
use App\Livewire\ControlPanel\AdminControll;
use App\Livewire\ControlPanel\DepartmentsControl;
use App\Livewire\ControlPanel\MenusControl;
use App\Livewire\ControlPanel\Reports\SurveyReport;
use App\Livewire\ControlPanel\RolesControl;
use App\Livewire\ControlPanel\UsersControl;
use App\Livewire\Dashboard\Generic as GenericDashboard;
use App\Livewire\Dashboard\ItssIntroduction;
use App\Livewire\Examination\Admin\Questions;
use App\Livewire\Examination\Admin\Subject;
use App\Livewire\Examination\Coordinator\Codegenerator;
use App\Livewire\Manuals\ITSSManual;
use App\Livewire\MasterFiles\Dashboard as MasterFileDashboard;
use App\Livewire\PAMO\AssetTracker;
use App\Livewire\PAMO\BarcodeGenerator;
use App\Livewire\PAMO\Dashboard;
use App\Livewire\PAMO\Inventory;
use App\Livewire\PAMO\MasterList;
use App\Livewire\PAMO\Transactions;
use App\Livewire\Tickets\GuestHome;
use App\Livewire\Tickets\GuestPortal;
use App\Livewire\Tickets\GuestTrack;
use App\Livewire\Tickets\MyTickets;
use App\Livewire\Tickets\TicketShow as EndUserTicketShow;
use App\Livewire\UserRecords\FalcoData;
use App\Livewire\UserRecords\StudentRecords;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// CSAT public endpoints (no auth)
Route::get('/csat/{token}', [\App\Http\Controllers\CsatController::class, 'show'])->name('csat.show');
Route::post('/csat/{token}', [\App\Http\Controllers\CsatController::class, 'submit'])->name('csat.submit');

// Route::get('/', function () {
//     return view('login');
// });

// Public Helpdesk submission portal (no auth)
Route::middleware(['throttle:20,1'])->group(function () {
    Route::get('/helpdesk', GuestHome::class)->name('helpdesk.home');
    Route::get('/helpdesk/new', GuestPortal::class)->name('helpdesk.new');
    Route::get('/helpdesk/track', GuestTrack::class)->name('helpdesk.track');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'check.temporary.password',
    // DesktopBorrwersIpFilter::class,
])->group(function () {
    Route::middleware(['role:administrator,developer'])->group(function () {
        Route::get('/', ItssIntroduction::class)->name('dashboard');

        Route::get('import', [UserRecordsFalcoData::class, 'import_data'])->name('falco');
        Route::post('import-excel', [UserRecordsFalcoData::class, 'import_excel_post'])->name('falco.post');

        // Add the Livewire route for password change

        // Other routes
        Route::get('/itss-manual', ITSSManual::class)->name('itss.manual');
        Route::get('/subject', Subject::class)->name('examination.subject');
        Route::get('/subject/questions/{id}', Questions::class)->name('examination.questions');
        Route::get('/coordinator', Codegenerator::class)->name('examination.coordinator');
        Route::get('/consumable-tracker', AssetsConsumableTracker::class)->name('consumable.tracker');
        Route::get('/borrowers-form', BorrowersForm::class)->name('borrower.form');
        Route::get('/assets-transfer', AssetsTransfer::class)->name('asset.form');
        Route::get('/brf-reservation', BrfReservation::class)->name('reservation.form');
        Route::get('/assets', AssetsLists::class)->name('assets.view');
        Route::get('/assets-category', AssetsCategory::class)->name('assets.category');
        Route::get('/assetsConsumable', AssetsConsumable::class)->name('assets.consumable');
        Route::get('/falco-records', FalcoData::class)->name('falco.records');
        Route::get('/student-records', StudentRecords::class)->name('student.records');
        Route::get('/borrowers-log', BorrowersLogs::class)->name('borrowers.logs');
        Route::get('/borrower-return', BorrowersReturn::class)->name('borrowers.return');
        Route::get('/control-panel', AdminControll::class)->name('controlPanel.admin');
        Route::get('/control-panel/userControl', UsersControl::class)->name('controlPanel.user');
        Route::get('/control-panel/roles', RolesControl::class)->name('controlPanel.roles');
        Route::get('/control-panel/menus', MenusControl::class)->name('controlPanel.menus');
        Route::get('/control-panel/departments', DepartmentsControl::class)->name('controlPanel.departments');
        Route::get('/control-panel/reports/surveys', SurveyReport::class)->name('controlPanel.reports.surveys');
        Route::get('/control-panel/reports/surveys/export', function () {
            $dept = request('department');
            $start = request('start_date') ? Carbon::parse(request('start_date'))->startOfDay() : now()->subDays(60)->startOfDay();
            $end = request('end_date') ? Carbon::parse(request('end_date'))->endOfDay() : now()->endOfDay();
            $source = request('source', 'all'); // all|guest|end_user

            $guest = DB::table('csat_responses')
                ->join('tickets', 'tickets.id', '=', 'csat_responses.ticket_id')
                ->leftJoin('ticket_categories', 'ticket_categories.id', '=', 'tickets.category_id')
                ->selectRaw("csat_responses.submitted_at as submitted_at, tickets.ticket_no as ticket_no, COALESCE(ticket_categories.name, 'Uncategorized') as category, tickets.department as department, CASE csat_responses.rating WHEN 'good' THEN 5 WHEN 'neutral' THEN 3 WHEN 'poor' THEN 1 ELSE NULL END as rating, csat_responses.comment as comment, 'guest' as source")
                ->whereNotNull('csat_responses.submitted_at')
                ->whereBetween('csat_responses.submitted_at', [$start, $end])
                ->when($dept !== null && $dept !== '', fn ($qq) => $qq->where('tickets.department', $dept));

            $endUser = DB::table('system_csat_responses')
                ->join('users', 'users.id', '=', 'system_csat_responses.user_id')
                ->leftJoin('departments', 'departments.slug', '=', 'users.department')
                ->selectRaw("system_csat_responses.submitted_at as submitted_at, '' as ticket_no, '—' as category, COALESCE(departments.slug, users.department, '') as department, system_csat_responses.rating as rating, system_csat_responses.comment as comment, 'end_user' as source")
                ->whereNotNull('system_csat_responses.submitted_at')
                ->whereBetween('system_csat_responses.submitted_at', [$start, $end])
                ->when($dept !== null && $dept !== '', fn ($qq) => $qq->where('users.department', $dept));

            if ($source === 'guest') {
                $rows = $guest->orderBy('submitted_at', 'desc')->get();
            } elseif ($source === 'end_user') {
                $rows = $endUser->orderBy('submitted_at', 'desc')->get();
            } else {
                $union = $guest->unionAll($endUser);
                $rows = DB::query()->fromSub($union, 'u')->orderBy('submitted_at', 'desc')->get();
            }

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="csat-export.csv"',
            ];
            $callback = function () use ($rows) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['submitted_at', 'source', 'ticket_no', 'category', 'department', 'rating', 'comment']);
                foreach ($rows as $row) {
                    fputcsv($out, [
                        (string) $row->submitted_at,
                        (string) ($row->source ?? ''),
                        (string) ($row->ticket_no ?? ''),
                        (string) ($row->category ?? ''),
                        (string) ($row->department ?? ''),
                        (string) ($row->rating ?? ''),
                        (string) ($row->comment ?? ''),
                    ]);
                }
                fclose($out);
            };

            return response()->stream($callback, 200, $headers);
        })->name('controlPanel.reports.surveys.export');

        Route::prefix('master-file')->name('master-file.')->group(function () {
            Route::get('/dashboard', MasterFileDashboard::class)->name('dashboard');
            Route::get('/categories', \App\Livewire\MasterFiles\Categories::class)->name('categories');
            Route::get('/upload', \App\Livewire\MasterFiles\Upload::class)->name('upload');
            Route::get('/upload/{parent_id}', \App\Livewire\MasterFiles\Upload::class)->name('upload-version');
            Route::get('/search', \App\Livewire\MasterFiles\Search::class)->name('search');
            Route::get('/versions', \App\Livewire\MasterFiles\Versions::class)->name('versions');
            Route::get('/analytics', \App\Livewire\MasterFiles\Analytics::class)->name('analytics');
            Route::get('/file/{file}', \App\Livewire\MasterFiles\Show::class)->name('show');
            Route::get('/file/{file}/download', [MasterFileController::class, 'download'])->name('download');
        });
    });

});
// Route::get('/password/change', ChangePassword::class)->name('password.change');

// Route::get('/desktop/borrowers', BorrowersDesktop::class)->name('desktop.borrowers');
Route::middleware(['ip.filter'])->group(function () {
    Route::get('/desktop/borrowers', BorrowersDesktop::class)->name('desktop.borrowers');
});

Route::middleware([
    'auth:sanctum',
    'verified',
    'role:pamo,administrator,developer',
    'check.temporary.password',
])->prefix('pamo')->name('pamo.')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/inventory', Inventory::class)->name('inventory');
    Route::get('/barcode', BarcodeGenerator::class)->name('barcode');
    Route::get('/transactions', Transactions::class)->name('transactions');
    Route::get('/assets-tracker', AssetTracker::class)->name('assetTracker');
    Route::get('/masterList', MasterList::class)->name('masterList');
    // PAMO Helpdesk: end-user scoped tickets (create + track own)
    Route::get('/helpdesk', MyTickets::class)->name('helpdesk');
    // PAMO Ticket detail for end-users (shared end-user TicketShow)
    Route::get('/tickets/{ticket}', EndUserTicketShow::class)->name('tickets.show');
});

Route::middleware([
    'auth:sanctum',
    'verified',
    'role:bfo,administrator,developer',
    'check.temporary.password',
])->prefix('bfo')->name('bfo.')->group(function () {
    Route::get('/dashboard', BFODashboard::class)->name('dashboard');
    Route::get('/cheque', Cheque::class)->name('cheque');
    Route::get('/cheque-list', ChequeList::class)->name('cheque-list');
    // BFO Helpdesk: end-user scoped tickets (create + track own)
    Route::get('/helpdesk', MyTickets::class)->name('helpdesk');
    // BFO Ticket detail for end-users (shared end-user TicketShow)
    Route::get('/tickets/{ticket}', EndUserTicketShow::class)->name('tickets.show');
});

Route::middleware([
    'auth:sanctum',
    'verified',
    'role:itss,administrator,developer',
    'check.temporary.password',
])->prefix('itss')->name('itss.')->group(function () {
    Route::get('/dashboard', App\Livewire\ITSS\Dashboard::class)->name('dashboard');
    Route::get('/id-production', App\Livewire\ITSS\IDProduction::class)->name('id-production');
    Route::get('/helpdesk', App\Livewire\ITSS\Helpdesk::class)->name('helpdesk');
    Route::get('/tickets/{ticket}', App\Livewire\ITSS\TicketShow::class)->name('ticket.show');
    Route::get('/sla-policies', App\Livewire\ITSS\SlaPolicies::class)->name('sla.policies');
    Route::get('/sla/insights', App\Livewire\ITSS\SLA\Insights::class)->name('sla.insights');
    Route::get('/escalations', App\Livewire\ITSS\EscalationsPanel::class)->name('escalations');
    Route::get('/canned-responses', App\Livewire\ITSS\CannedResponses::class)->name('canned');
    Route::get('/macros', App\Livewire\ITSS\Macros::class)->name('macros');
    Route::get('/assignment-rules', App\Livewire\ITSS\AssignmentRules::class)->name('assignment-rules');
    Route::get('/sla-escalations', App\Livewire\ITSS\SlaEscalations::class)->name('sla.escalations');
    Route::get('/reports/iso-audit', App\Livewire\ITSS\Reports\IsoAudit::class)->name('reports.iso-audit');
});

Route::get('/password/change', ChangePassword::class)->name('password.change');
Route::middleware([
    'auth:sanctum',
    'verified',
    'check.temporary.password',
])->group(function () {
    // Generic dashboard route for new roles
    Route::get('/dashboard/generic', GenericDashboard::class)->name('generic.dashboard');
    // Generic Helpdesk for any authenticated role (end-user scoped tickets)
    Route::get('/helpdesk/app', MyTickets::class)->name('generic.helpdesk');
    Route::get('/attachments/{attachment}/download', [TicketAttachmentController::class, 'download'])->name('attachments.download');
    Route::get('/attachments/{attachment}/preview', [TicketAttachmentController::class, 'preview'])->name('attachments.preview');
    // End-user Helpdesk (students/employees): list and view own tickets
    Route::get('/tickets', MyTickets::class)->name('tickets.index');
    Route::get('/tickets/{ticket}', EndUserTicketShow::class)->name('tickets.show');
});
