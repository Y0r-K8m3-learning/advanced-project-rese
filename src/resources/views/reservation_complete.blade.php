@section('css')
<link rel="stylesheet" href="{{ asset('css/complete.css') }}">
@endsection
<x-app-layout>
    <div class="container">
        <div class="message">
            ご予約ありがとうございます
        </div>
        <div>
            <a href="{{ route('restaurants.index') }}" class="back-button">戻る</a>
        </div>
    </div>
</x-app-layout>