<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Division;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with(['roles', 'division', 'position'])->get();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::pluck('role_name', 'role_id');
        $divisions = Division::where('div_is_active', true)->pluck('div_name', 'div_id');
        $positions = Position::where('pos_is_active', true)->pluck('pos_name', 'pos_id');
        $managers = User::where('u_is_manager', true)->where('u_is_active', true)->pluck('u_name', 'u_id');
        
        return view('users.create', compact('roles', 'divisions', 'positions', 'managers'));
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
            'u_employee_id' => 'required|string|max:20|unique:login.users,u_employee_id',
            'u_name' => 'required|string|max:100|unique:login.users,u_name',
            'u_email' => 'required|email|max:100|unique:login.users,u_email',
            'u_password' => 'required|string|min:8|confirmed',
            'u_phone' => 'nullable|string|max:20',
            'u_address' => 'nullable|string',
            'u_birthdate' => 'nullable|date',
            'u_join_date' => 'required|date',
            'u_division_id' => 'nullable|exists:login.divisions,div_id',
            'u_position_id' => 'nullable|exists:login.positions,pos_id',
            'u_is_manager' => 'nullable|boolean',
            'u_manager_id' => 'nullable|exists:login.users,u_id',
            'u_is_active' => 'nullable|boolean',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:login.roles,role_id'
        ]);
        
        // Handle checkbox booleans
        $validated['u_is_manager'] = $request->has('u_is_manager') ? 1 : 0;
        $validated['u_is_active'] = $request->has('u_is_active') ? 1 : 0;
        
        // Hash password
        $validated['u_password'] = Hash::make($validated['u_password']);
        
        // Set only created_at, leave updated_at as null
        $validated['u_created_at'] = now();
        $validated['u_updated_at'] = null;
        // No u_updated_at when creating
        
        // Create user
        $user = User::create($validated);
        
        // Assign roles
        if (isset($validated['roles'])) {
            $user->roles()->attach($validated['roles']);
        }
        
        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $user->load(['roles', 'division', 'position', 'manager']);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $roles = Role::pluck('role_name', 'role_id');
        $divisions = Division::where('div_is_active', true)->pluck('div_name', 'div_id');
        $positions = Position::where('pos_is_active', true)->pluck('pos_name', 'pos_id');
        $managers = User::where('u_is_manager', true)
                        ->where('u_is_active', true)
                        ->where('u_id', '!=', $user->u_id) // Exclude self
                        ->pluck('u_name', 'u_id');
        
        $userRoles = $user->roles->pluck('role_id')->toArray();
        
        return view('users.edit', compact('user', 'roles', 'divisions', 'positions', 'managers', 'userRoles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'u_employee_id' => ['required', 'string', 'max:20', Rule::unique('login.users', 'u_employee_id')->ignore($user->u_id, 'u_id')],
            'u_name' => ['required', 'string', 'max:100', Rule::unique('login.users', 'u_name')->ignore($user->u_id, 'u_id')],
            'u_email' => ['required', 'email', 'max:100', Rule::unique('login.users', 'u_email')->ignore($user->u_id, 'u_id')],
            'u_password' => 'nullable|string|min:8|confirmed',
            'u_phone' => 'nullable|string|max:20',
            'u_address' => 'nullable|string',
            'u_birthdate' => 'nullable|date',
            'u_join_date' => 'required|date',
            'u_division_id' => 'nullable|exists:login.divisions,div_id',
            'u_position_id' => 'nullable|exists:login.positions,pos_id',
            'u_is_manager' => 'nullable|boolean',
            'u_manager_id' => 'nullable|exists:login.users,u_id',
            'u_is_active' => 'nullable|boolean',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:login.roles,role_id'
        ]);
        
        // Handle checkbox booleans
        $validated['u_is_manager'] = $request->has('u_is_manager') ? 1 : 0;
        $validated['u_is_active'] = $request->has('u_is_active') ? 1 : 0;
        
        // Update password if provided
        if (isset($validated['u_password'])) {
            $validated['u_password'] = Hash::make($validated['u_password']);
        } else {
            unset($validated['u_password']);
        }
        
        // Set only updated_at when updating
        $validated['u_updated_at'] = now();
        
        // Remove created_at from the data being updated to preserve the original value
        unset($validated['u_created_at']);
        
        // Update user
        $user->update($validated);
        
        // Sync roles
        if (isset($validated['roles'])) {
            $user->roles()->sync($validated['roles']);
        } else {
            $user->roles()->detach();
        }
        
        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        // Detach all roles first
        $user->roles()->detach();
        
        // Delete user
        $user->delete();
        
        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}