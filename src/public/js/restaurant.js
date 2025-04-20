    $(document).ready(function() {
        $('.heart').click(function() {
            var heart = $(this);
            var restaurantId = heart.data('id');
            if (!heart.hasClass('favorited')) {
                // お気に入り追加
                $.ajax({
                    url: '/restaurants/' + restaurantId + '/favorite',
                    type: 'POST',
                     headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        _token: window.csrfToken,
                    },
                    success: function(response) {
                        heart.addClass('favorited');
                    },
                    error: function(xhr) {
                        if (xhr.status === 401) {
                            // 未ログインならログイン画面にリダイレクト
                            window.location.href = '/login';
                        } else {
                            console.error(xhr.responseText);
                        }
                    }
                });
            } else {
                // お気に入り解除
                $.ajax({
                    url: '/restaurants/' + restaurantId + '/unfavorite',
                    type: 'POST',
                     headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        _token: window.csrfToken,
                    },
                    success: function(response) {
                        heart.removeClass('favorited');
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            }
        });

        //inputイベント制御用
        let typingTimer;
        const doneTypingInterval = 700;

        $('#name').on('input', function() {
            clearTimeout(typingTimer);

            typingTimer = setTimeout(function() {
                Search();
            }, doneTypingInterval);
        });

        $('#area, #genre').on('change', function() {
            $('#searchFrom').submit();

        });



        function Search() {
            formData = {
                area: $('#area').val(),
                genre: $('#genre').val(),
                name: $('#name').val(),
            }

            $.ajax({
                url: window.searchUrl,
                type: 'GET',
                data: formData,
                cache: true,
                success: function(data) {
                    $('body').html(data);
                    $('#name').off('input');
                    $('#area, #genre').off('change');

                    $('#name').focus();
                    var tmpStr = $('#name').val();
                    $('#name').val('');
                    $('#name').val(tmpStr);

                },
                error: function() {
                    alert('検索に失敗しました。');
                }
            });
        }


    });