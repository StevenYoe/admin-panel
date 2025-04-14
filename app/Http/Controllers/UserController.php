<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\LengthAwarePaginator;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
{
    $params = [
        'sort_by' => $request->input('sort_by', 'u_id'),
        'sort_order' => $request->input('sort_order', 'asc'),
        'per_page' => $request->input('per_page', 10)
    ];

    $response = $this->apiGet('/users', $params);
    
    // Check if the response is valid and has data
    if (!isset($response['success']) || !$response['success']) {
        return view('users.index')->with('error', $response['message'] ?? 'Failed to fetch users');
    }
    
    $users = $response['data']['data'] ?? [];
    
    // Create a proper paginator instance if we have the necessary pagination data
    $paginator = null;
    if (isset($response['data'])) {
        $paginationData = $response['data'];
        if (isset($paginationData['current_page']) && isset($paginationData['per_page']) && isset($paginationData['total'])) {
            $paginator = new LengthAwarePaginator(
                $users,
                $paginationData['total'],
                $paginationData['per_page'],
                $paginationData['current_page'],
                [
                    'path' => request()->url(),
                    'query' => $request->query()
                ]
            );
        }
    }

    $sortBy = $params['sort_by'];
    $sortOrder = $params['sort_order'];
    
    // Check if this is an AJAX request
    if ($request->ajax() || $request->has('ajax')) {
        return view('users.table-content', compact('users', 'paginator', 'sortBy', 'sortOrder'))->render();
    }
    
    return view('users.index', compact('users', 'paginator', 'sortBy', 'sortOrder'));
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
            $data = $request->all();
            
            // Handle file upload for API request
            if ($request->hasFile('u_profile_image')) {
                $data['u_profile_image'] = $request->file('u_profile_image');
            }
            
            $response = $this->apiPost('/users', $data);
            
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
        try {
            $data = $request->all();
            
            // Handle file upload for API request
            if ($request->hasFile('u_profile_image')) {
                $data['u_profile_image'] = $request->file('u_profile_image');
            }
            
            $response = $this->apiPut("/users/{$id}", $data);
            
            if (!isset($response['success']) || !$response['success']) {
                return back()
                    ->withInput()
                    ->withErrors($response['errors'] ?? [])
                    ->with('error', $response['message'] ?? 'Failed to update user');
            }
            
            return redirect()->route('users.show', $id)
                ->with('success', $response['message'] ?? 'User updated successfully');
        } catch (\Exception $e) {
            \Log::error('Exception in user update: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'An error occurred while updating the user. Please try again.');
        }
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