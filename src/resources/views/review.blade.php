@section('css')
<link rel="stylesheet" href="{{ asset('css/review.css') }}">
@endsection
@section('js')
<script src="{{ asset('js/review.js') }}"></script>
@endsection

<x-app-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    </div>
<div class="d-flex flex-row">
    <div class="row w-100">
        <!-- 左半分: レストラン情報 -->
        <div class="col-md-6 d-flex flex-column align-items-center">
            <div class="ms-4 col-md-10 col-lg-9 col-12 mb-4 rounded shadow">
                <img src="{{ $restaurant['image_url'] }}" class="card-img-top" alt="{{ $restaurant['name'] }}">
                <div class="card-body">
                    <h5 class="card-title fw-bold">{{ $restaurant['name'] }}</h5>
                    <p hidden class="card-text">{{ $restaurant['description'] }}</p>
                    <div class="row fs-10 fw-bold">
                        <div class="col-6">
                            #{{ $restaurant['area']['name'] }}
                        </div>
                        <div class="col-6">
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
                            <span class="heart" data-id="{{ $restaurant->id }}"></span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- 口コミフォーム -->
        <div class="col-md-6 bg-gray-100 d-flex flex-column justify-content-center">

            <div class="p-4">
                <form id="rateForm" method="POST" action="{{ route('review.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
                    <div class="mb-3">
                        <p class="fs-5" id="rateModalLabel" >体験を評価してください</p>
                    </div>
                    <div class="form-group mb-3">
                        <div class="rate-form">
                            @foreach(range(1,5) as $i)
                                <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" class="star" />
                                <label for="star{{ $i }}" class="star">★</label>
                            @endforeach
                       </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="comment" class="fs-3 fw-bold"><span>口コミを投稿</span></label>
                        <textarea id="comment" name="comment" class="form-control" placeholder="ガジュアルな夜のお出かけにおすすめのスポット" rows="3" maxlength="400" required></textarea>
                        {{-- エラー時にメッセージ表示 --}}
                        @error('comment')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                        <div>0/400[最高文字数]</div>
                        <label for="image" class="fs-3 fw-bold"><span>画像を追加</span></label>

                      <div id="drop-area" class="upload-area">
                            <span id="drop-message">
                                <p>クリックして画像を追加またはドラッグアンドドロップ</p>
                            </span>
                            <input type="file" id="file-input" name="image" accept="image/*" style="display:none;">
                            <img id="preview" src="" style="display:none; max-width: 100%; max-height: 180px; margin-top: 8px;" />              </div>

                <button type="submit" class="btn m-auto btn-primary rounded w-50">口コミを投稿</button>
                      
                </form>
            </div>
        </div>
    </div>
    
</div>

  

</x-app-layout>