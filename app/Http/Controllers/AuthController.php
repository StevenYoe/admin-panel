<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

// AuthController handles user authentication logic for the admin panel.
// It provides methods for showing the login form, processing login requests,
// and handling user logout. All authentication-related session and API logic
// is centralized here for maintainability and clarity.

class AuthController extends BaseController
{
    /**
     * Show login form
     *
     * Displays the login page and sets a welcome message using SweetAlert
     * if the user is not coming from a redirect. This helps guide users
     * to log in before accessing the admin panel.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        // Add SweetAlert welcome message if not coming from a redirect
        if (!session()->has('swal_msg')) {
            Session::flash('swal_type', 'info');
            Session::flash('swal_title', 'Welcome');
            Session::flash('swal_msg', 'Please login to access the user admin panel.');
        }
        
        return view('auth.login');
    }

    /**
     * Handle login request
     *
     * Validates user credentials, sends them to the API for authentication,
     * and manages session data on success or failure. Displays appropriate
     * SweetAlert messages for user feedback.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        // Convert email to lowercase before sending to API
        $credentials['email'] = strtolower($credentials['email']);
    
        // Call API to authenticate
        $response = $this->apiPost('/login', $credentials);
        
        \Log::info('Login API Response:', ['response' => $response]);
        
        if (!isset($response['success']) || !$response['success']) {
            // Handle failed login attempts and display user-friendly error messages
            $errorTitle = 'Login Failed';
            $errorMsg = 'An error occurred while trying to log in. Please try again.';
            
            if (isset($response['message'])) {
                $message = $response['message'];
                
                if ($message === 'User does not exist or is not active') {
                    $errorTitle = 'Email Not Found';
                    $errorMsg = 'The email you entered is not registered in our system.';
                } 
                else if ($message === 'Invalid credentials') {
                    $errorTitle = 'Wrong Password';
                    $errorMsg = 'The password you entered is incorrect. Please try again.';
                }
                else {
                    $errorMsg = $message;
                }
            }
            
            Session::flash('swal_type', 'error');
            Session::flash('swal_title', $errorTitle);
            Session::flash('swal_msg', $errorMsg);
            
            return back()->withInput($request->only('email'));
        }

        // On successful login, store authentication data in session
        Session::put('auth_token', $response['data']['token']);
        Session::put('user', $response['data']['user']);
        Session::put('roles', $response['data']['roles'] ?? []);

        // Add SweetAlert message for successful login
        Session::flash('swal_type', 'success');
        Session::flash('swal_title', 'Login Successful');
        Session::flash('swal_msg', 'Welcome back, ' . $response['data']['user']['u_name'] . '!');

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Handle logout request
     *
     * Calls the API to log out the user, clears session data, and redirects
     * to the login page with a logout confirmation message.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        // Call API to logout
        $this->apiPost('/auth/logout');

        // Clear session data
        Session::forget(['auth_token', 'user', 'roles']);

        // Add SweetAlert message for successful logout
        Session::flash('swal_type', 'success');
        Session::flash('swal_title', 'Logged Out');
        Session::flash('swal_msg', 'You have been successfully logged out.');

        return redirect()->route('login');
    }
    
}