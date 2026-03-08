<?php

use App\Http\Controllers\Web\ActivityContractController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\EntranceContractController;
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
    Route::get("/", fn() => redirect()->route("hotel-contracts.index"));
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
