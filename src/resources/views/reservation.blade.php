@section('css')
<link rel="stylesheet" href="{{ asset('css/card.css') }}">
<link rel="stylesheet" href="{{ asset('css/restaurant.css') }}">


@endsection

@section('js')

@endsection
<x-app-layout>
    ログイン{{Auth::check()}}
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />


    <div class="container mt-5">
        <div class="row">
            @foreach($restaurants as $restaurant)
            <div class="col-md-4">
                <div class="card">
                    <img src="{{ $restaurant['image_url'] }}" class="card-img-top" alt="{{ $restaurant['name'] }}">
                    <div class="card-body">

                        <h5 class="card-title">{{ $restaurant['name'] }}</h5>
                        <p class="card-text">{{ $restaurant['description'] }}</p>
                        <p class="card-hash">#{{ $restaurant['area']['name'] }}</p>
                        <p class="card-hash">#{{ $restaurant['genre']['name'] }}</p>
                        <form method="GET" action="{{ route('restaurant.detail', $restaurant->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">詳しく見る</button>
                        </form>


                        @if (Auth::check())
                        <!-- 初期表示時にお気に入り状態を確認してクラスを切り替える -->
                        <span class="heart {{ in_array($restaurant->id, $favoriteRestaurantIds) ? 'favorited' : '' }}"
                            data-id="{{ $restaurant->id }}"></span>
                        @else
                        <span class="heart" data-id="{{ $restaurant->id }} "></span> <!-- ログインしていない場合 -->
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
<script>
    $(document).ready(function() {
        $('.heart').click(function() {
            var heart = $(this);
            var restaurantId = heart.data('id');
            if (!heart.hasClass('favorited')) {
                // お気に入り追加
                $.ajax({
                    url: '/restaurants/' + restaurantId + '/favorite',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        console.log(response);

                        heart.addClass('favorited');
                    },
                    error: function(xhr) {
                        if (xhr.status === 401) {
                            // 未ログインならログイン画面にリダイレクト
                            window.location.href = '/login';
                        } else {
                            console.error(xhr.responseText);
                        }
                    }
                });
            } else {
                // お気に入り解除
                $.ajax({
                    url: '/restaurants/' + restaurantId + '/unfavorite',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        console.log(response);
                        heart.removeClass('favorited');
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            }
        });
    });
</script>