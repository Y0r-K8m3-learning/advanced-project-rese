<x-app-layout>
    <div class="container">
        <h1>予約情報</h1>
      

        <div class="qr-code">
            {!! $qrCode !!}
        </div>

        <!-- 元の画面に戻るボタン -->
        <a href="{{ route('mypage.index') }}" class="btn btn-secondary mt-3">戻る</a>
    </div>
</x-app-layout>