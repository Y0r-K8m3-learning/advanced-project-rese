@section('css')
<link rel="stylesheet" href="{{ asset('css/card.css') }}">
<link rel="stylesheet" href="{{ asset('css/restaurant.css') }}">


<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
@endsection

@section('js')

@endsection
<x-app-layout>
    <!-- Session Status -->
    <x-auth-session-status :status="session('status')" />

    <!-- 検索バー（固定） -->
    <div class="search-container w-75" id="all-content">
        <form method="GET" id="searchFrom" action="{{ route('restaurants.index') }}" class="form-inline pull-right">
            <div class="shadow search-item d-flex align-items-center ">
                <div class="form-group position-relative">
                    <select name="area" id="area" class="custom-select">
                        <option value="">All area</option>
                        @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ request('area') == $area->id ? 'selected' : '' }}>
                            {{ $area->name }}
                        </option>
                        @endforeach
                    </select>
                    <div class="vertical-line"></div>
                </div>

                <div class="form-group position-relative">
                    <select name="genre" id="genre" class="custom-select">
                        <option value="">All genre</option>
                        @foreach($genres as $genre)
                        <option value="{{ $genre->id }}" {{ request('genre') == $genre->id ? 'selected' : '' }}>
                            {{ $genre->name }}
                        </option>
                        @endforeach
                    </select>
                    <div class="vertical-line"></div>
                </div>

                <div class="form-group position-relative search-input w-100 bg-white">
                    <div class="flex">
                        <span class="material-symbols-outlined p-2 bg-white">
                            search
                        </span>
                        <input type="text" name="name" id="name" class="custom-input w-100 bg-white" value="{{ request('name') }}" placeholder="Search...">
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="container">
        <div class="row" id="search-result">
            @foreach($restaurants as $restaurant)
            <div class="col-md-4 col-lg-3 col-6 mb-4">
                <div class="card h-100 w-100 mt-0">
                    <img src="{{ $restaurant['image_url'] }}" class="card-img-top" alt="{{ $restaurant['name'] }}">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">{{ $restaurant['name'] }}</h5>
                        <p hidden class="card-text">{{ $restaurant['description'] }}</p>
                        <div class="row fs-10 fw-bold ">
                            <div class="col-3 col-md-4 w-50">
                                #{{ $restaurant['area']['name'] }}
                            </div>
                            <div class="col-3 col-md-4 w-50">
                                #{{ $restaurant['genre']['name'] }}
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <form method="GET" action="{{ route('restaurant.detail', $restaurant->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">詳しく見る</button>
                            </form>

                            @if (Auth::check())

                            <span class="heart {{ in_array($restaurant->id, $favoriteRestaurantIds) ? 'favorited' : '' }}"
                                data-id="{{ $restaurant->id }}"></span>
                            @else
                            <span class="heart" data-id="{{ $restaurant->id }} "></span>
                            @endif
                        </div>
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
                        heart.removeClass('favorited');
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            }
        });

        //inputイベント制御用
        let typingTimer;
        const doneTypingInterval = 700;

        $('#name').on('input', function() {
            clearTimeout(typingTimer);

            typingTimer = setTimeout(function() {
                Search();
            }, doneTypingInterval);
        });

        $('#area, #genre').on('change', function() {
            $('#searchFrom').submit();

        });



        function Search() {
            formData = {
                area: $('#area').val(),
                genre: $('#genre').val(),
                name: $('#name').val(),
            }

            $.ajax({
                url: "{{route('restaurants.index')}}",
                type: 'GET',
                data: formData,
                cache: true,
                success: function(data) {
                    $('body').html(data);
                    $('#name').off('input');
                    $('#area, #genre').off('change');

                    $('#name').focus();
                    var tmpStr = $('#name').val();
                    $('#name').val('');
                    $('#name').val(tmpStr);

                },
                error: function() {
                    alert('検索に失敗しました。');
                }
            });
        }


    });
</script>