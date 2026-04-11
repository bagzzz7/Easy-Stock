<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Maximum number of login attempts.
     *
     * @var int
     */
    protected $maxAttempts = 3;

    /**
     * Lockout time in minutes.
     *
     * @var int
     */
    protected $decayMinutes = 15;

    /**
     * Create a new controller instance.
     */
    

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('landing');
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        return redirect()->intended($this->redirectPath())
            ->with('success', 'Welcome back, ' . $user->name . '!');
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
   protected function sendFailedLoginResponse(Request $request)
{
    // Check if the email exists in the database
    $user = \App\Models\User::where('email', $request->email)->first();

    if (!$user) {
        // Email doesn't exist
        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->with('error_type', 'email');
    }

    // Email exists but password is wrong
    return redirect()->back()
        ->withInput($request->only($this->username(), 'remember'))
        ->with('error_type', 'password');
}
    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out successfully!');
    }
}