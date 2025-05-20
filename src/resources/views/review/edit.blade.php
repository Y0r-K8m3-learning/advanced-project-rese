@section('css')
<link rel="stylesheet" href="{{ asset('css/review.css') }}">
@endsection

@section('js')
<script src="{{ asset('js/review.js') }}"></script>
@endsection

<x-app-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="d-flex flex-row">
        <div class="row w-100">
            <!--  レストラン情報 -->
            <div class="col-md-6 d-flex flex-column align-items-center">
              
                <x-restaurant-info :restaurant="$restaurant" :favoriteRestaurantIds="$favoriteRestaurantIds ?? []" />
            </div>

            <!-- 口コミフォーム -->
            <div class="col-md-6 bg-gray-100 d-flex flex-column justify-content-center">
                <div class="p-4">
                    <form id="rateForm" method="POST" action="{{ route('review.update') }}"
                        enctype="multipart/form-data">
                        @method('PATCH')
                        @csrf
                        <x-review-input :restaurant="$restaurant" :user_review="$user_review" user_action="update" />
                        <button type="submit" class="btn m-auto btn-primary rounded w-50">口コミを更新する</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>