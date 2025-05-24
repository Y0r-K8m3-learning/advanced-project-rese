<x-app-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <div class="mt-5">
        {{-- 完了メッセージ表示 --}}
        @if(session('complete'))
        <div class="alert alert-success">
            {{ session('complete') }}
        </div>
        @endif
        
        <a href="{{ route('restaurant.detail', ['id' => $restaurant_id]) }}" class="btn btn-primary">店舗詳細へ戻る</a>
    </div>
</x-app-layout>