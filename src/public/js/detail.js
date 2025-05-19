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
        var selectedTime = timeSelect.val(); // 現在選択されている時間を保存
        timeSelect.empty(); // 既存の選択肢をクリア

        var now = new Date();
        var currentDate = now.toISOString().split('T')[0]; // 今日の日付を取得
        var selectedDate = $('#date').val();
        var currentTimeMinutes = now.getHours() * 60 + now.getMinutes(); // 現在時刻を分単位で取得

        // 現在時刻を次の30分単位の丸めた時間に変更
        if (now.getMinutes() > 30) {
            now.setHours(now.getHours() + 1); // 1時間追加
            now.setMinutes(0); // 分を0に設定
        } else {
            now.setMinutes(30); // 30分に設定
        }

        var roundedTimeMinutes = now.getHours() * 60 + now.getMinutes();

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
                if (selectedDate === currentDate && optionTime < roundedTimeMinutes) {
                    option.prop('disabled', true);
                }

                timeSelect.append(option);
            }

            // ユーザーが以前に選択した時間を再選択
            if (selectedTime && timeSelect.find('option[value="' + selectedTime + '"]').length) {
                timeSelect.val(selectedTime);
                $('#selected-time').text(selectedTime);
            } else {
                // 初期表示に選択された時間を設定
                var initialTime = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
                timeSelect.val(initialTime);
                $('#selected-time').text(initialTime);
            }
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