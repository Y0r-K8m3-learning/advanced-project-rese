<x-app-layout>
    <div class="container">
        <h1>予約情報</h1>
        <!-- <p><strong>店名:</strong> {{ $reservation->restaurant->name }}</p>
        <p><strong>日付:</strong> {{ $reservation->reservation_date }}</p>
        <p><strong>時間:</strong> {{ $reservation->reservation_time }}</p>
        <p><strong>人数:</strong> {{ $reservation->number_of_people }}人</p> -->

        <div class="qr-code">
            {!! $qrCode !!}
        </div>

        <!-- 元の画面に戻るボタン -->
        <a href="{{ route('mypage.index') }}" class="btn btn-secondary mt-3">戻る</a>
    </div>
</x-app-layout>