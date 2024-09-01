@section('css')
<link rel="stylesheet" href="{{ asset('css/card.css') }}">
@endsection
<x-app-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <form method="POST" action="{{ route('login') }}">
    </form>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="container mt-5">
            <div class="row">
                @foreach([
                [
                'image' => 'https://via.placeholder.com/300x200',
                'title' => 'カードタイトル1',
                'description' => 'カード説明1'
                ],
                [
                'image' => 'https://via.placeholder.com/300x200',
                'title' => 'カードタイトル2',
                'description' => 'カード説明2'
                ],
                [
                'image' => 'https://via.placeholder.com/300x200',
                'title' => 'カードタイトル3',
                'description' => 'カード説明3'
                ]
                ] as $card)
                <div class="col-md-4">
                    <div class="card">
                        <img src="{{ $card['image'] }}" class="card-img-top" alt="{{ $card['title'] }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $card['title'] }}</h5>
                            <p class="card-text">{{ $card['description'] }}</p>
                            <button type="submit" class="btn btn-primary">詳しく見る</button>
                            <a href="#" class="btn btn-secondary">ハートマーク</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </form>
</x-app-layout>