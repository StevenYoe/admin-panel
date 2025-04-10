<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pazar Admin')</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
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
                }
            }
        }
    </script>
</head>
<body class="dark bg-bg-dark text-gray-custom min-h-screen">
    @yield('content')
</body>
</html>