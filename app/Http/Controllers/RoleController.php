<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = $this->apiGet('/roles');
        
        if (!isset($response['success']) || !$response['success']) {
            return view('roles.index')->with('error', $response['message'] ?? 'Gagal memuat data role');
        }
        
        // Handle both paginated and non-paginated responses
        if (isset($response['data']['data'])) {
            $roles = $response['data']['data'];
            $pagination = $response['data'];
        } else {
            $roles = $response['data'] ?? [];
            $pagination = null;
        }
        
        return view('roles.index', compact('roles', 'pagination'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('roles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_name' => 'required|string|max:50',
            'role_level' => 'required|integer|min:1|max:100000',
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
     * Display the specified resource.
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
        
        return view('roles.show', ['role' => $response['data']]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $response = $this->apiGet("/roles/{$id}");
        
        if (!isset($response['success']) || !$response['success']) {
            return redirect()->route('roles.index')
                ->with('error', $response['message'] ?? 'Role tidak ditemukan');
        }
        
        return view('roles.edit', ['role' => $response['data']]);
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
        $validated = $request->validate([
            'role_name' => 'required|string|max:50',
            'role_level' => 'required|integer|min:1|max:100000',
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = $this->apiDelete("/roles/{$id}");
        
        if (!isset($response['success']) || !$response['success']) {
            return redirect()->route('roles.index')
                ->with('error', $response['message'] ?? 'Role tidak dapat dihapus');
        }
        
        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil dihapus.');
    }
}