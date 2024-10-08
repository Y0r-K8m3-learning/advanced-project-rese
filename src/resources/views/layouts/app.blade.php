<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">

    <link rel="stylesheet" href="{{ asset('css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('css/icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    @yield('css')
    @yield('js')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <!-- Header (固定) -->
    <div class="header" style="position: fixed; top: 0; width: 100%; z-index: 1000; background-color: white; ">
        <div class="d-flex align-items-center">
            <!-- メニューボタンとテキスト -->
            <div class="menu-button-container d-flex align-items-center" onclick="toggleMenu()">
                <div class="menu-button bg-blue-500 border-start-0" id="menuButton" style="cursor: pointer;">
                    <div class="menu-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
                <div id="menuText" style="margin-left: 10px; color: #007bff; font-size: 24px;">
                    <h1 class="fw-bold">Rese</h1>
                </div>
            </div>
        </div>

        <!-- ハンバーガーメニュー -->
        <div class="side-menu" id="sideMenu" style="position: fixed; top: 0; left: 0; width: 250px; height: 100vh; background-color: #f8f9fa; transform: translateX(-100%); transition: transform 0.3s;">
            <div class="close-button" onclick="toggleMenu()" style="cursor: pointer; font-size: 24px; margin: 10px;">×</div>
            <a class="nav-link active" href="/">Home</a>
            @if (Auth::check())
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                    <div class="nav-link">
                        {{ __('Log Out') }}
                    </div>
                </x-dropdown-link>
            </form>
            <a class="nav-link" href="/mypage">Mypage</a>
            @else
            <a class="nav-link" href="/register">Registration</a>
            <a class="nav-link" href="/login">Login</a>
            @endif
        </div>
    </div>

    <!-- コンテンツ  -->
    <div class="content-container" style="margin-top: 70px; padding: 20px; height: calc(100vh - 80px); overflow-y: auto;">
        @isset($header)
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script>
        function toggleMenu() {
            var menu = document.getElementById('sideMenu');
            var button = document.getElementById('menuButton');
            var text = document.getElementById('menuText');

            if (menu && button) {
                menu.classList.toggle('open');
                if (menu.classList.contains('open')) {
                    menu.style.transform = 'translateX(0)';
                    button.style.display = 'none';
                    text.style.display = 'none';
                } else {
                    menu.style.transform = 'translateX(-100%)';
                    button.style.display = 'flex';
                    text.style.display = 'flex';
                }
            } else {
                console.error('Menu or button element not found.');
            }
        }
    </script>
</body>



</html>