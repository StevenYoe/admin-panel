@extends('layouts.app')

@section('title', 'Tambah Divisi - Pazar User Admin')

@section('page-title', 'Tambah Divisi')

@section('content')
    <div class="mb-6">
        <x-button href="{{ route('divisions.index') }}" variant="outline">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar
        </x-button>
    </div>
    
    <x-card>
        <form action="{{ route('divisions.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <x-form.input 
                        name="div_code" 
                        label="Kode Divisi" 
                        placeholder="Masukkan kode divisi" 
                        :value="old('div_code')"
                        required
                        helper="Kode divisi harus unik dan maksimal 10 karakter"
                    />
                </div>
                
                <div>
                    <x-form.input 
                        name="div_name" 
                        label="Nama Divisi" 
                        placeholder="Masukkan nama divisi" 
                        :value="old('div_name')"
                        required
                        helper="Nama divisi maksimal 100 karakter"
                    />
                </div>
                
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="div_is_active" value="1" {{ old('div_is_active', '1') == '1' ? 'checked' : '' }}
                            class="w-4 h-4 text-accent border-gray-600 rounded focus:ring-accent focus:ring-opacity-50">
                        <span class="ml-2">Aktif</span>
                    </label>
                </div>
            </div>
            
            <div class="flex justify-end mt-6 space-x-3">
                <x-button type="button" href="{{ route('divisions.index') }}" variant="outline">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary">
                    Simpan
                </x-button>
            </div>
        </form>
    </x-card>
@endsection
