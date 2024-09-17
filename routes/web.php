<?php

use App\Livewire\Assets\AssetsCategory;
use App\Livewire\Assets\AssetsLists;
use App\Livewire\Examination\Admin\Questions;
use App\Livewire\Examination\Admin\Subject;
use App\Livewire\Examination\Coordinator\Codegenerator;
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
    Route::get('/subject', Subject::class)->name('examination.subject');
    Route::get('/subject/questions/{id}', Questions::class)->name('examination.questions');
    Route::get('/coordinator', Codegenerator::class)->name('examination.coordinator');

    //Assets
    Route::get('/assets',AssetsLists::class)->name('assets.view');
    Route::get('/assets-category', AssetsCategory::class)->name('assets.category');

});
