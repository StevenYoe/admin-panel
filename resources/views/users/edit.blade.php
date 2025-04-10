@extends('layouts.app')

@section('title', 'Edit Pengguna - Pazar Admin')

@section('page-title', 'Edit Pengguna')

@section('content')
    <div class="mb-6">
        <x-button href="{{ route('users.index') }}" variant="outline">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar
        </x-button>
    </div>
    
    <x-card>
        <form action="{{ route('users.update', $user['u_id']) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <x-form.input 
                        name="u_employee_id" 
                        label="Employee ID" 
                        placeholder="Masukkan Employee ID" 
                        :value="old('u_employee_id', $user['u_employee_id'])"
                        required
                        helper="Employee ID harus unik dan maksimal 20 karakter"
                    />
                </div>
                
                <div>
                    <x-form.input 
                        name="u_name" 
                        label="Username" 
                        placeholder="Masukkan username" 
                        :value="old('u_name', $user['u_name'])"
                        required
                        helper="Username harus unik dan maksimal 100 karakter"
                    />
                </div>
                
                <div>
                    <x-form.input 
                        type="email" 
                        name="u_email" 
                        label="Email" 
                        placeholder="Masukkan email" 
                        :value="old('u_email', $user['u_email'])"
                        required
                        helper="Email harus unik dan valid"
                    />
                </div>
                
                <div>
                    <x-form.input 
                        type="password" 
                        name="u_password" 
                        label="Password" 
                        placeholder="Masukkan password baru" 
                        helper="Biarkan kosong jika tidak ingin mengubah password. Minimal 8 karakter."
                    />
                </div>
                
                <div>
                    <x-form.input 
                        type="password" 
                        name="u_password_confirmation" 
                        label="Konfirmasi Password" 
                        placeholder="Masukkan konfirmasi password baru" 
                    />
                </div>
                
                <div>
                    <x-form.input 
                        type="tel" 
                        name="u_phone" 
                        label="Nomor Telepon" 
                        placeholder="Masukkan nomor telepon" 
                        :value="old('u_phone', $user['u_phone'])"
                        helper="Opsional, maksimal 20 karakter"
                    />
                </div>
                
                <div class="md:col-span-2">
                    <x-form.textarea 
                        name="u_address" 
                        label="Alamat" 
                        placeholder="Masukkan alamat" 
                        :value="old('u_address', $user['u_address'])"
                        helper="Opsional"
                    />
                </div>
                
                <div>
                    <x-form.input 
                        type="date" 
                        name="u_birthdate" 
                        label="Tanggal Lahir" 
                        :value="old('u_birthdate', $user['u_birthdate'] ? date('Y-m-d', strtotime($user['u_birthdate'])) : '')"
                        helper="Opsional"
                    />
                </div>
                
                <div>
                    <x-form.input 
                        type="date" 
                        name="u_join_date" 
                        label="Tanggal Bergabung" 
                        :value="old('u_join_date', $user['u_join_date'] ? date('Y-m-d', strtotime($user['u_join_date'])) : '')"
                        required
                    />
                </div>
                
                <div>
                    <x-form.select 
                        name="u_division_id" 
                        label="Divisi" 
                        :options="$divisions"
                        :selected="old('u_division_id', $user['u_division_id'])"
                        placeholder="Pilih Divisi"
                    />
                </div>
                
                <div>
                    <x-form.select 
                        name="u_position_id" 
                        label="Jabatan" 
                        :options="$positions"
                        :selected="old('u_position_id', $user['u_position_id'])"
                        placeholder="Pilih Jabatan"
                    />
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2">Is Manager?</label>
                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="u_is_manager" value="1" {{ old('u_is_manager', $user['u_is_manager']) == '1' ? 'checked' : '' }}
                                class="w-4 h-4 text-accent border-gray-600 focus:ring-accent focus:ring-opacity-50">
                            <span class="ml-2">Yes</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="u_is_manager" value="0" {{ old('u_is_manager', $user['u_is_manager']) == '0' ? 'checked' : '' }}
                                class="w-4 h-4 text-accent border-gray-600 focus:ring-accent focus:ring-opacity-50">
                            <span class="ml-2">No</span>
                        </label>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2">Status</label>
                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="u_is_active" value="1" {{ old('u_is_active', $user['u_is_active']) == '1' ? 'checked' : '' }}
                                class="w-4 h-4 text-accent border-gray-600 focus:ring-accent focus:ring-opacity-50">
                            <span class="ml-2">Active</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="u_is_active" value="0" {{ old('u_is_active', $user['u_is_active']) == '0' ? 'checked' : '' }}
                                class="w-4 h-4 text-accent border-gray-600 focus:ring-accent focus:ring-opacity-50">
                            <span class="ml-2">Inactive</span>
                        </label>
                    </div>
                </div>
                
                <div class="md:col-span-2">
                    <h4 class="text-sm font-medium mb-2">Role</h4>
                    <div class="p-4 bg-gray-700 rounded-md border border-gray-600">
                        @foreach($roles as $id => $roleName)
                            <div class="mb-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="roles[]" value="{{ $id }}" 
                                        {{ in_array($id, old('roles', $userRoles)) ? 'checked' : '' }}
                                        class="w-4 h-4 text-accent border-gray-600 rounded focus:ring-accent focus:ring-opacity-50">
                                    <span class="ml-2">{{ $roleName }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end mt-6 space-x-3">
                <x-button type="button" href="{{ route('users.index') }}" variant="outline">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary">
                    Perbarui
                </x-button>
            </div>
        </form>
    </x-card>
@endsection