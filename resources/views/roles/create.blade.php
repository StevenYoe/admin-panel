@extends('layouts.app')

@section('title', 'Tambah Role - Pazar Admin')

@section('page-title', 'Tambah Role')

@section('content')
    <div class="mb-6">
        <x-button href="{{ route('roles.index') }}" variant="outline">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar
        </x-button>
    </div>
    
    <x-card>
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <x-form.input 
                        name="role_name" 
                        label="Nama Role" 
                        placeholder="Masukkan nama role" 
                        :value="old('role_name')"
                        required
                        helper="Nama role harus unik dan maksimal 50 karakter"
                    />
                </div>
                
                <div>
                    <x-form.input 
                        type="number" 
                        name="role_level" 
                        label="Level" 
                        placeholder="Masukkan level (angka)" 
                        :value="old('role_level', 1)"
                        required
                        min="1"
                        max="100000"
                        helper="Level menentukan hierarki role. Semakin tinggi nilai, semakin tinggi levelnya."
                    />
                </div>
            </div>
            
            <div class="flex justify-end mt-6 space-x-3">
                <x-button type="button" href="{{ route('roles.index') }}" variant="outline">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary">
                    Simpan
                </x-button>
            </div>
        </form>
    </x-card>
@endsection