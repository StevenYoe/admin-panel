<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Division;
use App\Models\Position;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get basic counts
        $userCount = User::count();
        $roleCount = Role::count();
        $divisionCount = Division::count();
        $positionCount = Position::count();
        
        // Count active users
        $activeUserCount = User::where('u_is_active', true)->count();
        
        // Count new users this month
        $now = Carbon::now();
        $startOfMonth = $now->startOfMonth();
        $newUsersThisMonth = User::where('u_created_at', '>=', $startOfMonth)->count();
        
        // Get users per division with percentages
        $usersPerDivision = Division::select(
            'div_id',
            'div_name',
            DB::raw('COUNT(u.u_id) as user_count'),
            DB::raw('CASE WHEN (SELECT COUNT(*) FROM login.users) > 0 
                     THEN (COUNT(u.u_id) * 100.0 / (SELECT COUNT(*) FROM login.users)) 
                     ELSE 0 END as percentage')
        )
        ->leftJoin('login.users as u', 'login.divisions.div_id', '=', 'u.u_division_id')
        ->where('div_is_active', true)
        ->groupBy('div_id', 'div_name')
        ->orderByDesc('user_count')
        ->get();
        
        // Get users per position with percentages
        $usersPerPosition = Position::select(
            'pos_id',
            'pos_name',
            DB::raw('COUNT(u.u_id) as user_count'),
            DB::raw('CASE WHEN (SELECT COUNT(*) FROM login.users) > 0 
                     THEN (COUNT(u.u_id) * 100.0 / (SELECT COUNT(*) FROM login.users)) 
                     ELSE 0 END as percentage')
        )
        ->leftJoin('login.users as u', 'login.positions.pos_id', '=', 'u.u_position_id')
        ->where('pos_is_active', true)
        ->groupBy('pos_id', 'pos_name')
        ->orderByDesc('user_count')
        ->get();
            
        return view('dashboard', compact(
            'userCount',
            'roleCount',
            'divisionCount',
            'positionCount',
            'activeUserCount',
            'newUsersThisMonth',
            'usersPerDivision',
            'usersPerPosition'
        ));
    }
}