<?php

use App\Http\Controllers\Web\ActivityContractController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\EntranceContractController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\HotelContractController;
use App\Http\Controllers\Web\RestaurantContractController;
use App\Http\Controllers\Web\ImportController;
use App\Http\Controllers\Web\PaymentSlipController;
use App\Http\Controllers\Web\TransportContractController;
use Illuminate\Support\Facades\Route;

Route::get("/lang/{locale}", function ($locale) {
    if (in_array($locale, ["en", "id"])) {
        session(["locale" => $locale]);
    }
    return redirect()->back();
})->name("lang.switch");

Route::post("/set-timezone", function(\Illuminate\Http\Request $r) {
    session(["user_timezone" => $r->timezone]);
    return response()->json(["ok" => true]);
})->name("set.timezone");

Route::middleware("guest")->group(function () {
    Route::get("/login", [AuthController::class, "showLogin"])->name("login");
    Route::post("/login", [AuthController::class, "login"])->name("login.post");
});

Route::post("/logout", [AuthController::class, "logout"])
    ->middleware("auth")
    ->name("logout");

Route::middleware("auth")->group(function () {
    Route::get("/", [DashboardController::class, "index"])->name("dashboard");
    Route::resource("hotel-contracts", HotelContractController::class);
    Route::resource("transport-contracts", TransportContractController::class);
    Route::resource("activity-contracts", ActivityContractController::class);
    Route::resource("entrance-contracts", EntranceContractController::class);
    Route::resource("restaurant-contracts", RestaurantContractController::class);
    Route::get('imports', [ImportController::class, 'index'])->name('imports.index');
    Route::get('imports/template/{module}', [ImportController::class, 'downloadTemplate'])->name('imports.template');
    Route::post('imports/upload', [ImportController::class, 'upload'])->name('imports.upload');
    Route::get('payment-slips', [PaymentSlipController::class, 'index'])->name('payment-slips.index');
    Route::get('payment-slips/download/{fileId}', [PaymentSlipController::class, 'download'])->name('payment-slips.download');
    Route::post('payment-slips/mark-read', [PaymentSlipController::class, 'markAllRead'])->name('payment-slips.mark-read');
    Route::get('payment-slips/notifications', [PaymentSlipController::class, 'notifications'])->name('payment-slips.notifications');
    Route::get('payment-slips/archive', [PaymentSlipController::class, 'archive'])->name('payment-slips.archive');
    Route::get('payment-slips/preview/{fileId}', [PaymentSlipController::class, 'preview'])->name('payment-slips.preview');
});

// Guide Fee
Route::resource('guide-languages', \App\Http\Controllers\Web\GuideLanguageController::class);
Route::post('guide-languages/{guideLanguage}/services', [\App\Http\Controllers\Web\GuideLanguageController::class, 'storeService'])->name('guide-languages.services.store');
Route::delete('guide-languages/{guideLanguage}/services/{service}', [\App\Http\Controllers\Web\GuideLanguageController::class, 'destroyService'])->name('guide-languages.services.destroy');
Route::get('guide-languages/api/rate', [\App\Http\Controllers\Web\GuideLanguageController::class, 'apiRate'])->name('guide-languages.api.rate');
Route::put('guide-languages/{guideLanguage}/info', [\App\Http\Controllers\Web\GuideLanguageController::class, 'updateInfo'])->name('guide-languages.update-info');
Route::put('guide-languages/{guideLanguage}/services/{service}', [\App\Http\Controllers\Web\GuideLanguageController::class, 'updateService'])->name('guide-languages.services.update');
Route::delete('guide-languages/{guideLanguage}/tiers/{tier}', [\App\Http\Controllers\Web\GuideLanguageController::class, 'destroyTier'])->name('guide-languages.tiers.destroy');
Route::post('guide-languages/{guideLanguage}/services/{service}/tiers', [\App\Http\Controllers\Web\GuideLanguageController::class, 'storeTier'])->name('guide-languages.tiers.store');

// Tour Package Costing
Route::resource('tour-packages', \App\Http\Controllers\Web\TourPackageController::class);
Route::get('tour-packages-api/hotel-rooms', [\App\Http\Controllers\Web\TourPackageController::class, 'apiHotelRooms'])->name('tour-packages.api.hotel-rooms');
Route::get('tour-packages-api/hotel-surcharge', [\App\Http\Controllers\Web\TourPackageController::class, 'apiHotelSurcharge'])->name('tour-packages.api.hotel-surcharge');

Route::get('tour-packages-api/transport-contracts', [App\Http\Controllers\Web\TourPackageController::class, 'apiTransportContracts']);
Route::get('tour-packages-api/transport-vehicles', [App\Http\Controllers\Web\TourPackageController::class, 'apiTransportVehicles']);
Route::get('tour-packages-api/transport-rates', [App\Http\Controllers\Web\TourPackageController::class, 'apiTransportRates']);
Route::get('tour-packages-api/guide-languages', [App\Http\Controllers\Web\TourPackageController::class, 'apiGuideLanguages']);
Route::get('tour-packages-api/guide-services', [App\Http\Controllers\Web\TourPackageController::class, 'apiGuideServices']);
Route::get('tour-packages-api/guide-rate', [App\Http\Controllers\Web\TourPackageController::class, 'apiGuideRate']);

Route::get('tour-packages-api/restaurant-menus', [App\Http\Controllers\Web\TourPackageController::class, 'apiRestaurantMenus']);
Route::get('tour-packages-api/restaurant-menu-detail', [App\Http\Controllers\Web\TourPackageController::class, 'apiRestaurantMenuDetail']);
Route::post('tour-packages-api/generate-brief', [App\Http\Controllers\Web\TourPackageController::class, 'apiGenerateBrief'])->name('tour-packages.api.generate-brief');
