<input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">

<div class="mb-3">
    <p class="fs-5" id="rateModalLabel">体験を評価してください</p>
</div>
<div class="form-group mb-3">
    <p>データ{{$user_action}}</p>
    <div class="rate-form">
        @foreach(range(1,5) as $i)
        <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" class="star" @checked(old('rating',
            $review->rating ?? '') == $i)
        />
        <label for="star{{ $i }}" class="star">★</label>
        @endforeach
    </div>
    @error('rating')
    <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>
<div class="form-group mb-3">
    <label for="comment" class="fs-3 fw-bold"><span>口コミを投稿</span></label>
    <textarea id="comment" name="comment" class="form-control" placeholder="カジュアルな夜のお出かけにおすすめのスポット" rows="3"
        maxlength="400" required>{{ old('comment', $review->comment ?? '') }}</textarea>
    @error('comment')
    <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    <div>{{ mb_strlen(old('comment', $review->comment ?? '')) }}/400[最高文字数]</div>
</div>
<label for="image" class="fs-3 fw-bold"><span>画像を追加</span></label>
<div id="drop-area" class="upload-area">
    <span id="drop-message">
        <p>クリックして画像を追加またはドラッグアンドドロップ</p>
    </span>
    <input type="file" id="file-input" name="image" accept="image/*" style="display:none;">
    <img id="preview" src="" style="display:none; max-width: 100%; max-height: 180px; margin-top: 8px;" />
</div>
@error('image')
<div class="invalid-feedback d-block">{{ $message }}</div>
@enderror