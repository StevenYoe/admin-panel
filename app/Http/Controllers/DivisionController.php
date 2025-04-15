<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class DivisionController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $params = [
            'sort_by' => $request->input('sort_by', 'div_id'),
            'sort_order' => $request->input('sort_order', 'asc'),
            'per_page' => $request->input('per_page', 10)
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
     * Show the form for creating a new resource.
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
     * Store a newly created resource in storage.
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
     * Display the specified resource.
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
     * Show the form for editing the specified resource.
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
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
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