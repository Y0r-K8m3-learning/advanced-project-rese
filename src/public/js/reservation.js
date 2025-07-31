$(document).ready(function() {
    $('.heart').click(function() {
        var heart = $(this);
        var restaurantId = heart.data('id');
        if (!heart.hasClass('favorited')) {
            $.ajax({
                url: '/restaurants/' + restaurantId + '/favorite',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                },
                success: function(response) {
                    heart.addClass('favorited');
                },
                error: function(xhr) {
                    if (xhr.status === 401) {
                        window.location.href = '/login';
                    }
                }
            });
        } else {
            $.ajax({
                url: '/restaurants/' + restaurantId + '/unfavorite',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                },
                success: function(response) {
                    heart.removeClass('favorited');
                },
                error: function(xhr) {
                }
            });
        }
    });
});