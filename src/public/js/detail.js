$(document).ready(function () {
    // 時間が変更されたとき
    $("#time").change(function () {
        $("#selected-time").text($(this).val());
    });

    // 人数が変更されたとき
    $("#number").change(function () {
        $("#selected-number").text($(this).val() + "人");
    });

    // 現在の日付と現在時刻以降の時間を選択肢にする関数
    function updateTimes() {
        var timeSelect = $("#time");
        var selectedTime = timeSelect.val(); // 現在選択されている時間を保存
        timeSelect.empty(); // 既存の選択肢をクリア

        var now = new Date();
        var currentDate = now.toISOString().split("T")[0]; // 今日の日付を取得
        var selectedDate = $("#date").val();
        var currentTimeMinutes = now.getHours() * 60 + now.getMinutes(); // 現在時刻を分単位で取得

        // 現在時刻を次の30分単位の丸めた時間に変更
        if (now.getMinutes() > 30) {
            now.setHours(now.getHours() + 1); // 1時間追加
            now.setMinutes(0); // 分を0に設定
        } else {
            now.setMinutes(30); // 30分に設定
        }

        var roundedTimeMinutes = now.getHours() * 60 + now.getMinutes();

        if (selectedDate < currentDate) {
            // 選択日が過去の場合、すべての時間を無効にする
            var option = $("<option>").text("選択不可").prop("disabled", true);
            timeSelect.append(option);
        } else {
            // 選択日が現在日以降の場合のみ、時間の選択肢を生成
            for (var i = 0; i < 24 * 2; i++) {
                var hours = Math.floor(i / 2)
                    .toString()
                    .padStart(2, "0");
                var minutes = i % 2 === 0 ? "00" : "30";
                var optionValue = hours + ":" + minutes;
                var optionTime = parseInt(hours) * 60 + parseInt(minutes);

                var option = $("<option>").val(optionValue).text(optionValue);

                // 選択日が今日の場合、現在時刻以降のみ選択可能にする
                if (
                    selectedDate === currentDate &&
                    optionTime < roundedTimeMinutes
                ) {
                    option.prop("disabled", true);
                }

                timeSelect.append(option);
            }

            // ユーザーが以前に選択した時間を再選択
            if (
                selectedTime &&
                timeSelect.find('option[value="' + selectedTime + '"]').length
            ) {
                timeSelect.val(selectedTime);
                $("#selected-time").text(selectedTime);
            } else {
                // 初期表示に選択された時間を設定
                var initialTime =
                    now.getHours().toString().padStart(2, "0") +
                    ":" +
                    now.getMinutes().toString().padStart(2, "0");
                timeSelect.val(initialTime);
                $("#selected-time").text(initialTime);
            }
        }
    }

    //口コミ一覧表示
    $("#rate-list").click(function () {
        let restaurantId = $(this).data("restaurant-id");
        $.ajax({
            url: "/restaurants/review/show/" + restaurantId,
            type: "GET",
            success: function (reviews) {
                // レビューがない場合
                if (!reviews || reviews.length === 0) {
                    $("#reviews").show();
                    $("#reviews").html(
                        '<div class="alert alert-info">口コミはまだありません</div>'
                    );
                    return;
                }
                let $list = $("<div></div>");
                reviews.forEach(function (review, idx) {
                    let $item = $('<div class="review-item mt-3"></div>');
                    let $header = $(
                        '<div class="review-header d-flex justify-content-between"></div>'
                    );

                    let $starRow = $('<div class="rate-form flex"></div>');

                    for (let star = 1; star <= 5; star++) {
                        let paint_value =
                            review.rating >= star
                                ? 1
                                : review.rating - (star - 1);
                        paint_value = Math.max(0, Math.min(1, paint_value));
                        let percent = paint_value * 100;

                        //星マーク
                        let svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" style="width:24x; height:24px; opacity:1;">
                            <defs>
                                <linearGradient id="star-gradient-${idx}-${star}" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="${percent}%" stop-color=#006aff />
                                    <stop offset="${percent}%" stop-color="#ccc" />
                                </linearGradient>
                            </defs>
                            <path fill="url(#star-gradient-${idx}-${star})" d="M510.698,196.593c-3.61-11.2-14.034-18.795-25.794-18.795H329.21L281.791,30.155 c-3.599-11.2-14.018-18.808-25.791-18.808c-11.772,0-22.192,7.608-25.791,18.808l-47.418,147.642H27.097 c-11.761,0-22.185,7.594-25.795,18.795c-3.599,11.2,0.436,23.449,9.999,30.302l126.246,90.643l-48.598,147.54 c-3.694,11.193,0.278,23.47,9.801,30.398c9.529,6.926,22.44,6.897,31.94-0.058L256,403.594l125.312,91.824 c9.5,6.956,22.411,6.985,31.941,0.058c9.522-6.927,13.494-19.205,9.811-30.398l-48.61-147.54L500.7,226.895 C510.262,220.042,514.298,207.792,510.698,196.593z" />
                        </svg>`;
                        $starRow.append(svg);
                        $header.append($starRow);
                    }
                    //ユーザIDとログインインユーザIDが一致する場合、編集、削除ボタンを表示

                    const userId = $("#data").data("user-id");
                    const isAdminUser = $("#data").data("isadminuser-id");
                    const isGeneralUser = $("#data").data("isgeneraluser-id");
                    const restaurantId = $("#data").data("restaurant-id");

                    let $doButton = $(
                        '<div class="review-header d-flex justify-content-between"></div>'
                    );
                    // 編集ボタン
                    let $editButton = $("<a></a>")
                        .attr(
                            "href",
                            "/restaurants/review/edit/" +
                                restaurantId +
                                "/" +
                                review.id
                        )
                        .append(
                            $("<span></span>")
                                .addClass(
                                    "border-bottom border-3 text-black-100 border-gray me-2"
                                )
                                .text("口コミを編集")
                        );

                    // 削除ボタン
                    let $deleteButton = $("<a></a>")
                        .attr(
                            "href",
                            "/restaurants/review/delete/" +
                                restaurantId +
                                "/" +
                                review.id
                        )
                        .append(
                            $("<span></span>")
                                .addClass(
                                    "border-bottom border-3 text-black-100 border-gray"
                                )
                                .text("口コミを削除")
                        );

                    if (review.user_id === userId && isGeneralUser) {
                        //ログインユーザのレビュー
                        $doButton.append($editButton);
                        $doButton.append($deleteButton);
                    }
                    if (isAdminUser) {
                        //管理者のレビュー
                        $doButton.append($deleteButton);
                    }
                    $header.append($doButton);

                    $item.append($header);

                    // コメント
                    $item.append(
                        $(
                            '<p class="review-comment p-2 fs-4" style="white-space: pre-line;"></p>'
                        ).text(review.comment)
                    );

                    // 画像
                    if (review.image && review.image.file_path) {
                        let $img = $(
                            '<img class="review-image" alt="画像が取得できません">'
                        );
                        $img.attr("src", "/" + review.image.file_path);
                        $item.append($img);
                    }

                    $item.append($("<hr class='mt-3 mb-3'>"));

                    $list.append($item);
                });

                // レビューを表示
                $("#reviews").empty().append($list.html());
                $("#reviews").show();

                $("html, body").animate(
                    {
                        scrollTop: $("#reviews").offset().top,
                    },
                    500
                );
            },
            error: function (xhr) {
                let errorMessage = "";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                let $errcontent = $(
                    '<div class="alert alert-danger">' +
                        "口コミの取得に失敗しました" +
                        "</div>"
                );
                $("#reviews").empty().append($errcontent.html());
                $("#reviews").show();
                console.error(errorMessage + "error:" + xhr.responseText);
            },
        });
    });

    //スター表示
    $(".rate-form").each(function () {
        const ave_rating = $("#rating-value").val();

        $(this).find('input[type="radio"]:checked ~ label').css("color", "");
    });

    //del:モーダル表示
    $("#rateButton").click(function () {
        $("#rateModal").modal("show");
    });

    // モーダルの閉じる機能
    $("#rateModal").on("hidden.bs.modal", function () {
        $(this).find("form").trigger("reset");
    });

    $(".close, .btn-secondary").click(function () {
        $("#rateModal").modal("hide");
    });
});
