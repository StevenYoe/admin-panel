<!--
    Login Page for Pazar User Admin
    - Extends the 'layouts.auth' layout
    - Displays a login form for users to authenticate
    - Shows validation errors if present
    - Uses POST method to submit credentials to the 'login.post' route
    - Includes CSRF protection
    - Styled with Tailwind CSS classes
-->
@extends('layouts.auth')

@section('title', 'Login - Pazar User Admin')

@section('content')
<!-- Centered container for the login form -->
<div class="flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-md p-6 bg-bg-dark border border-gray-700 rounded-lg shadow-lg">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-custom">Pazar User Admin Login</h1>
        </div>

        <!-- Display validation errors if any -->
        @if ($errors->any())
            <div class="p-4 mb-6 text-red-100 bg-red-800 border-l-4 border-red-500" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Login form starts here -->
        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="mb-4">
                <!-- Email input field -->
                <label for="email" class="block mb-2 text-sm font-medium text-gray-custom">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                    class="w-full p-2.5 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-accent focus:border-accent"
                    placeholder="name@company.com">
            </div>
            
            <div class="mb-6">
                <!-- Password input field -->
                <label for="password" class="block mb-2 text-sm font-medium text-gray-custom">Password</label>
                <input type="password" id="password" name="password" required
                    class="w-full p-2.5 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-accent focus:border-accent"
                    placeholder="••••••••">
            </div>

            <!-- Submit button -->
            <button type="submit" 
                class="w-full px-5 py-2.5 text-sm font-medium text-white bg-accent hover:bg-red-800 focus:ring-4 focus:ring-red-300 rounded-lg">
                Log in
            </button>
        </form>
    </div>
</div>
<!-- End of login form container -->
@endsection