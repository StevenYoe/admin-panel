<!--
    Division List Page
    - Extends the main application layout
    - Displays a list of all divisions in a table format
    - Super Admins can add, edit, or delete divisions
    - Uses Blade components for table, buttons, and cards
    - Supports sorting and pagination
    - Styled with Tailwind CSS utility classes
-->
@extends('layouts.app')

@section('title', 'Divisi - Pazar User Admin')

@section('page-title', 'Divisi')

@section('content')
    <!-- Header section with page title and Add Division button (for Super Admin) -->
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-xl font-semibold">Daftar Divisi</h2>
        @if($isSuperAdmin)
        <x-button href="{{ route('divisions.create') }}" variant="primary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Tambah Divisi
        </x-button>
        @endif
    </div>
    
    <!-- Card container for the division table -->
    <x-card>
        @if(count($divisions) > 0)
            <div class="overflow-x-auto">
            <!-- Table displaying division data -->
            <x-table 
                :headers="[
                    ['name' => 'ID', 'key' => 'div_id'],
                    ['name' => 'Kode', 'key' => 'div_code'],
                    ['name' => 'Nama', 'key' => 'div_name'],
                    ['name' => 'Status', 'key' => 'div_is_active']
                ]"
                :sortBy="$sortBy"
                :sortOrder="$sortOrder"
            >
                @foreach($divisions as $division)
                    <tr class="border-b dark:border-gray-700 hover:bg-gray-600">
                        <td class="px-5 py-4 text-center">{{ $division['div_id'] }}</td>
                        <td class="px-5 py-4 text-center">{{ $division['div_code'] }}</td>
                        <td class="px-5 py-4 text-center">{{ $division['div_name'] }}</td>
                        <td class="px-5 py-4 text-center">
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
                        </td>
                        <td class="px-5 py-4 text-center">
                            <!-- Action buttons: View, Edit, Delete (Edit/Delete for Super Admin only) -->
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('divisions.show', $division['div_id']) }}" class="text-blue-500 hover:text-blue-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                @if($isSuperAdmin)
                                <a href="{{ route('divisions.edit', $division['div_id']) }}" class="text-yellow-500 hover:text-yellow-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('divisions.destroy', $division['div_id']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus divisi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </x-table>
        </div>
        <!-- Pagination links if available -->
        @if(isset($paginator))
            <div class="mt-4">
                {{ $paginator->links() }}
            </div>
        @endif
        @else
            <!-- Message and button if no divisions exist -->
            <div class="py-8 text-center">
                <p class="text-gray-400">Belum ada divisi yang ditambahkan</p>
                <x-button href="{{ route('divisions.create') }}" variant="primary" class="mt-4">
                    Tambah Divisi
                </x-button>
            </div>
        @endif
    </x-card>
@endsection
