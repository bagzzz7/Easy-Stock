<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     */
    
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $messages = [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
            'email.unique' => 'This email is already registered. Please login or use a different email.',
            'phone.required' => 'Phone number is required.',
            'phone.unique' => 'This phone number is already registered.',
            'address.required' => 'Address is required.',
            'terms.required' => 'You must agree to the Terms of Service.',
        ];

        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            ],
            'phone' => ['required', 'string', 'max:20', 'unique:users'],
            'address' => ['required', 'string', 'max:500'],
            'terms' => ['required', 'accepted'],
        ], $messages);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // First user becomes administrator, others become pharmacist
        $isFirstUser = User::count() === 0;
        
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $isFirstUser ? 'administrator' : 'pharmacist', // Changed to pharmacist
            'phone' => $data['phone'],
            'address' => $data['address'],
            'is_active' => true,
        ]);
    }

    /**
     * OVERRIDE: Handle a registration request for the application.
     * Redirect to landing page with success message and auto-switch to login tab
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        try {
            // Validate the request
            $this->validator($request->all())->validate();

            // Create the user
            $user = $this->create($request->all());

            // Send email verification if enabled (optional)
            // $user->sendEmailVerificationNotification();

            // Return to landing page with success message and registered email
            return redirect()->route('landing')->with([
                'success' => '✅ Account created successfully! You can now login with your credentials.',
                'registered_email' => $request->email,
                'registered_name' => $request->name,
                'show_login' => true // Flag to automatically show login tab
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return back with validation errors
            return redirect()->route('landing')
                ->withErrors($e->errors())
                ->withInput($request->except('password', 'password_confirmation', 'terms'));
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Registration error: ' . $e->getMessage());
            
            // Return back with error message
            return redirect()->route('landing')
                ->with('error', 'Registration failed: ' . $e->getMessage())
                ->withInput($request->except('password', 'password_confirmation', 'terms'));
        }
    }

    /**
     * Show the application registration form.
     * Redirect to landing page instead of auth.register view
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function showRegistrationForm()
    {
        return redirect()->route('landing');
    }
}