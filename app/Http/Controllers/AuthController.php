<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends BaseController
{
    /**
     * Show login form
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        // Add SweetAlert welcome message if not coming from a redirect
        if (!session()->has('swal_msg')) {
            Session::flash('swal_type', 'info');
            Session::flash('swal_title', 'Welcome');
            Session::flash('swal_msg', 'Please login to access the admin panel.');
        }
        
        return view('auth.login');
    }

    /**
     * Handle login request
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

        // Call API to authenticate
        $response = $this->apiPost('/login', $credentials);
        
        if (!isset($response['success']) || !$response['success']) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['message' => $response['message'] ?? 'Invalid credentials']);
        }

        // Store token and user data in session
        Session::put('auth_token', $response['data']['token']);
        Session::put('user', $response['data']['user']);
        Session::put('roles', $response['data']['roles'] ?? []);

        // Add SweetAlert message
        Session::flash('swal_type', 'success');
        Session::flash('swal_title', 'Login Successful');
        Session::flash('swal_msg', 'Welcome back, ' . $response['data']['user']['u_name'] . '!');

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Handle logout request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        // Call API to logout
        $this->apiPost('/auth/logout');

        // Clear session data
        Session::forget(['auth_token', 'user', 'roles']);

        // Add SweetAlert message
        Session::flash('swal_type', 'success');
        Session::flash('swal_title', 'Logged Out');
        Session::flash('swal_msg', 'You have been successfully logged out.');

        return redirect()->route('login');
    }
    
}