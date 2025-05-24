@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

<x-app-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="container">
        <div class="left-half p-3" style="flex: 1;">
            <h5> <a href="{{ route('index') }}" class="back-link">＜</a>{{ $restaurant['name'] }}</h5>
            <img src="{{ $restaurant['image_url'] }}" class="img-fluid" alt="{{ $restaurant['title'] }}">
            <p class="card-hash">#{{ $restaurant['area']['name'] }}</p>
            <p class="card-hash">#{{ $restaurant['genre']['name'] }}</p>
            <p>{{ $restaurant['description'] }}</p>
            <p class="card-text">{{ $restaurant['description'] }}</p>

            <!-- 5段階評価ボタン -->
            <button type="button" class="btn btn-secondary mt-3" id="rateButton">評価</button>
        </div>

        <div class="right-half p-3" style="flex: 1; background-color: #f8f9fa;">
            <!-- エラーメッセージの表示 -->
            @if ($errors->has('error'))
            <div class="alert alert-danger">
                {{ $errors->first('error') }}
            </div>
            @endif
            <h3>予約</h3>
            <form method="POST" action="{{ route('reservation.store') }}">
                @csrf
                <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
                <div class="form-group">
                    <input type="date" id="date" name="date" class="form-control" required value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}">
                </div>
                <div class="form-group">
                    <select id="time" name="time" class="form-control" required>
                        @php
                        $currentTime = date('H:i');
                        $currentMinutes = date('i');
                        // 30分単位の丸め
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
                </div>
                <div class="form-group">
                    <select id="number" name="number" class="form-control" required>
                        @for ($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}">{{ $i }}人</option>
                            @endfor
                    </select>
                </div>
                <div class="reserve-content">
                    <div class="reserve-content-item">
                        Shop: <span id="shop-name">{{$restaurant['name']}}</span>
                    </div>
                    <div class="reserve-content-item">
                        Date: <span id="selected-date">{{ date('Y-m-d') }}</span>
                    </div>
                    <div class="reserve-content-item">
                        Time: <span id="selected-time">{{ $selectedTime }}</span>
                    </div>
                    <div class="reserve-content-item">
                        Number: <span id="selected-number">1人</span>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">予約する</button>
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

<script>
    $(document).ready(function() {
        // 初期化
        updateTimes();

        // 日付が変更されたとき
        $('#date').change(function() {
            $('#selected-date').text($(this).val());
            updateTimes();
        });

        // 時間が変更されたとき
        $('#time').change(function() {
            $('#selected-time').text($(this).val());
        });

        // 人数が変更されたとき
        $('#number').change(function() {
            $('#selected-number').text($(this).val() + '人');
        });

        // 現在の日付と現在時刻以降の時間を選択肢にする関数
        function updateTimes() {
            var timeSelect = $('#time');
            timeSelect.empty(); // 既存の選択肢をクリア

            var now = new Date();
            var currentDate = now.toISOString().split('T')[0]; // 今日の日付を取得
            var selectedDate = $('#date').val();
            var currentTime = now.getHours() * 60 + now.getMinutes(); // 現在の時間を分単位で取得

            if (selectedDate < currentDate) {
                // 選択日が過去の場合、すべての時間を無効にする
                var option = $('<option>').text('選択不可').prop('disabled', true);
                timeSelect.append(option);
            } else {
                // 選択日が現在日以降の場合のみ、時間の選択肢を生成
                for (var i = 0; i < 24 * 2; i++) {
                    var hours = Math.floor(i / 2).toString().padStart(2, '0');
                    var minutes = (i % 2 === 0) ? '00' : '30';
                    var optionValue = hours + ':' + minutes;
                    var optionTime = parseInt(hours) * 60 + parseInt(minutes);

                    var option = $('<option>').val(optionValue).text(optionValue);

                    // 選択日が今日の場合、現在時刻以降のみ選択可能にする
                    if (selectedDate === currentDate && optionTime < currentTime) {
                        option.prop('disabled', true);
                    }

                    timeSelect.append(option);
                }

                // 初期表示に選択された時間を設定
                var initialTime = "{{ $selectedTime }}";
                timeSelect.val(initialTime);
                $('#selected-time').text(initialTime);
            }
        }

        // 評価モーダルのトリガー
        $('#rateButton').click(function() {
            $('#rateModal').modal('show');
        });

        // モーダルの閉じる機能
        $('#rateModal').on('hidden.bs.modal', function() {
            $(this).find('form').trigger('reset');
        });

        $('.close, .btn-secondary').click(function() {
            $('#rateModal').modal('hide');
        });
    });
</script>