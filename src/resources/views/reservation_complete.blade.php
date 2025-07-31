@section('css')
<link rel="stylesheet" href="{{ asset('css/complete.css') }}">
@endsection
<x-app-layout>
    <div class="p-20 center-block">
        <div class="container h-100 d-flex justify-content-center align-items-center">
            <div class="text-center mt-3 fw-bold shadow p-5">
                @if(session('error'))
                <!-- エラーメッセージの場合 -->
                <div class="alert alert-danger mb-3">
                    <i class="text-danger me-2"></i>
                    {{ session('error') }}
                </div>
                <div class="text-danger fs-4">
                    決済処理でエラーが発生しました
                </div>

                @else
                <div class="text-success fs-4">
                    {{ session('success') }}
                    <br>ご予約ありがとうございます
                </div>
                @endif

                <div class="mt-4">
                    <a href="{{ route('restaurants.index') }}" class="back-button btn btn-primary px-4 py-2">
                        レストラン一覧に戻る
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>