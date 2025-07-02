<!--
    Authentication Layout (auth.blade.php)
    - Provides a minimal layout for authentication pages (login, register, etc.)
    - Includes Tailwind CSS for styling and SweetAlert2 for notifications
    - Sets up dark mode and custom color themes
    - Displays dynamic content via @yield('content')
    - Shows SweetAlert2 notifications if present in the session
-->
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <!-- Meta and title setup -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pazar User Admin')</title>
    <link rel="icon" href="img/Logo.ico" type="image/x-icon">
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 for notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Tailwind theme configuration
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'accent': '#BF161C',
                        'gray-custom': '#E0FBFC',
                        'bg-dark': '#253237',
                    }
                }
            }
        }
    </script>
</head>
<body class="dark bg-bg-dark text-gray-custom min-h-screen">
    <!-- Main content for authentication pages -->
    @yield('content')
    <!-- SweetAlert2 notification script -->
    <script>
        @if(session('swal_msg'))
            Swal.fire({
                icon: '{{ session('swal_type', 'info') }}',
                title: '{{ session('swal_title', 'Notification') }}',
                text: '{{ session('swal_msg') }}',
                timer: {{ session('swal_timer', 3000) }}
            });
        @endif
    </script>
</body>
</html>