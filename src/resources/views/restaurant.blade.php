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
            <!-- 上段：並び替え、エリア、ジャンル -->
            <div class="shadow search-item-top d-flex align-items-center w-100 rounded-top bg-white mb-1"
                style="height: 40px; padding: 0;">
                <div class="form-group position-relative mt-3">
                    <div class="d-flex w-100 align-items-center mb-3">
                        <label for="sort" class="mb-0 bg-white ps-1 pt-2 pb-2 sort-label"
                            style="width: 80px; height: 40px; vertical-align: middle;">並び替え:</label>
                        <select name="sort" id="sort" class="custom-select text-sm border-0 rounded-0 sort-select"
                            style="height: 40px;">
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
                <div class="form-group position-relative ">
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
                </div>
            </div>
            <!-- 下段：文字列検索 -->
            <div class="shadow search-item-bottom d-flex align-items-center w-100 rounded-bottom bg-white"
                style="height: 40px; padding: 0;">
                <div class="form-group position-relative search-input bg-white w-100">
                    <div class="d-flex w-100 align-items-center">
                        <span class="material-symbols-outlined ps-1 pt-2 pb-2 bg-white"
                            style="caret-color: transparent; margin: 0;">
                            search
                        </span>
                        <input type="text" name="name" id="name" class="custom-input bg-white " style=""
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
        <div class="row " id="restaurant-container" style="min-height: 800px;">
            @include('partials.restaurant-cards', ['restaurants' => $restaurants, 'favoriteRestaurantIds' =>
            $favoriteRestaurantIds])
        </div>



        <div id="loading" style="display:none; text-align:center;">
            読み込み中...
        </div>

        <!-- 最後まで読み込んだら表示 -->
        <div id="end-message" style="display:none; text-align:center;">
            全ての店舗が表示されました!!
        </div>
    </div>
</x-app-layout>