<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Restaurant;

class RestaurantInfo extends Component
{
    public $restaurant;
    public $favoriteRestaurantIds;
    /**
     * Create a new component instance.
     */
    public function __construct(Restaurant $restaurant, $favoriteRestaurantIds=[])
    {
        $this->restaurant = $restaurant;
        $this->favoriteRestaurantIds = $favoriteRestaurantIds;
    }
   

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.restaurant-info', [
            'restaurant' => $this->restaurant,
            'favoriteRestaurantIds' => $this->favoriteRestaurantIds,
        ]);
    }
}
