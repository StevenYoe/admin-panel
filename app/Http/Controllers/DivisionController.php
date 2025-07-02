<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

// DivisionController manages CRUD operations for division resources in the admin panel.
// It communicates with the API to fetch, create, update, and delete division data,
// and enforces superadmin access control for sensitive actions.

class DivisionController extends BaseController
{
    /**
     * Display a listing of the divisions with pagination, sorting, and access control.
     *
     * Fetches division data from the API, builds a paginator, and passes all relevant
     * variables to the index view. Handles API errors gracefully.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $params = [
            'sort_by' => $request->input('sort_by', 'div_id'),
            'sort_order' => $request->input('sort_order', 'asc'),
            'per_page' => $request->input('per_page', 10),
            'page' => $request->input('page', 1)
        ];

        $response = $this->apiGet('/divisions', $params);
        
        if (!isset($response['success']) || !$response['success']) {
            return view('divisions.index')->with('error', $response['message'] ?? 'Gagal memuat data divisi');
        }
        
        $divisions = $response['data']['data'] ?? [];
        
    
    // Create a proper paginator instance if we have the necessary pagination data
    $paginator = null;
    if (isset($response['data'])) {
        $paginationData = $response['data'];
        if (isset($paginationData['current_page']) && isset($paginationData['per_page']) && isset($paginationData['total'])) {
            $paginator = new LengthAwarePaginator(
                $divisions,
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
    
        // Make sure these variable names match what your view is expecting
        $sortBy = $params['sort_by'];
        $sortOrder = $params['sort_order'];
        $isSuperAdmin = $this->isSuperAdmin();

        return view('divisions.index', compact('divisions', 'paginator', 'sortBy', 'sortOrder', 'isSuperAdmin'));
    }

    /**
     * Show the form for creating a new division.
     *
     * Only accessible to superadmins. Redirects unauthorized users.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check if user has superadmin access
        $redirect = $this->checkSuperAdminAccess('divisions.index');
        if ($redirect !== true) {
            return $redirect;
        }
        
        return view('divisions.create');
    }

    /**
     * Store a newly created division in storage.
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
        $redirect = $this->checkSuperAdminAccess('divisions.index');
        if ($redirect !== true) {
            return $redirect;
        }
        
        $validated = $request->validate([
            'div_code' => 'required|string|max:10',
            'div_name' => 'required|string|max:100',
            'div_is_active' => 'nullable|boolean',
        ]);
        
        // Handle checkbox boolean
        $validated['div_is_active'] = $request->has('div_is_active');
        
        // Kirim data ke API
        $response = $this->apiPost('/divisions', $validated);
        
        if (!isset($response['success']) || !$response['success']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['message' => $response['message'] ?? 'Gagal menyimpan divisi']);
        }
        
        return redirect()->route('divisions.index')
            ->with('success', 'Divisi berhasil ditambahkan.');
    }

    /**
     * Display the specified division's details.
     *
     * Fetches division data from the API and passes it to the show view.
     * Also passes superadmin status for conditional UI rendering.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $response = $this->apiGet("/divisions/{$id}");
        
        if (!isset($response['success']) || !$response['success']) {
            return redirect()->route('divisions.index')
                ->with('error', $response['message'] ?? 'Divisi tidak ditemukan');
        }
        
        // Pass the superadmin status to view for conditionally showing edit/delete buttons
        $isSuperAdmin = $this->isSuperAdmin();
        
        return view('divisions.show', [
            'division' => $response['data'],
            'isSuperAdmin' => $isSuperAdmin
        ]);
    }

    /**
     * Show the form for editing the specified division.
     *
     * Only accessible to superadmins. Fetches division data for editing.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Check if user has superadmin access
        $redirect = $this->checkSuperAdminAccess('divisions.index');
        if ($redirect !== true) {
            return $redirect;
        }
        
        $response = $this->apiGet("/divisions/{$id}");
        
        if (!isset($response['success']) || !$response['success']) {
            return redirect()->route('divisions.index')
                ->with('error', $response['message'] ?? 'Divisi tidak ditemukan');
        }
        
        return view('divisions.edit', ['division' => $response['data']]);
    }

    /**
     * Update the specified division in storage.
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
        $redirect = $this->checkSuperAdminAccess('divisions.index');
        if ($redirect !== true) {
            return $redirect;
        }
        
        $validated = $request->validate([
            'div_code' => 'required|string|max:10',
            'div_name' => 'required|string|max:100',
            'div_is_active' => 'nullable|boolean',
        ]);
        
        // Handle checkbox boolean
        $validated['div_is_active'] = $request->has('div_is_active');
        
        // Kirim data ke API
        $response = $this->apiPut("/divisions/{$id}", $validated);
        
        if (!isset($response['success']) || !$response['success']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['message' => $response['message'] ?? 'Gagal memperbarui divisi']);
        }
        
        return redirect()->route('divisions.index')
            ->with('success', 'Divisi berhasil diperbarui.');
    }

    /**
     * Remove the specified division from storage.
     *
     * Only accessible to superadmins. Deletes the division via the API and handles errors.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Check if user has superadmin access
        $redirect = $this->checkSuperAdminAccess('divisions.index');
        if ($redirect !== true) {
            return $redirect;
        }
        
        $response = $this->apiDelete("/divisions/{$id}");
        
        if (!isset($response['success']) || !$response['success']) {
            return redirect()->route('divisions.index')
                ->with('error', $response['message'] ?? 'Divisi tidak dapat dihapus');
        }
        
        return redirect()->route('divisions.index')
            ->with('success', 'Divisi berhasil dihapus.');
    }
}