<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

// RoleController manages CRUD operations for role resources in the admin panel.
// It communicates with the API to fetch, create, update, and delete role data,
// and enforces superadmin access control for sensitive actions.

class RoleController extends BaseController
{
    /**
     * Display a listing of the roles with pagination, sorting, and access control.
     *
     * Fetches role data from the API, builds a paginator, and passes all relevant
     * variables to the index view. Handles API errors gracefully and supports AJAX requests.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $params = [
            'sort_by' => $request->input('sort_by', 'role_id'),
            'sort_order' => $request->input('sort_order', 'asc'),
            'per_page' => $request->input('per_page', 10),
            'page' => $request->input('page', 1)
        ];
    
        $response = $this->apiGet('/roles', $params);
        
        if (!isset($response['success']) || !$response['success']) {
            return view('roles.index')->with('error', $response['message'] ?? 'Gagal memuat data role');
        }
        
        $roles = $response['data']['data'] ?? [];
        
        // Create a proper paginator instance if we have the necessary pagination data
        $paginator = null;
        if (isset($response['data'])) {
            $paginationData = $response['data'];
            if (isset($paginationData['current_page']) && isset($paginationData['per_page']) && isset($paginationData['total'])) {
                $paginator = new LengthAwarePaginator(
                    $roles,
                    $paginationData['total'],
                    $paginationData['per_page'],
                    $paginationData['current_page'],
                    [
                        'path' => request()->url(),
                        'query' => request()->query()
                    ]
                );
            }
        }

        // Add these variables to be consistent with your view
        $sortBy = $params['sort_by'];
        $sortOrder = $params['sort_order'];
        $isSuperAdmin = $this->isSuperAdmin();

        // If this is an AJAX request, return only the table content
        if ($request->ajax()) {
            return view('roles.table-content', compact('roles', 'paginator', 'sortBy', 'sortOrder', 'isSuperAdmin'))->render();
        }

        return view('roles.index', compact('roles', 'paginator', 'sortBy', 'sortOrder', 'isSuperAdmin'));
    }

    /**
     * Show the form for creating a new role.
     *
     * Only accessible to superadmins. Redirects unauthorized users.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check if user has superadmin access
        $redirect = $this->checkSuperAdminAccess('roles.index');
        if ($redirect !== true) {
            return $redirect;
        }
        
        return view('roles.create');
    }

    /**
     * Store a newly created role in storage.
     *
     * Validates input, checks superadmin access, and sends data to the API.
     * Handles API errors and redirects with appropriate messages.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Check if user has superadmin access
        $redirect = $this->checkSuperAdminAccess('roles.index');
        if ($redirect !== true) {
            return $redirect;
        }
        
        $validated = $request->validate([
            'role_name' => 'required|string|max:50',
            'role_level' => 'required|integer|min:0|max:100000',
            'role_is_active' => 'nullable|boolean'
        ]);
        
        // Handle checkbox boolean
        $validated['role_is_active'] = $request->has('role_is_active') ? true : false;
        
        // Kirim data ke API
        $response = $this->apiPost('/roles', $validated);
        
        if (!isset($response['success']) || !$response['success']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['message' => $response['message'] ?? 'Gagal menyimpan role']);
        }
        
        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil ditambahkan.');
    }

    /**
     * Display the specified role's details.
     *
     * Fetches role data from the API and passes it to the show view.
     * Also passes superadmin status for conditional UI rendering.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $response = $this->apiGet("/roles/{$id}");
        
        if (!isset($response['success']) || !$response['success']) {
            return redirect()->route('roles.index')
                ->with('error', $response['message'] ?? 'Role tidak ditemukan');
        }
        
        // Pass the superadmin status to view for conditionally showing edit/delete buttons
        $isSuperAdmin = $this->isSuperAdmin();
        
        return view('roles.show',  ['role' => $response['data']], compact('isSuperAdmin'));
    }

    /**
     * Show the form for editing the specified role.
     *
     * Only accessible to superadmins. Fetches role data for editing.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Check if user has superadmin access
        $redirect = $this->checkSuperAdminAccess('roles.index');
        if ($redirect !== true) {
            return $redirect;
        }
        
        $response = $this->apiGet("/roles/{$id}");
        
        if (!isset($response['success']) || !$response['success']) {
            return redirect()->route('roles.index')
                ->with('error', $response['message'] ?? 'Role tidak ditemukan');
        }
        
        return view('roles.edit', ['role' => $response['data']]);
    }

    /**
     * Update the specified role in storage.
     *
     * Validates input, checks superadmin access, and updates data via the API.
     * Handles API errors and redirects with appropriate messages.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Check if user has superadmin access
        $redirect = $this->checkSuperAdminAccess('roles.index');
        if ($redirect !== true) {
            return $redirect;
        }
        
        $validated = $request->validate([
            'role_name' => 'required|string|max:50',
            'role_level' => 'required|integer|min:0|max:100000',
            'role_is_active' => 'nullable|boolean'
        ]);
        
        // Handle checkbox boolean
        $validated['role_is_active'] = $request->has('role_is_active') ? true : false;
        
        // Kirim data ke API
        $response = $this->apiPut("/roles/{$id}", $validated);
        
        if (!isset($response['success']) || !$response['success']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['message' => $response['message'] ?? 'Gagal memperbarui role']);
        }
        
        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil diperbarui.');
    }

    /**
     * Remove the specified role from storage.
     *
     * Only accessible to superadmins. Deletes the role via the API and handles errors.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Check if user has superadmin access
        $redirect = $this->checkSuperAdminAccess('roles.index');
        if ($redirect !== true) {
            return $redirect;
        }
        
        $response = $this->apiDelete("/roles/{$id}");
        
        if (!isset($response['success']) || !$response['success']) {
            return redirect()->route('roles.index')
                ->with('error', $response['message'] ?? 'Role tidak dapat dihapus');
        }
        
        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil dihapus.');
    }
}