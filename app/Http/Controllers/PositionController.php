<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

// PositionController manages CRUD operations for position resources in the admin panel.
// It communicates with the API to fetch, create, update, and delete position data,
// and enforces superadmin access control for sensitive actions.

class PositionController extends BaseController
{
    /**
     * Display a listing of the positions with pagination, sorting, and access control.
     *
     * Fetches position data from the API, builds a paginator, and passes all relevant
     * variables to the index view. Handles API errors gracefully.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $params = [
            'sort_by' => $request->input('sort_by', 'pos_id'),
            'sort_order' => $request->input('sort_order', 'asc'),
            'per_page' => $request->input('per_page', 10),
            'page' => $request->input('page', 1)
        ];

        $response = $this->apiGet('/positions', $params);
        
        if (!isset($response['success']) || !$response['success']) {
            return view('positions.index')->with('error', $response['message'] ?? 'Gagal memuat data jabatan');
        }
        
        $positions = $response['data']['data'] ?? [];
        
    
    // Create a proper paginator instance if we have the necessary pagination data
    $paginator = null;
    if (isset($response['data'])) {
        $paginationData = $response['data'];
        if (isset($paginationData['current_page']) && isset($paginationData['per_page']) && isset($paginationData['total'])) {
            $paginator = new LengthAwarePaginator(
                $positions,
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

        return view('positions.index', compact('positions', 'paginator', 'sortBy', 'sortOrder', 'isSuperAdmin'));
    }

    /**
     * Show the form for creating a new position.
     *
     * Only accessible to superadmins. Redirects unauthorized users.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check if user has superadmin access
        $redirect = $this->checkSuperAdminAccess('positions.index');
        if ($redirect !== true) {
            return $redirect;
        }
        
        return view('positions.create');
    }

    /**
     * Store a newly created position in storage.
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
        $redirect = $this->checkSuperAdminAccess('positions.index');
        if ($redirect !== true) {
            return $redirect;
        }
        
        $validated = $request->validate([
            'pos_code' => 'required|string|max:10',
            'pos_name' => 'required|string|max:100',
            'pos_is_active' => 'nullable|boolean',
        ]);
        
        // Handle checkbox boolean
        $validated['pos_is_active'] = $request->has('pos_is_active');
        
        // Kirim data ke API
        $response = $this->apiPost('/positions', $validated);
        
        if (!isset($response['success']) || !$response['success']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['message' => $response['message'] ?? 'Gagal menyimpan jabatan']);
        }
        
        return redirect()->route('positions.index')
            ->with('success', 'Jabatan berhasil ditambahkan.');
    }

    /**
     * Display the specified position's details.
     *
     * Fetches position data from the API and passes it to the show view.
     * Also passes superadmin status for conditional UI rendering.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $response = $this->apiGet("/positions/{$id}");
        
        if (!isset($response['success']) || !$response['success']) {
            return redirect()->route('positions.index')
                ->with('error', $response['message'] ?? 'Jabatan tidak ditemukan');
        }
        
        // Pass the superadmin status to view for conditionally showing edit/delete buttons
        $isSuperAdmin = $this->isSuperAdmin();
        
        return view('positions.show', [
            'position' => $response['data'],
            'isSuperAdmin' => $isSuperAdmin
        ]);
    }

    /**
     * Show the form for editing the specified position.
     *
     * Only accessible to superadmins. Fetches position data for editing.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Check if user has superadmin access
        $redirect = $this->checkSuperAdminAccess('positions.index');
        if ($redirect !== true) {
            return $redirect;
        }
        
        $response = $this->apiGet("/positions/{$id}");
        
        if (!isset($response['success']) || !$response['success']) {
            return redirect()->route('positions.index')
                ->with('error', $response['message'] ?? 'Jabatan tidak ditemukan');
        }
        
        return view('positions.edit', ['position' => $response['data']]);
    }

    /**
     * Update the specified position in storage.
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
        $redirect = $this->checkSuperAdminAccess('positions.index');
        if ($redirect !== true) {
            return $redirect;
        }
        
        $validated = $request->validate([
            'pos_code' => 'required|string|max:10',
            'pos_name' => 'required|string|max:100',
            'pos_is_active' => 'nullable|boolean',
        ]);
        
        // Handle checkbox boolean
        $validated['pos_is_active'] = $request->has('pos_is_active');
        
        // Kirim data ke API
        $response = $this->apiPut("/positions/{$id}", $validated);
        
        if (!isset($response['success']) || !$response['success']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['message' => $response['message'] ?? 'Gagal memperbarui jabatan']);
        }
        
        return redirect()->route('positions.index')
            ->with('success', 'Jabatan berhasil diperbarui.');
    }

    /**
     * Remove the specified position from storage.
     *
     * Only accessible to superadmins. Deletes the position via the API and handles errors.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Check if user has superadmin access
        $redirect = $this->checkSuperAdminAccess('positions.index');
        if ($redirect !== true) {
            return $redirect;
        }
        
        $response = $this->apiDelete("/positions/{$id}");
        
        if (!isset($response['success']) || !$response['success']) {
            return redirect()->route('positions.index')
                ->with('error', $response['message'] ?? 'Jabatan tidak dapat dihapus');
        }
        
        return redirect()->route('positions.index')
            ->with('success', 'Jabatan berhasil dihapus.');
    }
}