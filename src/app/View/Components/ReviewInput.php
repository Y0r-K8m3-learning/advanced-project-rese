<?php

namespace App\View\Components;

use App\Http\Requests\ReviewRequest;
use App\Models\Restaurant;
use App\Models\Review;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ReviewInput extends Component
{
    public $user_review;
    public $restaurant;
    public $user_action;
    public $maxLength;
    /**
     * Create a new component instance.
     */
    public function __construct(Restaurant $restaurant,?Review $userReview=null,  string  $userAction, int $maxLength = ReviewRequest::MAX_REVIEW_LENGTH)   {

        $this->user_review = $userReview;
        $this->restaurant = $restaurant;
        $this->user_action = $userAction;
        $this->maxLength = $maxLength;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.review-input', [
            'user_review' => $this->user_review,
            'restaurant' => $this->restaurant,
            'user_action' => $this->user_action,
            'maxLength' => $this->maxLength,
        ]);
    }
}
