<!--
    User Detail Page
    - Extends the main application layout
    - Displays detailed information about a specific user
    - Super Admins can edit or delete the user
    - Shows user details, organization info, and assigned roles
    - Uses Blade components for cards and buttons
    - Styled with Tailwind CSS utility classes
-->
@extends('layouts.app')

@section('title', 'Detail Pengguna - Pazar User Admin')

@section('page-title', 'Detail Pengguna')

@section('content')
    <!-- Header section with Back button and Edit/Delete actions (for Super Admin) -->
    <div class="mb-6 flex justify-between items-center">
        <x-button href="{{ route('users.index') }}" variant="outline">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar
        </x-button>
        
        <div class="flex justify-center space-x-2">
            @if($isSuperAdmin)
            <x-button href="{{ route('users.edit', $user['u_id']) }}" variant="primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </x-button>
            
            <form action="{{ route('users.destroy', $user['u_id']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="danger">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Hapus
                </x-button>
            </form>
            @endif
        </div>
    </div>
    
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <!-- Card with user information -->
            <x-card title="Informasi Pengguna">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <h4 class="text-sm font-medium text-gray-400">ID</h4>
                        <p>{{ $user['u_id'] }}</p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-400">Employee ID</h4>
                        <p>{{ $user['u_employee_id'] }}</p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-400">Username</h4>
                        <p>{{ $user['u_name'] }}</p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-400">Email</h4>
                        <p>{{ $user['u_email'] }}</p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-400">Phone</h4>
                        <p>{{ $user['u_phone'] ?: '-' }}</p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-400">Tanggal Lahir</h4>
                        <p>{{ $user['u_birthdate'] ? date('d M Y', strtotime($user['u_birthdate'])) : '-' }}</p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-400">Tanggal Bergabung</h4>
                        <p>{{ $user['u_join_date'] ? date('d M Y', strtotime($user['u_join_date'])) : '-' }}</p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-400">Status</h4>
                        <p>
                            <!-- Display status badge -->
                            @if($user['u_is_active'])
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-700 text-white">
                                    Active
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-700 text-white">
                                    Inactive
                                </span>
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-400">Peran Manajerial</h4>
                        <p>
                            <!-- Display manager/staff badge -->
                            @if($user['u_is_manager'])
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-700 text-white">
                                    Manager
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-700 text-white">
                                    Staff
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h4 class="text-sm font-medium text-gray-400">Alamat</h4>
                    <p class="mt-1">{{ $user['u_address'] ?: '-' }}</p>
                </div>
            </x-card>
            
            <!-- Card with organization information -->
            <x-card title="Informasi Organisasi" class="mt-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <h4 class="text-sm font-medium text-gray-400">Divisi</h4>
                        <p>{{ isset($user['division']) ? $user['division']['div_name'] : '-' }}</p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-400">Jabatan</h4>
                        <p>{{ isset($user['position']) ? $user['position']['pos_name'] : '-' }}</p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-400">Manager</h4>
                        <p>{{ isset($user['manager']) ? $user['manager']['u_name'] : '-' }}</p>
                    </div>
                </div>
            </x-card>
        </div>
        
        <div>
            <!-- Card with user roles -->
            <x-card title="Role">
                @if(!empty($user['roles']))
                    <div class="space-y-3">
                        @foreach($user['roles'] as $role)
                            <div class="p-4 bg-gray-700 rounded-lg border border-gray-600">
                                <h5 class="font-semibold">{{ $role['role_name'] }}</h5>
                                <p class="text-sm text-gray-400">Level: {{ $role['role_level'] }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-4 text-center">
                        <p class="text-gray-400">Pengguna ini tidak memiliki role</p>
                    </div>
                @endif
            </x-card>
        </div>
    </div>
@endsection