<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\LoginManager;
use App\Validator\LoginValidator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Max failed attempts before lockout (default in trait: 5).
     */
    protected int $maxAttempts = 5;

    /**
     * Lockout window in minutes (default in trait: 1).
     * Raised to 15 to deter brute-force.
     */
    protected int $decayMinutes = 15;

    //    /**
    //     * Where to redirect users after login.
    //     *
    //     * @var string
    //     */
    //    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct(
        private LoginValidator $loginValidator,
        private LoginManager $loginManager,
    ) {
        $this->middleware('guest')->except('logout');
    }

    public function redirectPath()
    {
        return Redirect::getIntendedUrl() ?? route('dashboard');
    }

    public function showLoginForm()
    {
        // Only set intended URL if it was a meaningful page (not home/login/register)
        if (Redirect::getIntendedUrl() === null) {
            $previous = url()->previous();
            $ignoredUrls = [
                route('home'),
                route('login'),
                route('register'),
                url('/'),
            ];

            if (! in_array($previous, $ignoredUrls)) {
                Redirect::setIntendedUrl($previous);
            }
        }

        return view('auth.login');
    }

    protected function authenticated(Request $request, $user)
    {
        if ($user->is_blocked) {
            $this->guard()->logout();

            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been blocked. Please contact support.',
            ]);
        }

        return redirect()->intended(route('dashboard'));
    }

    protected function validateLogin(Request $request)
    {
        $this->loginValidator->validateRequest($request);
    }

    protected function attemptLogin(Request $request)
    {
        return $this->loginManager->attempt($this->credentials($request), $request->boolean('remember'));
    }
}
