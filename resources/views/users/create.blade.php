<!--
    Create User Page
    - Extends the main application layout
    - Provides a form to add a new user to the system
    - Includes fields for employee ID, username, email, password, phone, address, birthdate, join date, division, position, manager status, active status, profile image, and roles
    - Uses Blade components for form inputs, selects, textareas, and buttons
    - Handles file upload for profile image
    - Form submits to the 'users.store' route
    - Styled with Tailwind CSS utility classes
-->
@extends('layouts.app')

@section('title', 'Tambah Pengguna - Pazar User Admin')

@section('page-title', 'Tambah Pengguna')

@section('content')
    <!-- Back to User List Button -->
    <div class="mb-6">
        <x-button href="{{ route('users.index') }}" variant="outline">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Daftar
        </x-button>
    </div>
    
    <!-- Card container for the form -->
    <x-card>
        <!-- User creation form -->
        <form enctype="multipart/form-data" action="{{ route('users.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <!-- Input for Employee ID -->
                    <x-form.input 
                        name="u_employee_id" 
                        label="Employee ID" 
                        placeholder="Masukkan Employee ID" 
                        :value="old('u_employee_id')"
                        required
                        helper="Employee ID harus unik dan maksimal 20 karakter"
                    />
                </div>
                
                <div>
                    <!-- Input for Username -->
                    <x-form.input 
                        name="u_name" 
                        label="Username" 
                        placeholder="Masukkan username" 
                        :value="old('u_name')"
                        required
                        helper="Username harus unik dan maksimal 100 karakter"
                    />
                </div>
                
                <div>
                    <!-- Input for Email -->
                    <x-form.input 
                        type="email" 
                        name="u_email" 
                        label="Email" 
                        placeholder="Masukkan email" 
                        :value="old('u_email')"
                        required
                        helper="Email harus unik dan valid"
                    />
                </div>
                
                <div>
                    <!-- Input for Password -->
                    <x-form.input 
                        type="password" 
                        name="u_password" 
                        label="Password" 
                        placeholder="Masukkan password" 
                        required
                        helper="Password minimal 8 karakter"
                    />
                </div>
                
                <div>
                    <!-- Input for Password Confirmation -->
                    <x-form.input 
                        type="password" 
                        name="u_password_confirmation" 
                        label="Konfirmasi Password" 
                        placeholder="Masukkan konfirmasi password" 
                        required
                    />
                </div>
                
                <div>
                    <!-- Input for Phone Number -->
                    <x-form.input 
                        type="tel" 
                        name="u_phone" 
                        label="Nomor Telepon" 
                        placeholder="Masukkan nomor telepon" 
                        :value="old('u_phone')"
                        helper="Opsional, maksimal 20 karakter"
                    />
                </div>
                
                <div class="md:col-span-2">
                    <!-- Input for Address -->
                    <x-form.textarea 
                        name="u_address" 
                        label="Alamat" 
                        placeholder="Masukkan alamat" 
                        :value="old('u_address')"
                        helper="Opsional"
                    />
                </div>
                
                <div>
                    <!-- Input for Birthdate -->
                    <x-form.input 
                        type="date" 
                        name="u_birthdate" 
                        label="Tanggal Lahir" 
                        :value="old('u_birthdate')"
                        helper="Opsional"
                    />
                </div>
                
                <div>
                    <!-- Input for Join Date -->
                    <x-form.input 
                        type="date" 
                        name="u_join_date" 
                        label="Tanggal Bergabung" 
                        :value="old('u_join_date')"
                        required
                    />
                </div>
                
                <div>
                    <!-- Select for Division -->
                    <x-form.select 
                        name="u_division_id" 
                        label="Divisi" 
                        :options="$divisions"
                        :selected="old('u_division_id')"
                        placeholder="Pilih Divisi"
                    />
                </div>
                
                <div>
                    <!-- Select for Position -->
                    <x-form.select 
                        name="u_position_id" 
                        label="Jabatan" 
                        :options="$positions"
                        :selected="old('u_position_id')"
                        placeholder="Pilih Jabatan"
                    />
                </div>
                
                <div>
                    <!-- Radio buttons for Manager status -->
                    <label class="block text-sm font-medium mb-2">Is Manager?</label>
                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="u_is_manager" value="1" {{ old('u_is_manager') == '1' ? 'checked' : '' }}
                                class="w-4 h-4 text-accent border-gray-600 focus:ring-accent focus:ring-opacity-50">
                            <span class="ml-2">Yes</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="u_is_manager" value="0" {{ old('u_is_manager', '0') == '0' ? 'checked' : '' }}
                                class="w-4 h-4 text-accent border-gray-600 focus:ring-accent focus:ring-opacity-50">
                            <span class="ml-2">No</span>
                        </label>
                    </div>
                </div>
                
                <!-- Hidden field to always set u_is_active as true for new users -->
                <input type="hidden" name="u_is_active" value="1">

                <div class="mb-4">
                    <!-- File input for Profile Image -->
                    <label for="u_profile_image" class="block text-sm font-medium mb-2">Profile Image</label>
                    <input type="file" name="u_profile_image" id="u_profile_image" accept="image/*"
                        class="block w-full text-sm text-gray-400 border border-gray-600 rounded-md 
                        file:mr-4 file:py-2 file:px-4 file:rounded-md
                        file:border-0 file:text-sm file:font-medium
                        file:bg-accent file:text-white
                        hover:file:bg-accent-dark">
                    <p class="mt-1 text-xs text-gray-400">Upload JPG, PNG, or GIF (max 2MB)</p>
                    
                    @if(isset($user) && $user['u_profile_image'])
                        <div class="mt-2">
                            <p class="text-xs text-gray-400 mb-1">Current image:</p>
                            <img src="{{ config('app.api_base_url') . '/storage/' . $user['u_profile_image'] }}" 
                                alt="Profile Image" class="h-20 w-20 object-cover rounded-full">
                        </div>
                    @endif
                </div>
                
                <div class="md:col-span-2">
                    <!-- Checkbox list for assigning roles to the user -->
                    <h4 class="text-sm font-medium mb-2">Role</h4>
                    <div class="p-4 bg-gray-700 rounded-md border border-gray-600">
                        @foreach($roles as $id => $roleName)
                            <div class="mb-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="roles[]" value="{{ $id }}" 
                                        {{ in_array($id, old('roles', [])) ? 'checked' : '' }}
                                        class="w-4 h-4 text-accent border-gray-600 rounded focus:ring-accent focus:ring-opacity-50">
                                    <span class="ml-2">{{ $roleName }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Action buttons: Cancel and Save -->
            <div class="flex justify-end mt-6 space-x-3">
                <x-button type="button" href="{{ route('users.index') }}" variant="outline">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary">
                    Simpan
                </x-button>
            </div>
        </form>
    </x-card>
@endsection