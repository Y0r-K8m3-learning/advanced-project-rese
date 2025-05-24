@section('css')
<link rel="stylesheet" href="{{ asset('css/review.css') }}">
@endsection
@section('js')
<script src="{{ asset('js/review.js') }}"></script>
@endsection

<x-app-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <div class="mt-3">

        <div class="d-flex flex-column flex-md-row align-items-center">
            <div class="p-3 flex-fill">
                <!--  レストラン情報 -->
                <div class="mt-4">
                    <p class="fs-2 fw-bold">今回のご利用はいかがでしかたか？</p>
                </div>
                <div class=" d-flex flex-column align-items-center">
                    <x-restaurant-info :restaurant="$restaurant"
                        :favoriteRestaurantIds="$favoriteRestaurantIds ?? []" />
                </div>
            </div>

            <div class="d-block d-md-none w-100 border-top my-2"></div>

            <div class="p-3 flex-fill ">
                <!-- 口コミフォーム -->
                <div class="border-md-start ps-2">
                    <style>
                        @media (min-width: 768px) {
                            .border-md-start {
                                border-left: 1px solid #dee2e6;
                            }
                        }
                    </style>
                    <div class="p-4">
                        <form id="rateForm" method="POST" action="{{ route('review.store') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <x-review-input :restaurant="$restaurant" user_action="create" />

                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mb-5">
            {{-- 投稿済みエラー --}}
            @if (session('error'))
            <div class="alert-danger alert">
                {{ session('error') }}
            </div>
            @endif
            <button type="submit"
                class="btn rounded w-50 rounded-pill  shadow fs-4 fw-3 bg-white border-0 text-black">口コミを投稿</button>
            </form>
        </div>
    </div>
    </div>
</x-app-layout>