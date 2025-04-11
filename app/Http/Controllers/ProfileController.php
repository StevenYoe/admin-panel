<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProfileController extends BaseController // Change to extend BaseController
{
    /**
     * Display the user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get user data from session
        $user = Session::get('user');
        
        // If we need more details, we can fetch from API
        $response = $this->apiGet('/me');
        
        if (isset($response['success']) && $response['success']) {
            $user = $response['data'];
        }
        
        return view('profile.index', compact('user'));
    }
}