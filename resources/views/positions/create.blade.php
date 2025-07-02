<!--
    Create Position Page
    - Extends the main application layout
    - Provides a form to add a new job position
    - Includes fields for position code, name, and active status
    - Uses Blade components for form inputs and buttons
    - Form submits to the 'positions.store' route
    - Styled with Tailwind CSS utility classes
-->
@extends('layouts.app')

@section('title', 'Tambah Jabatan - Pazar User Admin')

@section('page-title', 'Tambah Jabatan')

@section('content')
    <!-- Back to Position List Button -->
    <div class="mb-6">
        <x-button href="{{ route('positions.index') }}" variant="outline">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar
        </x-button>
    </div>
    
    <!-- Card container for the form -->
    <x-card>
        <!-- Position creation form -->
        <form action="{{ route('positions.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <!-- Input for Position Code -->
                    <x-form.input 
                        name="pos_code" 
                        label="Kode Jabatan" 
                        placeholder="Masukkan kode jabatan" 
                        :value="old('pos_code')"
                        required
                        helper="Kode jabatan harus unik dan maksimal 10 karakter"
                    />
                </div>
                
                <div>
                    <!-- Input for Position Name -->
                    <x-form.input 
                        name="pos_name" 
                        label="Nama Jabatan" 
                        placeholder="Masukkan nama jabatan" 
                        :value="old('pos_name')"
                        required
                        helper="Nama jabatan maksimal 100 karakter"
                    />
                </div>
                
                <div>
                    <!-- Checkbox for Active Status -->
                    <label class="flex items-center">
                        <input type="checkbox" name="pos_is_active" value="1" {{ old('pos_is_active', '1') == '1' ? 'checked' : '' }}
                            class="w-4 h-4 text-accent border-gray-600 rounded focus:ring-accent focus:ring-opacity-50">
                        <span class="ml-2">Aktif</span>
                    </label>
                </div>
            </div>
            
            <!-- Action buttons: Cancel and Save -->
            <div class="flex justify-end mt-6 space-x-3">
                <x-button type="button" href="{{ route('positions.index') }}" variant="outline">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary">
                    Simpan
                </x-button>
            </div>
        </form>
    </x-card>
@endsection
