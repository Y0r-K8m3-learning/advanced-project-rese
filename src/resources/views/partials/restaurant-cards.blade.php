@foreach($restaurants as $restaurant)
<div class="col-md-4 col-lg-3 col-6 mb-4">
    <div class="card h-100 w-100 mt-0">
        @if($restaurant['image_url'])
            <div class="image-container">
                <img src="{{ $restaurant['image_url'] }}" class="card-img-top" alt="{{ $restaurant['name'] }}" 
                     onerror="this.parentElement.innerHTML='<div class=&quot;card-img-top d-flex align-items-center justify-content-center bg-light text-muted no-image&quot;><div class=&quot;text-center&quot;><i class=&quot;material-symbols-outlined&quot; style=&quot;font-size: 48px;&quot;>image</i><div>画像がありません</div></div></div>';">
            </div>
        @else
            <div class="card-img-top d-flex align-items-center justify-content-center bg-light text-muted no-image">
                <div class="text-center">
                    <i class="material-symbols-outlined" style="font-size: 48px;">image</i>
                    <div>画像がありません</div>
                </div>
            </div>
        @endif
        <div class="card-body">
            <h5 class="card-title fw-bold">{{ $restaurant['name'] }}<sup style="font-size: smaller;"><span
                        class="text-warning">★</span>
                    @if($restaurant['reviews_avg_rating'])
                    <span class="fs-6">{{ $restaurant['reviews_avg_rating'] }}</span>
                    @else
                    <sup>投稿なし</sup>
                    @endif
                </sup>
            </h5>
            <p hidden class="card-text">{{ $restaurant['description'] }} </p>

            <div class="row fs-10 fw-bold ">
                <div class="col-3 col-md-4 w-50">
                    #{{ $restaurant['area']['name'] }}
                </div>
                <div class="col-3 col-md-4 w-50">
                    #{{ $restaurant['genre']['name'] }}
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <form method="GET" action="{{ route('restaurant.detail', $restaurant->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">詳しく見る</button>
                </form>

                @if (Auth::check())
                <span
                    class="heart {{ in_array($restaurant->id, $favoriteRestaurantIds) ? 'favorited' : '' }}"
                    data-id="{{ $restaurant->id }}"></span>
                @else
                <span class="heart" data-id="{{ $restaurant->id }} "></span>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach