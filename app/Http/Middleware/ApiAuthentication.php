<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ApiAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek jika tidak ada token
        if (!Session::has('auth_token')) {
            Session::flash('swal_type', 'warning');
            Session::flash('swal_title', 'Authentication Required');
            Session::flash('swal_msg', 'Please login to access this page.');
            return redirect()->route('login');
        }

        return $next($request);
    }
}