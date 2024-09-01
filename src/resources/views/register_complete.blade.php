@section('css')
<link rel="stylesheet" href="{{ asset('css/complete.css') }}">
@endsection
<x-app-layout>
    <div class="container">
        <div class="message">
            会員登録ありがとうございます
        </div>
        <div>

            <a href="{{ route('login') }}" class="back-button">ログイン</a>
        </div>
</x-app-layout>