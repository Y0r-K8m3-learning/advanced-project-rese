@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection
@section('js')
<script src="{{ asset('js/detail.js') }}"></script>
@endsection

<x-app-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="container">
        <div class="left-half fw-bold" style="flex: 1;">
            <div class="mb-4 mt-3">
                <a href="{{ route('home') }}" class="border back-button ">&lt;</a>
                <span class="pl-2 fs-4">{{ $restaurant['name'] }}</span>
            </div>
            <img src="{{ $restaurant['image_url'] }}" class="h-50 w-100" alt="{{ $restaurant['title'] }}">
            <div class="flex items-center">
                <p class="card-hash">#{{ $restaurant['area']['name'] }}</p>
                <p class="card-hash p-2">#{{ $restaurant['genre']['name'] }}</p>
            </div>
            <div>
                <p class="card-text">{{ $restaurant['description'] }}</p>
            </div>

            <!-- 5段階評価ボタン -->
            <button type="button" class="btn btn-secondary mt-3" id="rateButton">評価</button>
        </div>

        <div class="right-half bg-primary rounded shadow d-flex flex-column justify-content-between">
            <!-- エラーメッセージの表示 -->
            @if ($errors->has('error'))
            <div class="alert alert-danger">
                {{ $errors->first('error') }}
            </div>
            @endif
            <h2 class="text-white fs-5 fw-bold ps-3 mt-3">予約</h2>

            <div class="d-flex flex-column w-100 flex-grow-1 mt-3">
                <form method="POST" action="{{ route('paymentindex') }}">
                    @csrf
                    <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">

                    <div class="form-group">
                        <input type="date" id="date" name="date" class="form-control w-50 ms-4 rounded" required value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}">
                        <x-input-error :messages="$errors->get('date')" class="mt-2" />
                    </div>

                    <div class="form-group mt-2">
                        <select id="time" name="time" class="form-control w-75 ms-4" required>
                            @php
                            $currentTime = date('H:i');
                            $currentMinutes = date('i');
                            $roundedMinutes = ($currentMinutes < 30) ? '00' : '30' ;
                                $selectedTime=date('H') . ':' . $roundedMinutes;
                                for ($i=0; $i < 24 * 2; $i++) {
                                $hours=str_pad(floor($i / 2), 2, '0' , STR_PAD_LEFT);
                                $minutes=str_pad(($i % 2) * 30, 2, '0' , STR_PAD_LEFT);
                                $timeValue=$hours . ':' . $minutes;
                                $selected=($timeValue==$selectedTime) ? 'selected' : '' ;
                                echo "<option value=\" $timeValue\" $selected>$timeValue</option>";
                                }
                                @endphp
                        </select>
                        <x-input-error :messages="$errors->get('time')" class="mt-2 pl-9" />
                    </div>

                    <div class="form-group mt-2">
                        <select id="number" name="number" class="form-control w-75 ms-4" required>
                            @for ($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}">{{ $i }}人</option>
                                @endfor
                        </select>
                        <x-input-error :messages="$errors->get('number')" class="mt-2 pl-9" />
                    </div>

                    <div class="mt-3 reserve-content w-75 ms-4 text-left ps-3 rounded text-light ">
                        <div class="reserve-content-item mt-5 mb-3 pt-3">
                            Shop <span id="shop-name" class="ms-5">{{ $restaurant['name'] }}</span>
                        </div>
                        <div class="reserve-content-item  mb-3">
                            Date <span id="selected-date" class="ms-5">{{ date('Y-m-d') }}</span>
                        </div>
                        <div class="reserve-content-item mb-3">
                            Time <span id="selected-time" class="ms-5">{{ $selectedTime }}</span>
                        </div>
                        <div class="reserve-content-item mb-3 pb-2">
                            Number <span id="selected-number" class="ms-4">1人</span>
                        </div>
                    </div>
            </div>
            <!-- 予約ボタン -->
            <div class="under-right-content">
                <button type="submit" class="btn-reserve  w-100 text-white shadow rounded" id="reservation-button">予約する</button>
            </div>
            </form>

        </div>


    </div>

    <!-- 評価とコメントのモーダル -->
    <div class="modal fade" id="rateModal" tabindex="-1" aria-labelledby="rateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="rateForm" method="POST" action="{{ route('restaurant.rate', $restaurant->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="rateModalLabel">評価とコメント</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- 5段階評価の星 -->
                        <div class="form-group">
                            <label for="rating">評価 (1-5):</label>
                            <select id="rating" name="rating" class="form-control" required>
                                <option value="1">1 - 悪い</option>
                                <option value="2">2 - あまり良くない</option>
                                <option value="3" selected>3 - 普通</option> <!-- デフォルトを「普通」に設定 -->
                                <option value="4">4 - 良い</option>
                                <option value="5">5 - 非常に良い</option>
                            </select>
                        </div>
                        <!-- コメント入力 -->
                        <div class="form-group">
                            <label for="comment">コメント:</label>
                            <textarea id="comment" name="comment" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                        <button type="submit" class="btn btn-primary">送信</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>