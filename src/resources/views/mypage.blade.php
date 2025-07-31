@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
<link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
@endsection

<x-app-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="container row">
        <!-- ページ左半分: 予約状況 -->
        <div class="left-half col-12 col-md-6">
            <h2 class="fs-4 fw-bolder mb-4 text-primary">予約状況</h2>
            <div class="reservation-list">
                @foreach ($reservations as $reservation)
                <div class="mb-4 reservation-item rounded shadow-sm" id="reservation-{{ $reservation->id }}">
                    <div class="reservation-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <span class="material-symbols-outlined text-primary me-2">schedule</span>
                            <span class="fw-bold text-primary">予約{{$loop->iteration}}</span>
                        </div>
                        <span class="remove-button" data-id="{{ $reservation->id }}" title="予約を削除">×</span>
                    </div>

                    <div class="reservation-content p-3">
                        <div class="reservation-info mb-3">
                            <div class="info-item mb-2">
                                <span class="info-label">店舗:</span>
                                <span class="info-value">{{ $reservation->restaurant->name }}</span>
                            </div>
                            <div class="info-item mb-2">
                                <span class="info-label">日付:</span>
                                <span class="info-value">{{ $reservation->reservation_date }}</span>
                            </div>
                            <div class="info-item mb-2">
                                <span class="info-label">時間:</span>
                                <span class="info-value">{{ $reservation->formatted_time }}</span>
                            </div>
                            <div class="info-item mb-2">
                                <span class="info-label">人数:</span>
                                <span class="info-value">{{ $reservation->number_of_people }}人</span>
                            </div>
                        </div>

                        <div class="reservation-actions d-flex gap-2">
                            <a href="{{ route('reservation.qrcode', $reservation->id) }}"
                                class="btn btn-info btn-sm">予約QR</a>
                            <button type="button" class="btn btn-secondary btn-sm edit-button"
                                data-id="{{ $reservation->id }}" data-date="{{ $reservation->reservation_date }}"
                                data-time="{{ $reservation->formatted_time }}"
                                data-number="{{ $reservation->number_of_people }}">編集</button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- ページ右半分: お気に入り店舗 -->
        <div class="right-half ms-4 col-12 col-md-6">
            <div class="user-disp mb-4">
                <h2 class="fs-4 fw-bolder text-primary mb-2">{{ Auth::user()->name }}さん</h2>
                <h3 class="fs-4 fw-bolder ">お気に入り店舗</h3>
            </div>
            <div class="favorite-list row">
                @foreach ($favorites as $favorite)
                <div class="col-12 col-md-6 mb-4">
                    <div class="favorite-card shadow rounded h-100" id="favorite-{{ $favorite->restaurant->id }}">
                        <div class="favorite-image-wrapper">
                            <img src="{{ $favorite->restaurant->image_url }}" alt="{{ $favorite->restaurant->name }}"
                                class="favorite-image">
                        </div>
                        <div class="favorite-details p-3">
                            <h5 class="favorite-title fw-bold mb-2">{{ $favorite->restaurant->name }}</h5>
                            <div class="favorite-tags mb-3">
                                <span class="tag">#{{ $favorite->restaurant->area->name }}</span>
                                <span class="tag">#{{ $favorite->restaurant->genre->name }}</span>
                            </div>
                            <div class="favorite-actions d-flex justify-content-between align-items-center">
                                <form method="GET" action="{{ route('restaurant.detail', $favorite->restaurant->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm">詳しく見る</button>
                                </form>
                                <span class="heart favorited" data-id="{{ $favorite->restaurant->id }}"
                                    title="お気に入りから削除"></span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- 予約変更モーダル -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content edit-modal">
                <form id="editReservationForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header edit-modal-header">
                        <h5 class="modal-title text-white fw-bold" id="editModalLabel">予約変更</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="閉じる">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body edit-modal-body">
                        <input type="hidden" name="restaurant_id" id="restaurant_id">

                        <div class="form-group mb-3">
                            <label for="edit-date" class="form-label fw-bold">日付</label>
                            <input type="date" id="edit-date" name="date" class="form-control edit-input" required
                                value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="form-group mb-3">
                            <label for="edit-time" class="form-label fw-bold">時間</label>
                            <select id="edit-time" name="time" class="form-control edit-input" required>
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
                        <div class="form-group mb-3">
                            <label for="edit-number" class="form-label fw-bold">人数</label>
                            <select id="edit-number" name="number_of_people" class="form-control edit-input" required>
                                @for ($i = 1; $i <= 10; $i++) <option value="{{ $i }}">{{ $i }}人</option>
                                    @endfor
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer edit-modal-footer">
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