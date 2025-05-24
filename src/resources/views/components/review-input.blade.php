<input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
<input type="hidden" name="review_id" value="{{ $user_review->id }}">

<div class="mb-3">
    <p class="fs-5" id="rateModalLabel">体験を評価してください</p>
</div>
<div class="form-group mb-3">
    @php
    $disabled = $user_action == "delete" ? 'disabled' : '';
    @endphp
    <div class="rate-form">
        @foreach(range(1,5) as $starindex)

        <input {{ $disabled }} type="radio" id="star{{ $starindex }}" name="rating" value="{{ $starindex }}"
            class="star-input" @checked(old('rating', $user_review->rating ?? null) == $starindex) />
        <label for="star{{ $starindex }}" class="star-label">★</label>
        @endforeach
    </div>
    @error('rating')
    <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
<div class="form-group mb-3">
    <label for="comment" class="fs-3 fw-bold"><span>口コミを投稿</span></label>

    <textarea {{$disabled}} id="comment" name="comment" class="form-control" placeholder="カジュアルな夜のお出かけにおすすめのスポット"
        style="max-width: 100%; max-height: 100%; margin-top: 8px;" rows="3" maxlength="400"
        required>{{ old('comment', $user_review->comment ?? '') }}</textarea>

    <div class="text-right fs-6"><sub>
            <span id="charCount">0</span>/{{$maxLength}}[最高文字数]</sub></div>
    @error('comment')
    <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
<label for="image" class="fs-3 fw-bold"><span>画像を追加</span></label>

<div id="drop-area" class="upload-area bg-white">
    <span id="drop-message">
        <p class="text-black">クリックして画像を追加
            <br><sub> またはドラッグアンドドロップ</sub>
        </p>
    </span>

    <input {{ $disabled }} type="file" id="file-input" name="image" accept="image/*" style="display:none;">

    @php
    $imgPath = old('image', $user_review->image->file_path ?? '');
    @endphp

    <img id="preview" src="{{ $imgPath ? asset($imgPath) : '' }}" alt="画像の取得に失敗しました"
        style="max-height: 100%; margin-top: 8px;{{ $imgPath ? '' : 'display:none;' }}" />
    <div id="drop-message" {{ $imgPath ? 'style=display:none;' : '' }}>
    </div>

</div>
@error('image')
<div class="invalid-feedback d-block">{{ $message }}</div>
@enderror