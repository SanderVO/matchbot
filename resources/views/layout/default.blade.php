<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <script src="https://use.fontawesome.com/8538f97d18.js"></script>
    <script src="//unpkg.com/alpinejs" defer></script>

    @vite('resources/css/app.css')

    @livewireStyles
</head>

<body
    x-data="{sidebarVisible: true, createUserModalVisible: false, createOrganizationModalVisible: false, createSeasonModalVisible: false, createEventModalVisible: false}">
    @include('layout.header')

    @include('layout.sidebar')

    <div class="container mx-auto">
        @yield('content')
    </div>

    @include('layout.footer')

    @livewireScripts
</body>

</html>