<?php

use App\Livewire\Assets\AssetsCategory;
use App\Livewire\Assets\AssetsConsumable;
use App\Livewire\Assets\AssetsConsumableTracker;
use App\Livewire\Assets\AssetsLists;
use App\Livewire\Assets\AssetsTransfer;
use App\Livewire\Borrowers\BorrowersForm;
use App\Livewire\Examination\Admin\Questions;
use App\Livewire\Examination\Admin\Subject;
use App\Livewire\Examination\Coordinator\Codegenerator;
use App\Livewire\Manuals\ITSSManual;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
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

    //Assets
    Route::get('/assets',AssetsLists::class)->name('assets.view');
    Route::get('/assets-category', AssetsCategory::class)->name('assets.category');
    Route::get('/assetsConsumable', AssetsConsumable::class)->name('assets.consumable');

    //Transactions
    Route::get('/consumable-tracker', AssetsConsumableTracker::class)->name('consumable.tracker');
    Route::get('/borrowers-form', BorrowersForm::class)->name('borrower.form');
    Route::get('/assets-transfer', AssetsTransfer::class)->name('asset.form');

});
