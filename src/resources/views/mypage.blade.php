@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
<link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
@endsection

<x-app-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="container">
        <!-- ページ左半分: 予約状況 -->
        <div class="left-half">
            <h2 class="fs-4 fw-bolder mt-5 mb-1">予約状況</h2>
            <div class="reservation-list ">
                @foreach ($reservations as $reservation)
                <div class="mb-3 bg-primary rounded p-1 reservation-item text-white"
                    id="reservation-{{ $reservation->id }}">
                    <div class="flex  ms-3 mb-1">
                        <span class="material-symbols-outlined fs-5">
                            schedule
                        </span>予約{{$loop->iteration}}
                    </div>
                    <div class="mb-3">
                        <div>
                            <span class="border border-2   remove-button" data-id="{{ $reservation->id }}">×</span>
                        </div>
                        <p><strong class="ms-2 me-5">Shop:</strong> {{ $reservation->restaurant->name }}</p>
                        <p><strong class="ms-2 me-5">Date:</strong> {{ $reservation->reservation_date }}</p>
                        <p><strong class="ms-2 me-5">Time:</strong> {{ $reservation->formatted_time }}</p>
                        <p><strong class="ms-2 me-4">Number:</strong> {{ $reservation->number_of_people }}人</p>
                    </div>

                    <!-- QRコード表示ページへのリンク -->
                    <a href="{{ route('reservation.qrcode', $reservation->id) }}" class="btn btn-info ms-2">予約QR</a>


                    <!-- 予約編集ボタン -->
                    <button type="button" class="btn btn-secondary edit-button" data-id="{{ $reservation->id }}"
                        data-date="{{ $reservation->reservation_date }}" data-time="{{ $reservation->formatted_time }}"
                        data-number="{{ $reservation->number_of_people }}">編集</button>
                </div>
                @endforeach
            </div>
        </div>

        <!-- ページ右半分: お気に入り店舗 -->
        <div class="right-half ms-4 mt-2">
            <div class="username mb-2 fs-4 fw-bolder">{{ Auth::user()->name }}さん</div>
            <h2 class="mb-2 fs-4 fw-bolder">お気に入り店舗</h2>
            <div class="favorite-list row ">
                @foreach ($favorites as $favorite)
                <div class="col-12 col-md-6 mb-4">
                    <!-- カラムサイズを指定 -->
                    <div class="card shadow  w-100 h-100" id="favorite-{{ $favorite->restaurant->id }}">
                        <img src="{{ $favorite->restaurant->image_url }}" alt="{{ $favorite->restaurant->name }}">
                        <div class="details">
                            <h5 class="card-title mt-2 ms-4 fs-4 fw-bold">{{ $favorite->restaurant->name }}</h3>
                                <div class="ms-1 row fw-bold">
                                    <div class="col-4 col-md-4 w-50">
                                        #{{ $favorite->restaurant->area->name }}
                                    </div>
                                    <div class="col-3 col-md-4 w-50">
                                        #{{ $favorite->restaurant->genre->name }}
                                    </div>
                                    <p hidden>{{ $favorite->restaurant->description }}</p>
                                </div>
                        </div>
                        <form method="GET" action="{{ route('restaurant.detail', $favorite->restaurant->id) }}">
                            @csrf
                            <div class="ms-1 d-flex align-items-center justify-content-between fw-bold">
                                <button type="submit" class="btn btn-primary m-1">詳しく見る</button>
                                <span class="heart favorited me-4" data-id="{{ $favorite->restaurant->id }}"></span>

                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- 予約変更モーダル -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editReservationForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">予約変更</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="restaurant_id" id="restaurant_id">

                        <div class="form-group">
                            <label for="edit-date">日付</label>
                            <input type="date" id="edit-date" name="date" class="form-control" required
                                value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}">
                        </div>
                        <div class=" form-group">
                            <label for="edit-time">時間</label>
                            <select id="edit-time" name="time" class="form-control" required>
                                @php
                                $currentTime = date('H:i');
                                $currentMinutes = date('i');
                                // 30分単位の丸め
                                $roundedMinutes = ($currentMinutes < 30) ? '00' : '30' ; $selectedTime=date('H') . ':' .
                                    $roundedMinutes; for ($i=0; $i < 24 * 2; $i++) { $hours=str_pad(floor($i / 2),
                                    2, '0' , STR_PAD_LEFT); $minutes=str_pad(($i % 2) * 30, 2, '0' , STR_PAD_LEFT);
                                    $timeValue=$hours . ':' . $minutes; $selected=($timeValue==$selectedTime)
                                    ? 'selected' : '' ; echo "<option value=\" $timeValue\" $selected>$timeValue
                                    </option>";
                                    }
                                    @endphp
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit-number">人数</label>
                            <select id="edit-number" name="number_of_people" class="form-control" required>
                                @for ($i = 1; $i <= 10; $i++) <option value="{{ $i }}">{{ $i }}人</option>
                                    @endfor
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                        <button type="submit" class="btn btn-primary">変更</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    $(document).ready(function() {
        // モーダルのトリガー
        $('.edit-button').click(function() {
            var reservationId = $(this).data('id');
            var reservationDate = $(this).data('date');
            var reservationTime = $(this).data('time');
            var reservationNumber = $(this).data('number');

            $('#editModal #edit-date').val(reservationDate);
            $('#editModal #edit-number').val(reservationNumber);
            $('#editReservationForm').attr('action', '/reservations/' + reservationId);

            // 時間の選択肢を生成
            updateTimes(reservationDate, reservationTime);

            $('#editModal').modal('show');
        });

        // モーダルの閉じる機能
        $('#editModal').on('hidden.bs.modal', function() {
            $(this).find('form').trigger('reset');
        });

        // モーダルのクローズボタン
        $('.close, .btn-secondary').click(function() {
            $('#editModal').modal('hide');
        });

        // 日付変更時に時間の選択肢を更新
        $('#edit-date').change(function() {
            var selectedDate = $(this).val();
            updateTimes(selectedDate, null);
        });

        function updateTimes(selectedDate, selectedTime) {
            var timeSelect = $('#edit-time');
            timeSelect.empty();

            var now = new Date();
            var currentTime = now.getHours() * 60 + now.getMinutes(); // 現在の時間を分単位で取得
            var today = new Date().toISOString().split('T')[0]; // 今日の日付を取得

            for (var i = 0; i < 24 * 2; i++) {
                var hours = Math.floor(i / 2).toString().padStart(2, '0');
                var minutes = (i % 2 === 0) ? '00' : '30';
                var optionValue = hours + ':' + minutes;
                var optionTime = parseInt(hours) * 60 + parseInt(minutes);

                var option = $('<option>').val(optionValue).text(optionValue);

                // 選択日が今日の場合、現在時刻以降のみ選択可能にする
                if (selectedDate === today && optionTime < currentTime) {
                    option.prop('disabled', true);
                }

                timeSelect.append(option);
            }

            if (selectedTime) {
                timeSelect.val(selectedTime); // 既存の予約時間を選択状態にする
            }
        }

        // お気に入り解除機能
        $('.heart').click(function() {
            var restaurantId = $(this).data('id');
            var heartElement = $(this);

            $.ajax({
                url: '/restaurants/' + restaurantId + '/unfavorite',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#favorite-' + restaurantId).remove(); // お気に入りのカードを削除
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });

        // 予約削除機能
        $('.remove-button').click(function() {
            var reservationId = $(this).data('id');

            $.ajax({
                url: '/reservations/' + reservationId + '/delete',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#reservation-' + reservationId).remove(); // 予約のリストアイテムを削除
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });

        $('#showQrCodeButton').click(function() {
            var reservationId = $(this).data('id');
            $.ajax({
                url: '/reservations/' + reservationId + '/qrcode',
                type: 'GET',
                success: function(response) {
                    $('#qrCode').html(response.qrCode); // QRコードを表示

                    console.log(response);
                    $('#qrCodeArea').show(); // QRコードエリアを表示
                },
                error: function(xhr) {
                    console.error(xhr);
                }
            });
        });
    });
</script>