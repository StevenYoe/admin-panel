<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pazar Admin Dashboard')</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js untuk interaktivitas -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        // Konfigurasi Tailwind untuk tema
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'accent': '#BF161C',
                        'gray-custom': '#E0FBFC',
                        'bg-dark': '#253237',
                    }
                    // colors: {
                    //     'accent': '#BF161C',
                    //     'gray-custom': '#E0FBFC',
                    //     'bg-dark': '#253237',
                    // }
                    // colors: {
                    //     'accent': '#253237',
                    //     'gray-custom': '#E0FBFC',
                    //     'bg-dark': '#BF161C',
                    // }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body x-data="{ sidebarOpen: true }" 
      class="dark bg-bg-dark text-gray-custom min-h-screen transition-all duration-200">
    
    <!-- Header -->
    <header class="fixed top-0 z-30 w-full bg-bg-dark border-b border-gray-700 shadow">
        <div class="flex items-center justify-between h-16 px-4">
            <!-- Toggle sidebar button -->
            <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            
            <div>
                <h1 class="text-xl font-bold text-gray-custom">Pazar Admin</h1>
            </div>
            
            <div>
                <!-- Placeholder for future user profile dropdown -->
                <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <div class="flex pt-2">
        <aside 
            :class="sidebarOpen ? 'translate-x-0 w-64' : 'w-0 -translate-x-full'"
            class="fixed z-20 inset-y-0 left-0 mt-16 transition-all duration-300 transform h-full bg-bg-dark border-r border-gray-700 overflow-hidden">
            
            <nav class="p-4 space-y-2 overflow-y-auto h-full">
                <a href="{{ route('dashboard') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-700 
                        {{ request()->routeIs('dashboard') ? 'bg-gray-700' : '' }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">Dashboard</span>
                </a>
                
                <a href="{{ route('users.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-700 
                        {{ request()->routeIs('users.*') ? 'bg-gray-700' : '' }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">Pengguna</span>
                </a>
                
                <a href="{{ route('roles.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-700 
                        {{ request()->routeIs('roles.*') ? 'bg-gray-700' : '' }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <span :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">Role</span>
                </a>

                <a href="{{ route('divisions.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-700 
                        {{ request()->routeIs('divisions.*') ? 'bg-gray-700' : '' }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">Divisi</span>
                </a>
                
                <a href="{{ route('positions.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-700 
                        {{ request()->routeIs('positions.*') ? 'bg-gray-700' : '' }}">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span :class="sidebarOpen ? 'opacity-100' : 'opacity-0'">Jabatan</span>
                </a>
            </nav>
        </aside>

        <!-- Content -->
        <main 
            :class="sidebarOpen ? 'ml-64' : 'ml-0'"
            class="flex-1 min-h-screen p-6 pt-20 transition-all duration-300">
            <div class="mb-6">
                <h1 class="text-2xl font-bold">@yield('page-title', 'Dashboard')</h1>
            </div>
            
            @if(session('success'))
                <div class="p-4 mb-6 text-green-100 bg-green-800 border-l-4 border-green-500" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            
            @if(session('error'))
                <div class="p-4 mb-6 text-red-100 bg-red-800 border-l-4 border-red-500" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif
            
            @yield('content')
        </main>
    </div>
</body>
</html>