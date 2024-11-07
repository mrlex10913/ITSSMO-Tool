<?php

use App\Http\Controllers\UserRecords\FalcoData as UserRecordsFalcoData;
use App\Livewire\Assets\AssetsCategory;
use App\Livewire\Assets\AssetsConsumable;
use App\Livewire\Assets\AssetsConsumableTracker;
use App\Livewire\Assets\AssetsLists;
use App\Livewire\Assets\AssetsTransfer;
use App\Livewire\Borrowers\BorrowersForm;
use App\Livewire\Borrowers\BorrowersLogs;
use App\Livewire\Borrowers\BorrowersReturn;
use App\Livewire\Borrowers\BrfReservation;
use App\Livewire\Dashboard\ItssIntroduction;
use App\Livewire\Examination\Admin\Questions;
use App\Livewire\Examination\Admin\Subject;
use App\Livewire\Examination\Coordinator\Codegenerator;
use App\Livewire\Manuals\ITSSManual;
use App\Livewire\UserRecords\FalcoData;
use App\Livewire\UserRecords\StaffRecords;
use App\Livewire\UserRecords\StudentRecords;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', ItssIntroduction::class)->name('dashboard');
});
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('import', [UserRecordsFalcoData::class,'import_data'])->name('falco');
    Route::post('import-excel',[UserRecordsFalcoData::class,'import_excel_post'])->name('falco.post');
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    //Manual
    Route::get('/itss-manual', ITSSManual::class)->name('itss.manual');

    //Brainstorm
    Route::get('/subject', Subject::class)->name('examination.subject');
    Route::get('/subject/questions/{id}', Questions::class)->name('examination.questions');
    Route::get('/coordinator', Codegenerator::class)->name('examination.coordinator');



    //Transactions
    Route::get('/consumable-tracker', AssetsConsumableTracker::class)->name('consumable.tracker');
    Route::get('/borrowers-form', BorrowersForm::class)->name('borrower.form');
    Route::get('/assets-transfer', AssetsTransfer::class)->name('asset.form');
    Route::get('/brf-reservation', BrfReservation::class)->name('reservation.form');

    //Records
    //Assets
    Route::get('/assets',AssetsLists::class)->name('assets.view');
    Route::get('/assets-category', AssetsCategory::class)->name('assets.category');
    Route::get('/assetsConsumable', AssetsConsumable::class)->name('assets.consumable');
    //User Records
    // Route::get('/staff-records', StaffRecords::class)->name('staff.records');
    Route::get('/falco-records', FalcoData::class)->name('falco.records');
    Route::get('/student-records', StudentRecords::class)->name('student.records');
    //Borrowers
    Route::get('/borrowers-log', BorrowersLogs::class)->name('borrowers.logs');
    Route::get('/borrower-return', BorrowersReturn::class)->name('borrowers.return');



});
