@extends('layouts.app')

@section('title', 'Detail Role - Pazar Admin')

@section('page-title', 'Detail Role')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <x-button href="{{ route('roles.index') }}" variant="outline">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar
        </x-button>
        
        <div class="flex space-x-2">
            <x-button href="{{ route('roles.edit', $role['role_id']) }}" variant="primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit
            </x-button>
            
            <form action="{{ route('roles.destroy', $role['role_id']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus role ini?');">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="danger">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Hapus
                </x-button>
            </form>
        </div>
    </div>
    
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <x-card title="Informasi Role">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <h4 class="text-sm font-medium text-gray-400">ID</h4>
                    <p>{{ $role['role_id'] }}</p>
                </div>
                
                <div>
                    <h4 class="text-sm font-medium text-gray-400">Nama Role</h4>
                    <p class="text-lg font-semibold">{{ $role['role_name'] }}</p>
                </div>
                
                <div>
                    <h4 class="text-sm font-medium text-gray-400">Level</h4>
                    <p>{{ $role['role_level'] }}</p>
                </div>
            </div>
        </x-card>
        
        <x-card title="Pengguna dengan Role Ini">
            @if(!empty($role['users']) && count($role['users']) > 0)
                <div class="space-y-3">
                    @foreach($role['users'] as $user)
                        <div class="p-4 bg-gray-700 rounded-lg border border-gray-600">
                            <div class="flex justify-between">
                                <div>
                                    <h5 class="font-semibold">{{ $user['u_name'] }}</h5>
                                    <p class="text-sm text-gray-400">{{ $user['u_email'] }}</p>
                                </div>
                                <a href="{{ route('users.show', $user['u_id']) }}" class="text-blue-500 hover:text-blue-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-4 text-center">
                    <p class="text-gray-400">Tidak ada pengguna dengan role ini</p>
                </div>
            @endif
        </x-card>
    </div>
@endsection