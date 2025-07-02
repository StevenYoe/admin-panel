<!--
    Division Detail Page
    - Extends the main application layout
    - Displays detailed information about a specific division
    - Super Admins can edit or delete the division
    - Shows a list of users belonging to this division
    - Uses Blade components for cards, tables, and buttons
    - Styled with Tailwind CSS utility classes
-->
@extends('layouts.app')

@section('title', 'Detail Divisi - Pazar User Admin')

@section('page-title', 'Detail Divisi')

@section('content')
    <!-- Header section with Back button and Edit/Delete actions (for Super Admin) -->
    <div class="mb-6 flex justify-between items-center">
        <x-button href="{{ route('divisions.index') }}" variant="outline">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar
        </x-button>
        
        <div class="flex justify-center space-x-2">
            @if($isSuperAdmin)
            <x-button href="{{ route('divisions.edit', $division['div_id']) }}" variant="primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </x-button>
            
            <form action="{{ route('divisions.destroy', $division['div_id']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus divisi ini?');">
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
    
    <!-- Division information card -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <x-card title="Informasi Divisi">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <h4 class="text-sm font-medium text-gray-400">ID</h4>
                        <p>{{ $division['div_id'] }}</p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-400">Kode</h4>
                        <p>{{ $division['div_code'] }}</p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-400">Nama</h4>
                        <p>{{ $division['div_name'] }}</p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-400">Status</h4>
                        <p>
                            <!-- Display status badge -->
                            @if($division['div_is_active'])
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
                </div>
            </x-card>
        </div>
    </div>
    
    <!-- Card with list of users in this division -->
    <div class="mt-6">
        <x-card title="Daftar Pengguna Divisi Ini">
            @if(!empty($division['users']) && count($division['users']) > 0)
                <div class="overflow-x-auto">
                    <!-- Table displaying users in the division -->
                    <x-table :headers="['ID', 'Nama', 'Email', 'Jabatan']">
                        @foreach($division['users'] as $user)
                            <tr class="border-b dark:border-gray-700 hover:bg-gray-600">
                                <td class="px-5 py-4 text-center">{{ $user['u_employee_id'] }}</td>
                                <td class="px-5 py-4 text-center">{{ $user['u_name'] }}</td>
                                <td class="px-5 py-4 text-center">{{ $user['u_email'] }}</td>
                                <td class="px-5 py-4 text-center">
                                    {{ $user['position'] ? $user['position']['pos_name'] : '-' }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <!-- View user details button -->
                                    <div class="flex justify-center space-x-2">
                                        <a href="{{ route('users.show', $user['u_id']) }}" class="text-blue-500 hover:text-blue-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </x-table>
                </div>
            @else
                <!-- Message if no users in this division -->
                <div class="py-4 text-center">
                    <p class="text-gray-400">Divisi ini belum memiliki pengguna</p>
                </div>
            @endif
        </x-card>
    </div>
@endsection
