<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $positions = Position::all();
        return view('positions.index', compact('positions'));
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
            'pos_code' => 'required|string|max:10|unique:login.positions,pos_code',
            'pos_name' => 'required|string|max:100',
            'pos_is_active' => 'nullable|boolean',
        ]);
        
        // Handle checkbox boolean
        $validated['pos_is_active'] = $request->has('pos_is_active');
        
        // Add created_by tracking
        // $validated['pos_created_by'] = auth()->user()->u_employee_id;
        $validated['pos_created_at'] = now();
        $validated['pos_updated_at'] = null;
        
        // Create position
        Position::create($validated);
        
        return redirect()->route('positions.index')
            ->with('success', 'Jabatan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Position  $position
     * @return \Illuminate\Http\Response
     */
    public function show(Position $position)
    {
        // Load associated users
        $position->load('users');
        return view('positions.show', compact('position'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Position  $position
     * @return \Illuminate\Http\Response
     */
    public function edit(Position $position)
    {
        return view('positions.edit', compact('position'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Position  $position
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'pos_code' => ['required', 'string', 'max:10', Rule::unique('login.positions', 'pos_code')->ignore($position->pos_id, 'pos_id')],
            'pos_name' => 'required|string|max:100',
            'pos_is_active' => 'nullable|boolean',
        ]);
        
        // Handle checkbox boolean
        $validated['pos_is_active'] = $request->has('pos_is_active');
        
        // Add updated_by tracking
        // $validated['pos_updated_by'] = auth()->user()->u_employee_id;
        $validated['pos_updated_at'] = now();
        unset($validated['pos_created_at']);
        
        // Update position
        $position->update($validated);
        
        return redirect()->route('positions.index')
            ->with('success', 'Jabatan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Position  $position
     * @return \Illuminate\Http\Response
     */
    public function destroy(Position $position)
    {
        // Check if there are users associated with this position
        if ($position->users()->count() > 0) {
            return redirect()->route('positions.index')
                ->with('error', 'Jabatan tidak dapat dihapus karena masih memiliki pengguna terkait.');
        }
        
        // Delete position
        $position->delete();
        
        return redirect()->route('positions.index')
            ->with('success', 'Jabatan berhasil dihapus.');
    }
}
