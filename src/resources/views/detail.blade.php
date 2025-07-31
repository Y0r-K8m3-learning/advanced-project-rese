@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection
@section('js')
<script>
    window.csrfToken = "{{ csrf_token() }}";
</script>
<script src="{{ asset('js/detail.js') }}">

</script>
@endsection
<x-app-layout>
    <div style="display: none" id="data" data-restaurant-id="{{ $restaurant->id }}"
        data-user-id="{{Auth::check() ? Auth::id() : '' }}"
        data-isgeneraluser-id="{{ Auth::check()? Auth::user()->isUser() ?? null: '' }}"
        data-isadminuser-id="{{Auth::check()?Auth::user()->isAdmin() ?? null: '' }}">
    </div>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center ">
                <div class="back-button me-1">
                    <a href="{{ route('home') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                            class="bi bi-chevron-left" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z" />
                        </svg>
                    </a>
                </div>
                <span class="fs-3 fw-bold">{{ $restaurant['name'] }}</span>
            </div>
            <div class="favorite-section">
                @if (Auth::check())
                <span class="heart {{ in_array($restaurant->id, $favoriteRestaurantIds ?? []) ? 'favorited' : '' }}"
                    data-id="{{ $restaurant->id }}" title="お気に入り"></span>
                @else
                <span class="heart" data-id="{{ $restaurant->id }}" title="お気に入り（ログインが必要）"></span>
                @endif
            </div>
        </div>
        <div class="d-flex flex-column flex-md-row align-items-stretch">
            <div class="left-half d-flex flex-column fw-bold w-100 w-md-50 p-3 h-100" style="min-width:0;">

                <div>
                    <img src="{{ $restaurant['image_url'] }}" class="h-50 w-100 " alt="{{ $restaurant['title'] }}">
                </div>
                <div class="flex items-center">
                    <p class="card-hash">#{{ $restaurant['area']['name'] }}</p>
                    <p class="card-hash p-2">#{{ $restaurant['genre']['name'] }}</p>
                </div>
                <div>
                    <p class="card-text">{{ $restaurant['description'] }}</p>
                </div>

                @if(Auth::check())
                @if(Auth::user()->isUser())
                <div class="mt-3">
                    @if($hasReviewed)
                    <sub class="mt-3 text-gray-600 border-gray">口コミを投稿済みです</sub>
                    @elseif(!$isPastReservationExists)
                    <sub class="mt-3 text-gray-600 border-gray">予約終了日以降に口コミが投稿できます</sub>
                    @else
                    <a href="{{ route('review.create',$restaurant->id) }}"><span
                            class="border-bottom border-3 text-black-100 border-gray ">口コミを投稿する</span></a>


                    @endif
                </div>
                @elseif(Auth::user()->isAdmin() || Auth::user()->isOwner())
                <sub class="mt-3 text-gray-600 border-gray">一般ユーザのみ口コミが投稿できます</sub>
                @endif
                @else
                <div class="mt-3">
                    <sub class="mt-3 text-gray-600 border-gray">ログイン後、予約終了日以降に口コミが投稿できます</sub>
                </div>

                @endif
                <button type="button" data-restaurant-id="{{ $restaurant->id }}" class="btn btn-primary mt-3 w-100"
                    id="rate-list">全ての口コミ情報</button>
                <hr class="mt-4">

                <div id="reviews" class="reviews" style="display: none;">
                    {{-- レビュー一覧非同期でセット --}}
                </div>

            </div>

            <div class="right-half reservation-section bg-primary rounded shadow d-flex flex-column justify-content-start w-100 w-md-50 p-4 h-100"
                style="min-width:0;">
                <!-- エラーメッセージの表示 -->
                @if ($errors->has('error'))
                <div class="alert alert-danger mb-3">
                    {{ $errors->first('error') }}
                </div>
                @endif
                <h2 class="text-white fs-4 fw-bold text-center mb-4">予約</h2>

                <div class="d-flex flex-column w-100 flex-grow-1 p-2">
                    <form method="POST" action="{{ route('paymentindex') }}">
                        @csrf
                        <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">

                        <div class="form-group mb-3">
                            <label for="date" class="form-label text-white mb-2">日付</label>
                            <input type="date" id="date" name="date" class="form-control reservation-input rounded"
                                required value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}">
                            <x-input-error :messages="$errors->get('date')" class="mt-2" />
                        </div>

                        <div class="form-group mb-3">
                            <label for="time" class="form-label text-white mb-2">時間</label>
                            <select id="time" name="time" class="form-control reservation-input rounded" required>
                                @php
                                $currentTime = date('H:i');
                                $currentMinutes = date('i');
                                $roundedMinutes = ($currentMinutes < 30) ? '00' : '30' ; $selectedTime=date('H') . ':' .
                                    $roundedMinutes; for ($i=0; $i < 24 * 2; $i++) { $hours=str_pad(floor($i / 2),
                                    2, '0' , STR_PAD_LEFT); $minutes=str_pad(($i % 2) * 30, 2, '0' , STR_PAD_LEFT);
                                    $timeValue=$hours . ':' . $minutes; $selected=($timeValue==$selectedTime)
                                    ? 'selected' : '' ; echo "<option value=\" $timeValue\" $selected>$timeValue
                                    </option>";
                                    }
                                    @endphp
                            </select>
                            <x-input-error :messages="$errors->get('time')" class="mt-2" />
                        </div>

                        <div class="form-group mb-3">
                            <label for="number" class="form-label text-white mb-2">人数</label>
                            <select id="number" name="number" class="form-control reservation-input rounded" required>
                                @for ($i = 1; $i <= 10; $i++) <option value="{{ $i }}">{{ $i }}人</option>
                                    @endfor
                            </select>
                            <x-input-error :messages="$errors->get('number')" class="mt-2" />
                        </div>

                        <div class="mt-4 reserve-content rounded text-light p-3">
                            <h5 class="text-white mb-3 fw-bold">予約内容</h5>
                            <div class="reserve-content-item mb-2">
                                <span class="fw-bold">店舗:</span> <span id="shop-name" class="ms-2">{{
                                    $restaurant['name'] }}</span>
                            </div>
                            <div class="reserve-content-item mb-2">
                                <span class="fw-bold">日付:</span> <span id="selected-date" class="ms-2">{{ date('Y-m-d')
                                    }}</span>
                            </div>
                            <div class="reserve-content-item mb-2">
                                <span class="fw-bold">時間:</span> <span id="selected-time" class="ms-2">{{ $selectedTime
                                    }}</span>
                            </div>
                            <div class="reserve-content-item mb-2">
                                <span class="fw-bold">人数:</span> <span id="selected-number" class="ms-2">1人</span>
                            </div>
                        </div>
                </div>
                <!-- 予約ボタン -->
                <div class="under-right-content mt-3">
                    <button type="submit" class="btn-reserve w-100 text-white shadow rounded fw-bold py-2"
                        id="reservation-button">予約する</button>
                </div>
                </form>
            </div>
        </div>


    </div>
</x-app-layout>