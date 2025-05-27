<?php

use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\UserRecords\FalcoData as UserRecordsFalcoData;
use App\Http\Middleware\CheckTemporaryPassword;
use App\Http\Middleware\DesktopBorrwersIpFilter;
use App\Livewire\Assets\AssetsCategory;
use App\Livewire\Assets\AssetsConsumable;
use App\Livewire\Assets\AssetsConsumableTracker;
use App\Livewire\Assets\AssetsLists;
use App\Livewire\Assets\AssetsTransfer;
use App\Livewire\Borrowers\BorrowersForm;
use App\Livewire\Borrowers\BorrowersLogs;
use App\Livewire\Borrowers\BorrowersReturn;
use App\Livewire\Borrowers\BrfReservation;
use App\Livewire\BorrowersDesktop;
use App\Livewire\ChangePassword;
use App\Livewire\ControlPanel\AdminControll;
use App\Livewire\ControlPanel\UsersControl;
use App\Livewire\Dashboard\ItssIntroduction;
use App\Livewire\Examination\Admin\Questions;
use App\Livewire\Examination\Admin\Subject;
use App\Livewire\Examination\Coordinator\Codegenerator;
use App\Livewire\Manuals\ITSSManual;
use App\Livewire\PAMO\AssetTracker;
use App\Livewire\PAMO\BarcodeGenerator;
use App\Livewire\PAMO\Dashboard;
use App\Livewire\PAMO\Inventory;
use App\Livewire\PAMO\MasterList;
use App\Livewire\PAMO\Transactions;
use App\Livewire\UserRecords\FalcoData;
use App\Livewire\UserRecords\StaffRecords;
use App\Livewire\UserRecords\StudentRecords;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('login');
// });


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    CheckTemporaryPassword::class,
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
    });


    //PAMO
    // Route::get('/pamo/dashboard', Dashboard::class)->name('pamo.dashboard');
    // Route::get('/pamo/invetory', Inventory::class)->name('pamo.inventory');
    // Route::get('/pamo/transactions', Transactions::class)->name('pamo.transactions');
    // Route::get('/pamo/generateBarcode', BarcodeGenerator::class)->name('pamo.barcode');
    // Route::get('/print-barcode-view', [BarcodeGenerator::class, 'printBarcodes'])->name('print-barcode-view');

});
Route::get('/password/change', ChangePassword::class)->name('password.change');

// Route::get('/desktop/borrowers', BorrowersDesktop::class)->name('desktop.borrowers');
Route::middleware(['ip.filter'])->group(function(){
    Route::get('/desktop/borrowers', BorrowersDesktop::class)->name('desktop.borrowers');
});

Route::middleware([
    'auth:sanctum',
    'verified',
    'role:pamo,administrator,developer',
    ])->prefix('pamo')->group(function(){
    Route::get('/dashboard', Dashboard::class)->name('pamo.dashboard');
    Route::get('/inventory', Inventory::class)->name('pamo.inventory');
    Route::get('/barcode', BarcodeGenerator::class)->name('pamo.barcode');
    Route::get('/transactions', Transactions::class)->name('pamo.transactions');
    Route::get('/assets-tracker', AssetTracker::class)->name('pamo.assetTracker');
    Route::get('/masterList', MasterList::class)->name('pamo.masterList');
});



// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified',
//     CheckTemporaryPassword::class,
// ])->group(function () {
//     Route::get('/dashboard', ItssIntroduction::class)->name('dashboard');

//     Route::get('/password/change', ChangePassword::class)->name('password.change');
// });
// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified',
// ])->group(function () {

//     Route::get('import', [UserRecordsFalcoData::class,'import_data'])->name('falco');
//     Route::post('import-excel',[UserRecordsFalcoData::class,'import_excel_post'])->name('falco.post');
// });


// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified',
// ])->group(function () {

//     //Manual
//     Route::get('/itss-manual', ITSSManual::class)->name('itss.manual');

//     //Brainstorm
//     Route::get('/subject', Subject::class)->name('examination.subject');
//     Route::get('/subject/questions/{id}', Questions::class)->name('examination.questions');
//     Route::get('/coordinator', Codegenerator::class)->name('examination.coordinator');



//     //Transactions
//     Route::get('/consumable-tracker', AssetsConsumableTracker::class)->name('consumable.tracker');
//     Route::get('/borrowers-form', BorrowersForm::class)->name('borrower.form');
//     Route::get('/assets-transfer', AssetsTransfer::class)->name('asset.form');
//     Route::get('/brf-reservation', BrfReservation::class)->name('reservation.form');

//     //Records
//     //Assets
//     Route::get('/assets',AssetsLists::class)->name('assets.view');
//     Route::get('/assets-category', AssetsCategory::class)->name('assets.category');
//     Route::get('/assetsConsumable', AssetsConsumable::class)->name('assets.consumable');
//     //User Records
//     // Route::get('/staff-records', StaffRecords::class)->name('staff.records');
//     Route::get('/falco-records', FalcoData::class)->name('falco.records');
//     Route::get('/student-records', StudentRecords::class)->name('student.records');
//     //Borrowers
//     Route::get('/borrowers-log', BorrowersLogs::class)->name('borrowers.logs');
//     Route::get('/borrower-return', BorrowersReturn::class)->name('borrowers.return');

//     //Admin Access
//     Route::get('/control-panel', AdminControll::class)->name('controlPanel.admin');
//     Route::get('/control-panel/userControl', UsersControl::class)->name('controlPanel.user');

// });
