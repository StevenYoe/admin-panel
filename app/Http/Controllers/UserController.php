<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $response = $this->apiGet('/users');
        
        // Check if the response is valid and has data
        if (!isset($response['success']) || !$response['success']) {
            return view('users.index')->with('error', $response['message'] ?? 'Failed to fetch users');
        }
        
        // Extract data properly from the response
        $users = $response['data']['data'] ?? []; // Note: Pagination structure has a 'data' key
        $pagination = $response['data'] ?? [];
        
        return view('users.index', compact('users', 'pagination'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Get divisions for dropdown
        $divisionsResponse = $this->apiGet('/divisions/all'); // Use 'all' endpoint for simpler list
        $divisionsData = $divisionsResponse['data'] ?? [];
        
        // Transform to ID => Name format
        $divisions = [];
        if (is_array($divisionsData)) {
            foreach ($divisionsData as $division) {
                if (isset($division['div_id']) && isset($division['div_name'])) {
                    $divisions[$division['div_id']] = $division['div_name'];
                }
            }
        }
        
        // Get positions for dropdown
        $positionsResponse = $this->apiGet('/positions/all'); // Use 'all' endpoint
        $positionsData = $positionsResponse['data'] ?? [];
        $positions = [];
        if (is_array($positionsData)) {
            foreach ($positionsData as $position) {
                if (isset($position['pos_id']) && isset($position['pos_name'])) {
                    $positions[$position['pos_id']] = $position['pos_name'];
                }
            }
        }
        
        // Get roles for dropdown
        $rolesResponse = $this->apiGet('/roles/all'); // Use 'all' endpoint
        $rolesData = $rolesResponse['data'] ?? [];
        $roles = [];
        if (is_array($rolesData)) {
            foreach ($rolesData as $role) {
                if (isset($role['role_id']) && isset($role['role_name'])) {
                    $roles[$role['role_id']] = $role['role_name'];
                }
            }
        }
        
        // Get user roles if in edit mode
        $userRoles = [];
        
        return view('users.create', compact('divisions', 'positions', 'roles', 'userRoles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $response = $this->apiPost('/users', $request->all());
            
            if (!isset($response['success']) || !$response['success']) {
                return back()
                    ->withInput()
                    ->withErrors($response['errors'] ?? [])
                    ->with('error', $response['message'] ?? 'Failed to create user');
            }
            
            return redirect()->route('users.index')
                ->with('success', $response['message'] ?? 'User created successfully');
        } catch (\Exception $e) {
            \Log::error('Exception in user creation: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'An error occurred while creating the user. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $response = $this->apiGet("/users/{$id}");
        
        if (!isset($response['success']) || !$response['success']) {
            return redirect()->route('users.index')
                ->with('error', $response['message'] ?? 'User not found');
        }
        
        $user = $response['data'];
        
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        // Get user
        $userResponse = $this->apiGet("/users/{$id}");
        
        if (!isset($userResponse['success']) || !$userResponse['success']) {
            return redirect()->route('users.index')
                ->with('error', $userResponse['message'] ?? 'User not found');
        }
        
        $user = $userResponse['data'];
        
        // Get user roles
        $userRoles = [];
        if (isset($user['roles']) && is_array($user['roles'])) {
            foreach ($user['roles'] as $role) {
                if (isset($role['role_id'])) {
                    $userRoles[] = $role['role_id'];
                }
            }
        }
        
        // Get divisions for dropdown
        $divisionsResponse = $this->apiGet('/divisions/all');
        $divisionsData = $divisionsResponse['data'] ?? [];
        $divisions = [];
        if (is_array($divisionsData)) {
            foreach ($divisionsData as $division) {
                if (isset($division['div_id']) && isset($division['div_name'])) {
                    $divisions[$division['div_id']] = $division['div_name'];
                }
            }
        }
        
        // Get positions for dropdown
        $positionsResponse = $this->apiGet('/positions/all');
        $positionsData = $positionsResponse['data'] ?? [];
        $positions = [];
        if (is_array($positionsData)) {
            foreach ($positionsData as $position) {
                if (isset($position['pos_id']) && isset($position['pos_name'])) {
                    $positions[$position['pos_id']] = $position['pos_name'];
                }
            }
        }
        
        // Get roles for dropdown
        $rolesResponse = $this->apiGet('/roles/all');
        $rolesData = $rolesResponse['data'] ?? [];
        $roles = [];
        if (is_array($rolesData)) {
            foreach ($rolesData as $role) {
                if (isset($role['role_id']) && isset($role['role_name'])) {
                    $roles[$role['role_id']] = $role['role_name'];
                }
            }
        }
        
        return view('users.edit', compact('user', 'divisions', 'positions', 'roles', 'userRoles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $response = $this->apiPut("/users/{$id}", $request->all());
        
        if (!isset($response['success']) || !$response['success']) {
            return back()
                ->withInput()
                ->withErrors(['message' => $response['message'] ?? 'Failed to update user']);
        }
        
        return redirect()->route('users.show', $id)
            ->with('success', $response['message'] ?? 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $response = $this->apiDelete("/users/{$id}");
        
        if (!isset($response['success']) || !$response['success']) {
            return back()->with('error', $response['message'] ?? 'Failed to delete user');
        }
        
        return redirect()->route('users.index')
            ->with('success', $response['message'] ?? 'User deleted successfully');
    }
}