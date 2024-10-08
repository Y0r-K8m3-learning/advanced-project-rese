@section('css')
<link rel="stylesheet" href="{{ asset('css/complete.css') }}">
@endsection
<x-app-layout>
    <div class="p-20 center-block">
        <div class="container h-100 d-flex justify-content-center align-items-center">
            <div class="text-center mt-3  fw-bold shadow p-5">
                ご予約ありがとうございます
                <div class="mt-3">
                    <a href="{{ route('restaurants.index') }}" class="back-button">戻る</a>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>