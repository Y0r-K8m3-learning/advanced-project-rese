@section('css')
<link rel="stylesheet" href="{{ asset('css/card.css') }}">
<link rel="stylesheet" href="{{ asset('css/restaurant.css') }}">
<link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
@endsection

@section('js')
<script>
    window.csrfToken = "{{ csrf_token() }}";
    window.searchUrl = "{{ route('restaurants.index') }}";
</script>
<script src="{{ asset('js/restaurant.js') }}"></script>
@endsection

<x-app-layout>
    <!-- Session Status -->
    <x-auth-session-status :status="session('status')" />

    <!-- 検索バー（固定） -->
    <div class="search-container w-75 w-md-100 mt-2 mb-2" id="all-content">
        <form method="GET" id="searchFrom" action="{{ route('restaurants.index') }}" class="form-inline pull-right">
            <div class="shadow search-item d-flex align-items-center w-100 rounded bg-white"
                style=" height: 40px; padding: 0; ">
                <div class="form-group position-relative mt-3">
                    <div class="d-flex align-items-center mb-3 ">
                        <label for="sort" class="mb-0 bg-white ps-1 pt-2 pb-2"
                            style="width: 110px; height: 40px; vertical-align: middle;">並び替え:</label>
                        <select name="sort" id="sort" class="form-select border-0 rounded-0 w-75" style="height: 40px;">
                            @php
                            $sortOptions = [
                            ['sorttype' => 'random', 'text' => 'ランダム'],
                            ['sorttype' => 'asc', 'text' => '評価が高い順'],
                            ['sorttype' => 'desc', 'text' => '評価が低い順'],
                            ];
                            @endphp
                            @foreach($sortOptions as $sortOption)
                            <option value="{{ $sortOption['sorttype'] }}" {{ request('sort')==$sortOption['sorttype']
                                ? 'selected' : '' }}>
                                {{ $sortOption['text'] }}
                            </option>
                            @endforeach

                        </select>
                    </div>
                    <div class="vertical-line order-outline"></div>

                </div>
                <div class="form-group position-relative">
                    <select name="area" id="area" class="custom-select">
                        <option value="">All area</option>
                        @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ request('area')==$area->id ? 'selected' : '' }}>
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
                        <option value="{{ $genre->id }}" {{ request('genre')==$genre->id ? 'selected' : '' }}>
                            {{ $genre->name }}
                        </option>
                        @endforeach
                    </select>
                    <div class="vertical-line"></div>
                </div>

                <div class="form-group position-relative search-input bg-white ">
                    <div class="d-flex">
                        <span class="material-symbols-outlined ps-1 pt-2 pb-2 bg-white "
                            style="caret-color: transparent; margin: 0;">
                            search
                        </span>
                        <input type="text" name="name" id="name" class="custom-input  bg-white" style=""
                            value="{{ request('name') }}" placeholder="Search...">

                    </div>
                </div>
            </div>
        </form>
    </div>
    {{-- 管理者のみ表示 --}}
    @if (Auth::check() && Auth::user()->isAdmin())
    <div class="container">
        <form method="post" action="{{ route('admin.csv.store') }}" enctype="multipart/form-data">
            @csrf
            <label name="csvFile" class="bg-info rounded-top p-1">店舗一括登録(管理者用)
            </label>
            <div class="border border-info border-2 rounded p-3">
                ファイルを選択後、アップロードボタンを押してください。
                <input type="file" name="csvFile" class="ms-3" id="csvFile" />
                <button type="submit" class="btn btn-primary">アップロード</button>

                @error('csvFile')
                <div class="alert alert-danger">{{ $message }}</div>
                @enderror

                @if (session('error'))
                <div class="alert alert-danger mt-3">
                    {{ session('error') }}
                    <ul>
                        @if(session('errors'))
                        @foreach (session('errors')->all() as $dataError)
                        <li>-{{ $dataError }}</li>
                        @endforeach
                        @endif
                    </ul>
                </div>
                @endif

                {{-- 完了メッセージ --}}
                @if (session('import_complete'))
                <div class="alert alert-success mt-3">
                    {{ session('import_complete') }}
                </div>
                @endif
            </div>



        </form>

    </div>
    @endif
    <div class="container ">
        <div class="row " id="search-result">
            @foreach($restaurants as $restaurant)
            <div class="col-md-4 col-lg-3 col-6 mb-4">
                <div class="card h-100 w-100 mt-0">
                    <img src="{{ $restaurant['image_url'] }}" class="card-img-top" alt="{{ $restaurant['name'] }}">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">{{ $restaurant['name'] }}<sup style="font-size: smaller;"><span
                                    class="text-warning">★</span>
                                @if($restaurant['reviews_avg_rating'])
                                <span class="fs-6">{{ $restaurant['reviews_avg_rating'] }}</span>
                                @else
                                <sup>投稿なし</sup>
                                @endif
                            </sup>
                        </h5>
                        <p hidden class="card-text">{{ $restaurant['description'] }} </p>

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

                            <span
                                class="heart {{ in_array($restaurant->id, $favoriteRestaurantIds) ? 'favorited' : '' }}"
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