<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// DashboardController handles the logic for displaying the admin dashboard.
// It retrieves statistics and summary data from the API and passes them to the dashboard view.
// This controller is responsible for preparing all data visualizations and metrics shown on the main admin page.

class DashboardController extends BaseController
{
    /**
     * Display the dashboard with statistics and summary data.
     *
     * Calls the API endpoint '/dashboard/statistics' to fetch user, division, position, and role counts,
     * as well as recent user and grouping data. Handles API errors gracefully and passes all relevant
     * data to the dashboard view for rendering.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch dashboard statistics from the API
        $response = $this->apiGet('/dashboard/statistics');
        
        // Check if the API response was successful
        if (!isset($response['success']) || !$response['success']) {
            // If not, show the dashboard view with an error message
            return view('dashboard')->with('error', $response['message'] ?? 'Failed to load dashboard data');
        }
        
        // Extract and prepare data for the dashboard view
        $data = [
            'userCount' => $response['data']['total_users'] ?? 0,
            'activeUserCount' => $response['data']['active_users'] ?? 0,
            'divisionCount' => $response['data']['total_divisions'] ?? 0,
            'positionCount' => $response['data']['total_positions'] ?? 0,
            'roleCount' => $response['data']['total_roles'] ?? 0,
            'newUsersThisMonth' => count($response['data']['recent_users'] ?? []),
            'usersPerDivision' => collect($response['data']['users_by_division'] ?? [])->map(function($item) {
                return (object) [
                    'div_name' => $item['name'] ?? 'Unknown',
                    'user_count' => $item['count'] ?? 0
                ];
            }),
            'usersPerPosition' => collect($response['data']['users_by_position'] ?? [])->map(function($item) {
                return (object) [
                    'pos_name' => $item['name'] ?? 'Unknown',
                    'user_count' => $item['count'] ?? 0
                ];
            })
        ];
        
        // Return the dashboard view with the prepared data
        return view('dashboard', $data);
    }
}