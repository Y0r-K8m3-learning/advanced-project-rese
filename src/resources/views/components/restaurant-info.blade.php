<div class="ms-4 mt-4 col-md-10 col-lg-9 col-12 mb-4 rounded shadow" style="max-width: 350px;">
    <img src="{{ $restaurant['image_url'] }}" class="card-img-top" alt="{{ $restaurant['name'] }}">
    <div class="card-body">
        <h5 class="card-title fw-bold">{{ $restaurant['name'] }}</h5>
        <p hidden class="card-text">{{ $restaurant['description'] }}</p>
        <div class="row fs-10 fw-bold">
            <div class="col-6">
                #{{ $restaurant['area']['name'] }}
            </div>
            <div class="col-6">
                #{{ $restaurant['genre']['name'] }}
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <a href="{{ route('restaurant.detail', $restaurant->id) }}" class="btn btn-primary">詳しく見る</a>
            @if (Auth::check())
            <span class="heart {{ in_array($restaurant->id, $favoriteRestaurantIds ?? []) ? 'favorited' : '' }}"
                data-id="{{ $restaurant->id }}"></span>
            @else
            <span class="heart" data-id="{{ $restaurant->id }}"></span>
            @endif
        </div>
    </div>
</div>