<!--
    Dashboard Page
    - Extends the main application layout
    - Displays summary cards for users, roles, divisions, and positions
    - Shows statistics for active users and new users this month
    - Includes tables for users per division and users per position
    - Uses Blade components for cards and tables
    - Styled with Tailwind CSS utility classes
-->
@extends('layouts.app')

@section('title', 'Dashboard - Pazar User Admin')

@section('page-title', 'Dashboard')

@section('content')
    <!-- Top Cards Row: User, Role, Division, Position statistics -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
        <!-- Card 1: Total Users -->
        <x-card class="border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-3 mr-4 rounded-full bg-purple-500 bg-opacity-10">
                    <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Users</p>
                    <p class="text-lg font-semibold">{{ $userCount ?? 0 }}</p>
                </div>
            </div>
        </x-card>

        <!-- Card 2: Roles -->
        <x-card class="border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 mr-4 rounded-full bg-blue-500 bg-opacity-10">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Roles</p>
                    <p class="text-lg font-semibold">{{ $roleCount ?? 0 }}</p>
                </div>
            </div>
        </x-card>

        <!-- Card 3: Divisions -->
        <x-card class="border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 mr-4 rounded-full bg-green-500 bg-opacity-10">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Divisions</p>
                    <p class="text-lg font-semibold">{{ $divisionCount ?? 0 }}</p>
                </div>
            </div>
        </x-card>

        <!-- Card 4: Positions -->
        <x-card class="border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="p-3 mr-4 rounded-full bg-yellow-500 bg-opacity-10">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Positions</p>
                    <p class="text-lg font-semibold">{{ $positionCount ?? 0 }}</p>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Mid Cards Row: Active users and new users this month -->
    <div class="grid grid-cols-1 gap-6 mt-6 md:grid-cols-2">
        <!-- Active Users Card -->
        <x-card class="border-l-4 border-cyan-500">
            <div class="flex items-center">
                <div class="p-3 mr-4 rounded-full bg-cyan-500 bg-opacity-10">
                    <svg class="w-6 h-6 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Users</p>
                    <p class="text-lg font-semibold">{{ $activeUserCount ?? 0 }}</p>
                </div>
            </div>
        </x-card>

        <!-- New Users This Month Card -->
        <x-card class="border-l-4 border-pink-500">
            <div class="flex items-center">
                <div class="p-3 mr-4 rounded-full bg-pink-500 bg-opacity-10">
                    <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">New Users This Month</p>
                    <p class="text-lg font-semibold">{{ $newUsersThisMonth ?? 0 }}</p>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Table Section: Users per division and position -->
    <div class="grid grid-cols-1 gap-6 mt-6 md:grid-cols-2">
        <!-- Users Per Division Table -->
        <x-card>
            <h2 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300">Users Per Division</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-center">Division</th>
                            <th scope="col" class="px-6 py-3 text-center">User Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usersPerDivision ?? [] as $division)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-5 py-4 text-center">{{ $division->div_name }}</td>
                            <td class="px-5 py-4 text-center">{{ $division->user_count }}</td>
                        </tr>
                        @empty
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td colspan="2" class="px-5 py-4 text-center">No data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <!-- Users Per Position Table -->
        <x-card>
            <h2 class="mb-4 text-lg font-semibold text-gray-700 dark:text-gray-300">Users Per Position</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-center">Position</th>
                            <th scope="col" class="px-6 py-3 text-center">User Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usersPerPosition ?? [] as $position)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-5 py-4 text-center">{{ $position->pos_name }}</td>
                            <td class="px-5 py-4 text-center">{{ $position->user_count }}</td>
                        </tr>
                        @empty
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td colspan="2" class="px-5 py-4 text-center">No data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
@endsection