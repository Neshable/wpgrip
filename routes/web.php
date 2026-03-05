<?php

use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\PaymentProviders\PaddleController as PaddleController;
use App\Services\SessionManager;
use App\Services\TenantCreationManager;
use App\Services\UserDashboardManager;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WebhookController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
| If you want the URL to be added to the sitemap, add a "sitemapped" middleware to the route (it has to GET route)
|
*/

// Home page - public
Route::get('/', function () {
    return view('home');
})->name('home');



Route::get('/pricing', function () {
    return view('pages.pricing');
})->name('pricing');

Route::get('/features', function () {
    return view('pages.features');
})->name('features');

// Normal dashboard
Route::get('/dashboard', function (UserDashboardManager $dashboardManager) {
    return redirect($dashboardManager->getUserDashboardUrl(Auth::user()));
})->name('dashboard')->middleware('auth');

// Route to email verification
Route::get('/verify-email', function () {
    return view('auth.not-verified');
})->name('not-verified')->middleware('auth');

// Alternative dasboard if plan is selected
Route::get('/get-started', function () {
    return view('pages.get-started');
})->name('get-started')->middleware('auth');


Auth::routes(['register' => false]);

// Stricter rate-limit on the login POST on top of ThrottlesLogins trait
Route::middleware('throttle:login')->group(function () {
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
});

Route::get('/plan/start', function (
    TenantCreationManager $tenantCreationManager,
    SessionManager $sessionManager
) {
    if (! auth()->check()) {
        $sessionManager->setCreateTenantForFreePlanUser(true);
    } else {
        $tenantCreationManager->createTenantForFreePlanUser(auth()->user());
    }

    return redirect()->route('login');
})->name('plan.start');

Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    $user = $request->user();
    if ($user->hasVerifiedEmail()) {
        return redirect()->route('registration.thank-you');
    }

    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::get('/phone/verify', function () {
    return view('verify.sms-verification');
})->name('user.phone-verify')
    ->middleware('auth');

Route::get('/phone/verified', function () {
    return view('verify.sms-verification-success');
})->name('user.phone-verified')
    ->middleware('auth');

Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/registration/thank-you', function () {
    return view('auth.thank-you');
})->middleware('auth')->name('registration.thank-you');

Route::get('/auth/{provider}/redirect', [OAuthController::class, 'redirect'])
    ->where('provider', 'google|github|facebook|twitter-oauth-2|linkedin-openid|bitbucket|gitlab')
    ->name('auth.oauth.redirect');

Route::get('/auth/{provider}/callback', [OAuthController::class, 'callback'])
    ->where('provider', 'google|github|facebook|twitter-oauth-2|linkedin-openid|bitbucket|gitlab')
    ->name('auth.oauth.callback');

Route::get('/checkout/plan/{planSlug}', [
    App\Http\Controllers\SubscriptionCheckoutController::class,
    'subscriptionCheckout',
])->name('checkout.subscription');

Route::get('/checkout/convert-subscription/{subscriptionUuid}', [
    App\Http\Controllers\SubscriptionCheckoutController::class,
    'convertLocalSubscriptionCheckout',
])->name('checkout.convert-local-subscription');

Route::get('/already-subscribed', function () {
    return view('checkout.already-subscribed');
})->name('checkout.subscription.already-subscribed');

Route::get('/checkout/subscription/success', [
    App\Http\Controllers\SubscriptionCheckoutController::class,
    'subscriptionCheckoutSuccess',
])->name('checkout.subscription.success')->middleware('auth');

Route::get('/checkout/convert-subscription-success', [
    App\Http\Controllers\SubscriptionCheckoutController::class,
    'convertLocalSubscriptionCheckoutSuccess',
])->name('checkout.convert-local-subscription.success')->middleware('auth');

Route::get('/payment-provider/paddle/payment-link', [
    PaddleController::class,
    'paymentLink',
])->name('payment-link.paddle');

Route::get('/subscription/{subscriptionUuid}/change-plan/{planSlug}/tenant/{tenantUuid}', [
    App\Http\Controllers\SubscriptionController::class,
    'changePlan',
])->name('subscription.change-plan')->middleware('auth');

Route::post('/subscription/{subscriptionUuid}/change-plan/{planSlug}/tenant/{tenantUuid}', [
    App\Http\Controllers\SubscriptionController::class,
    'changePlan',
])->name('subscription.change-plan.post')->middleware('auth');

Route::get('/subscription/change-plan-thank-you', [
    App\Http\Controllers\SubscriptionController::class,
    'success',
])->name('subscription.change-plan.thank-you')->middleware('auth');

// blog
// Route::get('/blog/{slug}', [
//     App\Http\Controllers\BlogController::class,
//     'view',
// ])->name('blog.view');

// Route::get('/blog', [
//     App\Http\Controllers\BlogController::class,
//     'all',
// ])->name('blog')->middleware('sitemapped');

// Route::get('/blog/category/{slug}', [
//     App\Http\Controllers\BlogController::class,
//     'category',
// ])->name('blog.category');

Route::get('/terms-of-service', function () {
    return view('pages.terms-of-service');
})->name('terms-of-service');

Route::get('/privacy-policy', function () {
    return view('pages.privacy-policy');
})->name('privacy-policy');

// Product checkout routes

Route::get('/buy/product/{productSlug}/{quantity?}', [
    App\Http\Controllers\ProductCheckoutController::class,
    'addToCart',
])->name('buy.product');

Route::get('/cart/clear', [
    App\Http\Controllers\ProductCheckoutController::class,
    'clearCart',
])->name('cart.clear');

Route::get('/checkout/product', [
    App\Http\Controllers\ProductCheckoutController::class,
    'productCheckout',
])->name('checkout.product');

Route::get('/checkout/product/success', [
    App\Http\Controllers\ProductCheckoutController::class,
    'productCheckoutSuccess',
])->name('checkout.product.success')->middleware('auth');

// roadmap disabled

Route::get('/refund-policy', function () {
    return view('pages.refund-policy');
})->name('refund-policy');

// Invitations

Route::get('/invitations', [
    App\Http\Controllers\InvitationController::class,
    'index',
])->name('invitations')->middleware('auth');

// Invoice

Route::get('/invoice/generate/{transactionUuid}', [
    App\Http\Controllers\InvoiceController::class,
    'generate',
])->name('invoice.generate');

Route::get('/invoice/preview', [
    App\Http\Controllers\InvoiceController::class,
    'preview',
])->name('invoice.preview');

// Repo webhook trigger
Route::post('/webhook/git/{unique_token}', [ WebhookController::class, 'handleWebhook' ])
    ->middleware('throttle:30,1');
