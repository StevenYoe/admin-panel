<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DivisionController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = $this->apiGet('/divisions');
        
        if (!isset($response['success']) || !$response['success']) {
            return view('divisions.index')->with('error', $response['message'] ?? 'Gagal memuat data divisi');
        }
        
        // Handle both paginated and non-paginated responses
        if (isset($response['data']['data'])) {
            $divisions = $response['data']['data'];
            $pagination = $response['data'];
        } else {
            $divisions = $response['data'] ?? [];
            $pagination = null;
        }
        
        return view('divisions.index', compact('divisions', 'pagination'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
        
        return view('divisions.show', ['division' => $response['data']]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
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
        $response = $this->apiDelete("/divisions/{$id}");
        
        if (!isset($response['success']) || !$response['success']) {
            return redirect()->route('divisions.index')
                ->with('error', $response['message'] ?? 'Divisi tidak dapat dihapus');
        }
        
        return redirect()->route('divisions.index')
            ->with('success', 'Divisi berhasil dihapus.');
    }
}