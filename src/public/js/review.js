$(document).ready(function () {

    //文字数カウント
    $("#comment").on("input", function () {
        $("#charCount").text($(this).val().length);
    });

    //画像アップロード＆プレビュー
    const dropArea = document.getElementById("drop-area");
    const fileInput = document.getElementById("file-input");
    const preview = document.getElementById("preview");
    const message = document.getElementById("drop-message");

    // エリアクリックでファイル選択
   dropArea.addEventListener("click", () => fileInput.click());

    // ドラッグ＆ドロップ
    dropArea.addEventListener("dragover", (e) => {
        e.preventDefault();
        dropArea.classList.add("dragover");
    });
    dropArea.addEventListener("dragleave", (e) => {
        e.preventDefault();
        dropArea.classList.remove("dragover");
    });
    dropArea.addEventListener("drop", (e) => {
        e.preventDefault();
        dropArea.classList.remove("dragover");
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith("image/")) {
            showPreview(file);
            fileInput.files = e.dataTransfer.files; // inputにセット
        }
    });

    // ファイル選択時のプレビュー
    fileInput.addEventListener("change", function () {
        if (this.files && this.files[0]) {
            showPreview(this.files[0]);
        }
    });

    // プレビュー関数
    function showPreview(file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = "block";
            message.style.display = "none";
        };
        reader.readAsDataURL(file);
    }
});
