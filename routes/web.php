<?php

use App\Http\Controllers\Ai\AiController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CoverLetter\CoverLetterAiController;
use App\Http\Controllers\CoverLetter\CoverLetterBuilderController;
use App\Http\Controllers\CoverLetter\CoverLetterController;
use App\Http\Controllers\Cv\CvPreviewController;
use App\Http\Controllers\Cv\CreateCvFromTemplateController;
use App\Http\Controllers\Cv\CvBuilderController;
use App\Http\Controllers\Cv\CvController as CvDraftController;
use App\Http\Controllers\CvController as CvPdfController;
use App\Http\Controllers\Cv\CvTemplateSelectionController;
use App\Http\Controllers\Cv\CvTemplateRenderController;
use App\Http\Controllers\Cv\CvTemplatePreviewController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Pdf\PdfExportController;
use App\Http\Controllers\MarketingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Subscription\CouponController;
use App\Http\Controllers\Subscription\PayFastController;
use App\Http\Controllers\Subscription\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MarketingController::class, 'home'])->name('home');
Route::get('/templates', [MarketingController::class, 'templates'])->name('templates.index');
Route::get('/pricing', [MarketingController::class, 'pricing'])->name('pricing');
Route::get('/contact', [MarketingController::class, 'contact'])->name('contact');
Route::get('/about', [MarketingController::class, 'about'])->name('about');
Route::get('/privacy-policy', [MarketingController::class, 'privacy'])->name('privacy');
Route::get('/terms', [MarketingController::class, 'terms'])->name('terms');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/cv/download', [CvPdfController::class, 'download'])->name('cv.download');
Route::get('/cv/download/template-1', [CvPdfController::class, 'downloadTemplate1'])->name('cv.download.template-1');
Route::get('/cv/download/template-2', [CvPdfController::class, 'downloadTemplate2'])->name('cv.download.template-2');
Route::get('/cv/download/template-3', [CvPdfController::class, 'downloadTemplate3'])->name('cv.download.template-3');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/templates/{cvTemplate:slug}/use', CreateCvFromTemplateController::class)->name('templates.use');
    Route::post('/cvs', [CvDraftController::class, 'store'])->name('cvs.store');
    Route::delete('/cvs/{cv}', [CvDraftController::class, 'destroy'])->name('cvs.destroy');
    Route::get('/cv/{cv}/preview', [CvPreviewController::class, 'show'])->name('cv.preview');
    Route::get('/cvs/{cv}/templates', [CvTemplateSelectionController::class, 'index'])->name('cvs.templates');
    Route::post('/cvs/{cv}/templates', [CvTemplateSelectionController::class, 'store'])->name('cvs.templates.store');
    Route::get('/cvs/{cv}/builder', [CvBuilderController::class, 'edit'])->name('cvs.builder');
    Route::put('/cvs/{cv}/builder', [CvBuilderController::class, 'update'])->name('cvs.builder.update');
    Route::patch('/cvs/{cv}/builder/autosave', [CvBuilderController::class, 'autosave'])->name('cvs.builder.autosave');

    Route::get('/cvs/{cv}/render/{template?}', [CvTemplateRenderController::class, 'show'])->name('cvs.render.show');
    Route::post('/cvs/{cv}/render', [CvTemplateRenderController::class, 'live'])->name('cvs.render.live');
    Route::post('/cvs/{cv}/template', [CvTemplateRenderController::class, 'switchTemplate'])->name('cvs.template.switch');
    Route::post('/cvs/{cv}/export/pdf', [PdfExportController::class, 'storeCv'])->name('pdf-exports.cv.store');
    Route::post('/cvs/{cv}/export/pdf/download', [PdfExportController::class, 'downloadCv'])->name('pdf-exports.cv.download');
    Route::post('/cover-letters/{coverLetter}/export/pdf', [PdfExportController::class, 'storeCoverLetter'])->name('pdf-exports.cover-letter.store');
    Route::post('/cover-letters/{coverLetter}/export/pdf/download', [PdfExportController::class, 'downloadCoverLetter'])->name('pdf-exports.cover-letter.download');
    Route::get('/pdf-exports/{pdfExport}', [PdfExportController::class, 'show'])->name('pdf-exports.show');
    Route::get('/pdf-exports/{pdfExport}/download', [PdfExportController::class, 'download'])->name('pdf-exports.download');

    Route::prefix('ai')->name('ai.')->group(function () {
        Route::get('/usage', [AiController::class, 'usage'])->name('usage');
        Route::get('/logs/{aiLog}', [AiController::class, 'show'])->name('logs.show');
        Route::post('/summary', [AiController::class, 'generateSummary'])->name('summary');
        Route::post('/experience/improve', [AiController::class, 'improveExperience'])->name('experience.improve');
        Route::post('/skills/improve', [AiController::class, 'improveSkills'])->name('skills.improve');
        Route::post('/grammar-fix', [AiController::class, 'grammarFix'])->name('grammar-fix');
        Route::post('/cover-letter', [AiController::class, 'generateCoverLetter'])
            ->middleware('premium')
            ->name('cover-letter');
    });

    Route::get('/cv-templates', [CvTemplateRenderController::class, 'catalog'])->name('cv-templates.catalog');
    Route::get('/cv-templates/{cvTemplate:slug}/preview', [CvTemplatePreviewController::class, 'show'])->name('cv-templates.preview');
    Route::get('/cover-letter-templates/{cvTemplate:slug}/preview', [CoverLetterController::class, 'preview'])->name('cover-letter-templates.preview');

    // Cover Letters — always linked to a specific CV.
    Route::get('/cvs/{cv}/cover-letters/create', [CoverLetterController::class, 'create'])->name('cvs.cover-letters.create');
    Route::post('/cvs/{cv}/cover-letters', [CoverLetterController::class, 'store'])->name('cvs.cover-letters.store');

    Route::get('/cover-letters/{coverLetter}/edit', [CoverLetterBuilderController::class, 'edit'])->name('cover-letters.edit');
    Route::put('/cover-letters/{coverLetter}', [CoverLetterBuilderController::class, 'update'])->name('cover-letters.update');
    Route::patch('/cover-letters/{coverLetter}/autosave', [CoverLetterBuilderController::class, 'autosave'])->name('cover-letters.autosave');
    Route::post('/cover-letters/{coverLetter}/render', [CoverLetterBuilderController::class, 'render'])->name('cover-letters.render');
    Route::post('/cover-letters/{coverLetter}/ai', [CoverLetterAiController::class, 'generate'])
        ->middleware('premium')
        ->name('cover-letters.ai');
    Route::post('/cover-letters/{coverLetter}/duplicate', [CoverLetterController::class, 'duplicate'])->name('cover-letters.duplicate');
    Route::delete('/cover-letters/{coverLetter}', [CoverLetterController::class, 'destroy'])->name('cover-letters.destroy');

    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::get('/checkout/{plan}', [SubscriptionController::class, 'checkout'])->name('checkout');
        Route::post('/checkout/{plan}', [SubscriptionController::class, 'pay'])->name('pay');
        Route::get('/success', [SubscriptionController::class, 'success'])->name('success');
        Route::get('/failed', [SubscriptionController::class, 'failed'])->name('failed');
        Route::get('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
    });

    Route::post('/coupon/validate', [CouponController::class, 'check'])->name('coupon.validate');
    Route::post('/coupon/remove', [CouponController::class, 'remove'])->name('coupon.remove');
});

Route::post('/payfast/itn', [PayFastController::class, 'itn'])->name('payfast.itn');

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/cvs', [DashboardController::class, 'cvs'])->name('dashboard.cvs');
    Route::get('/cover-letters', [DashboardController::class, 'coverLetters'])->name('dashboard.cover-letters');
    Route::get('/subscription', [DashboardController::class, 'subscription'])->name('dashboard.subscription');
    Route::get('/billing', [DashboardController::class, 'billing'])->name('dashboard.billing');
    Route::get('/profile', [DashboardController::class, 'profile'])->name('dashboard.profile');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', fn () => redirect()->route('dashboard.profile'))->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::delete('/profile/photo', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
