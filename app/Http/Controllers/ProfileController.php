<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

// ProfileController handles displaying the authenticated user's profile.
// It retrieves user data from the session and, if needed, fetches the latest
// profile details from the API to ensure up-to-date information is shown.

class ProfileController extends BaseController
{
    /**
     * Display the user's profile page.
     *
     * Gets user data from the session and attempts to refresh it from the API.
     * Passes the user data to the profile view for rendering.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get user data from session
        $user = Session::get('user');
        
        // Optionally fetch the latest user details from the API
        $response = $this->apiGet('/me');
        
        if (isset($response['success']) && $response['success']) {
            $user = $response['data'];
        }
        
        // Return the profile view with user data
        return view('profile.index', compact('user'));
    }
}