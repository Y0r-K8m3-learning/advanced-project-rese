@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

<x-app-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="container">
        <!-- ページ左半分: 予約状況 -->
        <div class="left-half">
            <h2>予約状況</h2>
            <div class="reservation-list">
                @foreach ($reservations as $reservation)
                <div class="reservation-item" id="reservation-{{ $reservation->id }}">
                    <span class="remove-button" data-id="{{ $reservation->id }}">×</span>
                    <p><strong>Shop:</strong> {{ $reservation->restaurants->name }}</p>
                    <p><strong>Date:</strong> {{ $reservation->reservation_date }}</p>
                    <p><strong>Time:</strong> {{ $reservation->formatted_time }}</p>
                    <p><strong>Number:</strong> {{ $reservation->number_of_people }}人</p>

                    <!-- 予約編集ボタン -->
                    <button type="button" class="btn btn-secondary edit-button" data-id="{{ $reservation->id }}" data-date="{{ $reservation->reservation_date }}" data-time="{{ $reservation->formatted_time }}" data-number="{{ $reservation->number_of_people }}">編集</button>
                </div>
                @endforeach
            </div>
        </div>

        <!-- ページ右半分: お気に入り店舗 -->
        <div class="right-half">
            <div class="username">{{Auth::user()->name}}さん</div>
            <h2>お気に入り店舗</h2>
            <div class="favorite-list">
                @foreach ($favorites as $favorite)
                <div class="card" id="favorite-{{ $favorite->restaurant->id }}">
                    <img src="{{ $favorite->restaurant->image_url }}" alt="{{ $favorite->restaurant->name }}">
                    <div class="details">
                        <h3>{{ $favorite->restaurant->name }}</h3>
                        <p class="card-hash">#{{ $favorite->restaurant->area->name }}</p>
                        <p class="card-hash">#{{ $favorite->restaurant->genre->name }}</p>
                        <p>{{ $favorite->restaurant->description }}</p>
                    </div>
                    <form method="GET" action="{{ route('restaurant.detail', $favorite->restaurant->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary">詳しく見る</button>
                        <span class="heart favorited" data-id="{{ $favorite->restaurant->id }} "></span>
                    </form>
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
                            <input type="date" id="edit-date" name="date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-time">時間</label>
                            <select id="edit-time" name="time" class="form-control" required></select>
                        </div>
                        <div class="form-group">
                            <label for="edit-number">人数</label>
                            <select id="edit-number" name="number_of_people" class="form-control" required>
                                @for ($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}">{{ $i }}人</option>
                                    @endfor
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                        <button type="submit" class="btn btn-primary">変更を保存</button>
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

            // 時間の選択肢をクリアして再生成
            var timeSelect = $('#editModal #edit-time');
            timeSelect.empty();
            for (var i = 0; i < 24 * 2; i++) {
                var hours = Math.floor(i / 2).toString().padStart(2, '0');
                var minutes = (i % 2 === 0) ? '00' : '30';
                var optionValue = hours + ':' + minutes;
                var option = $('<option>').val(optionValue).text(optionValue);
                timeSelect.append(option);
            }
            timeSelect.val(reservationTime); // ここで時刻をセット

            $('#editModal').modal('show');
        });

        // モーダルの閉じる機能
        $('#editModal').on('hidden.bs.modal', function() {
            $(this).find('form').trigger('reset');
        });

        // モーダルのクローズボタンが動作しない問題を解決
        $('.close, .btn-secondary').click(function() {
            $('#editModal').modal('hide');
        });

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
    });
</script>