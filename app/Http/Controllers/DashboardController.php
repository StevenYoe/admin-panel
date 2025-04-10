<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    public function index()
{
    // Change from '/dashboard' to '/dashboard/statistics'
    $response = $this->apiGet('/dashboard/statistics');
    
    // Cek apakah response berhasil
    if (!isset($response['success']) || !$response['success']) {
        return view('dashboard')->with('error', $response['message'] ?? 'Gagal memuat data dashboard');
    }
    
    // Ekstrak data dari response untuk view
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
    
    return view('dashboard', $data);
}
}