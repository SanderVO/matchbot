<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <script src="https://kit.fontawesome.com/cc05701db1.js" crossorigin="anonymous"></script>

    @vite('resources/css/app.css')

    @livewireStyles
</head>

<body class="bg-slate-950 text-white"
    x-data="{sidebarVisible: false, createUserModalVisible: false, createSeasonModalVisible: false}">
    <div class="relative min-h-screen">
        @include('layout.header')

        @include('layout.sidebar')

        <div class="container mx-auto pt-16 pb-16">
            @yield('content')
        </div>

        @include('layout.footer')
    </div>

    @livewireScripts
</body>

</html>