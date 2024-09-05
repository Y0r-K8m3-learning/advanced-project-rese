@section('css')
<link rel="stylesheet" href="{{ asset('css/card.css') }}">
<link rel="stylesheet" href="{{ asset('css/restaurant.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

@endsection

@section('js')

@endsection
<x-app-layout>
    ログイン{{Auth::check()}}
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <div class="container mt-4">
        <form method="GET" action="{{ route('restaurants.index') }}" class="form-inline">
            <div class="form-group mr-2">
                <label for="area" class="mr-2">県の検索</label>
                <select name="area" id="area" class="form-control">
                    <option value="">選択してください</option>
                    @foreach($areas as $area)
                    <option value="{{ $area->id }}" {{ request('area') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mr-2">
                <label for="genre" class="mr-2">ジャンルの検索</label>
                <select name="genre" id="genre" class="form-control">
                    <option value="">選択してください</option>
                    @foreach($genres as $genre)
                    <option value="{{ $genre->id }}" {{ request('genre') == $genre->id ? 'selected' : '' }}>{{ $genre->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 店名検索フィールドに検索アイコンを追加 -->
            <div class="form-group mr-2 search-input">
                <label for="name" class="mr-2">店名の検索</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ request('name') }}" placeholder="部分一致で検索">
                <i class="fas fa-search"></i>
            </div>

            <button type="submit" class="btn btn-primary">検索</button>
        </form>
    </div>

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