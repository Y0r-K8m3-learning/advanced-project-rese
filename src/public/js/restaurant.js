// グローバル変数
let page = 1;
let loading = false;
let lastPage = false;

// ハートボタンのイベントバインド関数
function bindHeartEvents() {
    $(".heart").off("click").on("click", function () {
        var heart = $(this);
        var restaurantId = heart.data("id");
        if (!heart.hasClass("favorited")) {
            // お気に入り追加
            $.ajax({
                url: "/restaurants/" + restaurantId + "/favorite",
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: {
                    _token: window.csrfToken,
                },
                success: function (response) {
                    heart.addClass("favorited");
                },
                error: function (xhr) {
                    if (xhr.status === 401) {
                        window.location.href = "/login";
                    }
                },
            });
        } else {
            // お気に入り解除
            $.ajax({
                url: "/restaurants/" + restaurantId + "/unfavorite",
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: {
                    _token: window.csrfToken,
                },
                success: function (response) {
                    heart.removeClass("favorited");
                },
                error: function (xhr) {
                },
            });
        }
    });
}

function resetPagination() {
    page = 1;
    loading = false;
    lastPage = false;
    $("#end-message").hide();
}

$(document).ready(function () {
    // 初期化時にハートボタンをバインド
    bindHeartEvents();
    
    
    // 初期読み込み時にドキュメントの高さをチェック
    setTimeout(function() {
        let documentHeight = $(document).height();
        let windowHeight = $(window).height();
        
        if (documentHeight <= windowHeight + 50 && !loading && !lastPage) {
            handleScroll(); // 初回読み込みをトリガー
        }
    }, 1000); // 1秒後にチェック
    
    // デバッグボタンのイベントハンドラー
    $("#debug-load-more").on("click", function () {
        $("#debug-info").text("Page: " + page + ", Loading: " + loading + ", LastPage: " + lastPage);
        
        if (loading || lastPage) {
            alert("Cannot load: loading=" + loading + ", lastPage=" + lastPage);
            return;
        }
        
        loading = true;
        page++;
        $("#loading").show();
        
        let currentParams = new URLSearchParams(window.location.search);
        currentParams.set('page', page);
        let requestUrl = window.searchUrl + "?" + currentParams.toString();
        
        $.ajax({
            url: requestUrl,
            type: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
            },
            success: function (data) {
                if (!data || data.trim() === "") {
                    lastPage = true;
                    $("#end-message").show();
                    alert("No more data available");
                } else {
                    $("#restaurant-container").append(data);
                    bindHeartEvents();
                    let addedCount = $(data).filter('.col-md-4').length;
                    alert("Data loaded successfully! Added " + addedCount + " items");
                }
            },
            complete: function () {
                $("#loading").hide();
                loading = false;
            },
            error: function (xhr) {
                if (xhr.status === 204) {
                    lastPage = true;
                    $("#end-message").show();
                    alert("No more data available (204 No Content)");
                } else {
                    alert("Error: " + xhr.status + " - " + xhr.statusText);
                }
            },
            statusCode: {
                204: function() {
                    lastPage = true;
                    $("#end-message").show();
                }
            },
        });
    });
    
    // スクロール情報デバッグボタン
    $("#debug-scroll-info").on("click", function() {
        let scrollTop = $(window).scrollTop();
        let windowHeight = $(window).height();
        let documentHeight = $(document).height();
        let threshold = documentHeight - 100;
        let isDocumentTooShort = documentHeight <= windowHeight + 50;
        
        let info = `
            ScrollTop: ${scrollTop}px
            WindowHeight: ${windowHeight}px
            DocumentHeight: ${documentHeight}px
            Threshold: ${threshold}px
            Should Load: ${(scrollTop + windowHeight) >= threshold}
            Document Too Short: ${isDocumentTooShort}
            Page: ${page}
            Loading: ${loading}
            LastPage: ${lastPage}
        `;
        
        $("#debug-info").html(info.replace(/\n/g, '<br>'));
    });

    //inputイベント制御用
    let typingTimer;
    const doneTypingInterval = 700;

    $("#name").on("input", function () {
        clearTimeout(typingTimer);

        typingTimer = setTimeout(function () {
            Search();
        }, doneTypingInterval);
    });

    $("#area, #genre, #sort").on("change", function () {
        resetPagination();
        $("#searchFrom").submit();
    });

    function Search() {
        formData = {
            area: $("#area").val(),
            genre: $("#genre").val(),
            name: $("#name").val(),
            sort: $("#sort").val(),
        };

        $.ajax({
            url: window.searchUrl,
            type: "GET",
            data: formData,
            cache: true,
            success: function (data) {
                $("body").html(data);
                $("#name").off("input");
                $("#area, #genre,#sort").off("change");
                $("#name").focus();
                var tmpStr = $("#name").val();
                $("#name").val("");
                $("#name").val(tmpStr);
                // 検索後にページネーション状態をリセット
                resetPagination();
            },
            error: function () {
                alert("検索に失敗しました。");
            },
        });
    }
    // 無限スクロール機能
    let scrollCount = 0;
    function handleScroll() {
        scrollCount++;
        
        // 検索中やローディング中、最終ページの場合は処理しない
        if (loading || lastPage) {
            return;
        }

        // スクロール位置を確認
        let scrollTop = $(window).scrollTop();
        let windowHeight = $(window).height();
        let documentHeight = $(document).height();
        let threshold = documentHeight - 100; // 100pxに変更してより早めに読み込み
        let scrolledToBottom = scrollTop + windowHeight >= threshold;
        
        // ドキュメントの高さがウィンドウの高さと同じかほぼ同じ場合、自動的に次のページを読み込む
        let isDocumentTooShort = documentHeight <= windowHeight + 50; // 50pxのマージンを追加

        // スクロールが最下部近くに来たかチェック、または、ドキュメントが短すぎる場合
        if (scrolledToBottom || isDocumentTooShort) {
            loading = true;
            page++;
            $("#loading").show();

            // 現在の検索条件を取得
            let currentParams = new URLSearchParams(window.location.search);
            currentParams.set('page', page);
            
            let requestUrl = window.searchUrl + "?" + currentParams.toString();

            $.ajax({
                url: requestUrl,
                type: "GET",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                },
                success: function (data) {
                    if (!data || data.trim() === "") {
                        lastPage = true;
                        $("#end-message").show();
                    } else {
                        $("#restaurant-container").append(data);
                        // 新しく追加されたハートボタンにイベントを再バインド
                        bindHeartEvents();
                    }
                },
                complete: function () {
                    $("#loading").hide();
                    loading = false;
                },
                error: function (xhr) {
                    if (xhr.status === 204) {
                        // No Content - 最後のページ
                        lastPage = true;
                        $("#end-message").show();
                    } else {
                        lastPage = true;
                        $("#end-message").text("エラーが発生しました").show();
                    }
                },
                statusCode: {
                    204: function() {
                        lastPage = true;
                        $("#end-message").show();
                    }
                },
            });
        }
    }
    
    // スクロールイベントのバインド
    $(window).on("scroll", handleScroll);
    
    // 代替方法も試す
    $(document).on("scroll", handleScroll);
    
    // 定期的にチェックも追加（無限スクロールの保険）
    setInterval(function() {
        if (!loading && !lastPage) {
            let documentHeight = $(document).height();
            let windowHeight = $(window).height();
            let scrollTop = $(window).scrollTop();
            
            if (documentHeight <= windowHeight + 50 || scrollTop + windowHeight >= documentHeight - 100) {
                handleScroll();
            }
        }
    }, 2000); // 2秒ごとにチェック
});
