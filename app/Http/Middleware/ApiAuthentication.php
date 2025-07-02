<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

// ApiAuthentication middleware ensures that only authenticated users can access protected routes.
// It checks for the presence of an authentication token in the session and redirects unauthenticated
// users to the login page with a warning message. This middleware is applied to routes that require login.

class ApiAuthentication
{
    /**
     * Handle an incoming request.
     *
     * Checks if the session contains an authentication token. If not, flashes a warning
     * message and redirects the user to the login page. Otherwise, allows the request to proceed.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if there is no authentication token in the session
        if (!Session::has('auth_token')) {
            Session::flash('swal_type', 'warning');
            Session::flash('swal_title', 'Authentication Required');
            Session::flash('swal_msg', 'Please login to access this page.');
            return redirect()->route('login');
        }

        // Allow the request to proceed if authenticated
        return $next($request);
    }
}