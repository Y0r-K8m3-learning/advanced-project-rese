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
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="font-sans antialiased">
    <div class="header">

        <div class="hanguagur">
            <!-- ハンバーグメニュー -->

            <!-- メニューボタンとテキスト -->
            <div class="menu-button-container" onclick="toggleMenu()">
                <!-- 四角の中に三本線のメニューボタン -->
                <div class="menu-button bg-blue-500 border-start-0" id="menuButton">
                    <div class="menu-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>

                <!-- 「メニュー」テキスト -->
                <div id="menuText" style="margin-left: 10px; color: #007bff; font-size: 24px;">
                    <h1 class="fw-bold">Rese<h1>
                </div>

            </div>

        </div>
        <!-- ハンバーガーメニュー -->
        <div class="side-menu" id="sideMenu">
            <div class="close-button" onclick="toggleMenu()">×</div>
            <a class="nav-link active" href="/">Home</a>
            @if (Auth::check())
            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault();
                                                this.closest('form').submit();">
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


    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script>
        // メニューを開閉する関数
        function toggleMenu() {
            var menu = document.getElementById('sideMenu');
            var button = document.getElementById('menuButton');
            var text = document.getElementById('menuText');

            if (menu && button) { // 要素が存在するか確認
                menu.classList.toggle('open'); // 'open'クラスの追加/削除でメニューをスライドイン/アウト
                button.style.display = menu.classList.contains('open') ? 'none' : 'flex'; // メニューが開いているときはメニューボタンを非表示
                text.style.display = menu.classList.contains('open') ? 'none' : 'flex'; // メニューが開いているときはメニューボタンを非表示
            } else {
                console.error('Menu or button element not found.');
            }
        }

        // $(document).ready(function() {
        //     $('#exampleModal').on('show.bs.modal', function(event) {
        //         var button = $(event.relatedTarget); // モーダルを開くボタン
        //         var modal = $(this);
        //         modal.find('.modal-title').text('新しいモーダルタイトル');
        //         modal.find('.modal-body').text('新しいモーダルの内容');
        //     });
        // });
    </script>

</body>

</html>