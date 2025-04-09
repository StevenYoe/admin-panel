<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::withCount('users')->get();
        return view('roles.index', compact('roles'));
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
            'role_name' => 'required|string|max:50|unique:login.roles,role_name',
            'role_level' => 'required|integer|min:1|max:100000',
            'role_is_active' => 'nullable|boolean'
        ]);
        
        // Handle checkbox boolean
        $validated['role_is_active'] = $request->has('role_is_active') ? true : false;
        
        // Add created_by tracking
        // $validated['role_created_by'] = auth()->user()->u_employee_id;
        $validated['role_created_at'] = now();
        $validated['role_updated_at'] = null;
        
        Role::create($validated);
        
        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $role->load('users');
        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'role_name' => ['required', 'string', 'max:50', Rule::unique('login.roles', 'role_name')->ignore($role->role_id, 'role_id')],
            'role_level' => 'required|integer|min:1|max:100000',
            'role_is_active' => 'nullable|boolean'
        ]);
        
        // Handle checkbox boolean
        $validated['role_is_active'] = $request->has('role_is_active') ? true : false;
        
        // Add updated_by tracking
        // $validated['role_updated_by'] = auth()->user()->u_employee_id;
        $validated['role_updated_at'] = now();
        unset($validated['role_created_at']);
        
        $role->update($validated);
        
        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        // Check if role has users attached
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Role ini tidak dapat dihapus karena masih digunakan oleh beberapa pengguna.');
        }
        
        $role->delete();
        
        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil dihapus.');
    }
}