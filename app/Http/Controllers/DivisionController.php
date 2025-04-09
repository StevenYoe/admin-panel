<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DivisionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $divisions = Division::all();
        return view('divisions.index', compact('divisions'));
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
            'div_code' => 'required|string|max:10|unique:login.divisions,div_code',
            'div_name' => 'required|string|max:100',
            'div_is_active' => 'nullable|boolean',
        ]);
        
        // Handle checkbox boolean
        $validated['div_is_active'] = $request->has('div_is_active');
        
        // Add created_by tracking
        // $validated['div_created_by'] = auth()->user()->u_employee_id;
        $validated['div_created_at'] = now();
        $validated['div_updated_at'] = null;
        
        // Create division
        Division::create($validated);
        
        return redirect()->route('divisions.index')
            ->with('success', 'Divisi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Division  $division
     * @return \Illuminate\Http\Response
     */
    public function show(Division $division)
    {
        // Load associated users
        $division->load('users');
        return view('divisions.show', compact('division'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Division  $division
     * @return \Illuminate\Http\Response
     */
    public function edit(Division $division)
    {
        return view('divisions.edit', compact('division'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Division  $division
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Division $division)
    {
        $validated = $request->validate([
            'div_code' => ['required', 'string', 'max:10', Rule::unique('login.divisions', 'div_code')->ignore($division->div_id, 'div_id')],
            'div_name' => 'required|string|max:100',
            'div_is_active' => 'nullable|boolean',
        ]);
        
        // Handle checkbox boolean
        $validated['div_is_active'] = $request->has('div_is_active');
        
        // Add updated_by tracking
        // $validated['div_updated_by'] = auth()->user()->u_employee_id;
        $validated['div_updated_at'] = now();
        unset($validated['div_created_at']);
        
        // Update division
        $division->update($validated);
        
        return redirect()->route('divisions.index')
            ->with('success', 'Divisi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Division  $division
     * @return \Illuminate\Http\Response
     */
    public function destroy(Division $division)
    {
        // Check if there are users associated with this division
        if ($division->users()->count() > 0) {
            return redirect()->route('divisions.index')
                ->with('error', 'Divisi tidak dapat dihapus karena masih memiliki pengguna terkait.');
        }
        
        // Delete division
        $division->delete();
        
        return redirect()->route('divisions.index')
            ->with('success', 'Divisi berhasil dihapus.');
    }
}
