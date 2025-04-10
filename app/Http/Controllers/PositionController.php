<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PositionController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = $this->apiGet('/positions');
        
        if (!isset($response['success']) || !$response['success']) {
            return view('positions.index')->with('error', $response['message'] ?? 'Gagal memuat data jabatan');
        }
        
        // Handle both paginated and non-paginated responses
        if (isset($response['data']['data'])) {
            $positions = $response['data']['data'];
            $pagination = $response['data'];
        } else {
            $positions = $response['data'] ?? [];
            $pagination = null;
        }
        
        return view('positions.index', compact('positions', 'pagination'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('positions.create');
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
     * Display the specified resource.
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
        
        return view('positions.show', ['position' => $response['data']]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $response = $this->apiGet("/positions/{$id}");
        
        if (!isset($response['success']) || !$response['success']) {
            return redirect()->route('positions.index')
                ->with('error', $response['message'] ?? 'Jabatan tidak ditemukan');
        }
        
        return view('positions.edit', ['position' => $response['data']]);
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = $this->apiDelete("/positions/{$id}");
        
        if (!isset($response['success']) || !$response['success']) {
            return redirect()->route('positions.index')
                ->with('error', $response['message'] ?? 'Jabatan tidak dapat dihapus');
        }
        
        return redirect()->route('positions.index')
            ->with('success', 'Jabatan berhasil dihapus.');
    }
}