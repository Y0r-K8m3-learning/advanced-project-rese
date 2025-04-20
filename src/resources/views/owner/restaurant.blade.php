@section('css')
<link rel="stylesheet" href="{{ asset('css/owner.css') }}">

@endsection

<x-app-layout>
    <div class="container ms-4">

        <div class="mt-1">
            <!-- 店舗登録ボタン -->
            <button type=" button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#restaurantCreateModal">
                店舗登録
            </button>
        </div>
        <h2 class="fw-bold fs-3 mt-4">店舗一覧</h2>

        <!-- 店舗一覧 -->
        <div class="row mt-4">
            @foreach($restaurants as $restaurant)
            <div class="col-md-4">
                <div class="card">
                    @php
                    $imagePath = $restaurant->image_url;
                    // ストレージにファイルが存在するか確認
                    if (Storage::exists($imagePath)) {
                    $imageUrl = Storage::url($imagePath);
                    } else {
                    // ストレージにファイルが存在しなければそのままURLをセット
                    $imageUrl = $restaurant->image_url;
                    }
                    @endphp
                    <img src="{{ $imageUrl }}" class="card-img-top h-75" alt="{{ $restaurant->name }}">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">{{ $restaurant['name'] }}</h5>
                        <p hidden class="card-text">{{ $restaurant['description'] }}</p>

                        <div class="row fw-bold">
                            <div class="col-5">
                                #{{ $restaurant['area']['name'] }}
                            </div>
                            <div class="col-5">
                                #{{ $restaurant['genre']['name'] }}
                            </div>

                        </div>
                        <!-- 編集ボタン -->
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#restaurantEditModal" data-id="{{ $restaurant->id }}" data-name="{{ $restaurant->name }}" data-description="{{ $restaurant->description }}" data-area-id="{{ $restaurant->area_id }}" data-genre-id="{{ $restaurant->genre_id }}" data-image-url="{{ $restaurant->image_url }}">
                            編集
                        </button>

                        <!-- 予約一覧ボタン -->
                        <a href="{{ route('reservations', $restaurant->id) }}" class="btn btn-primary">予約一覧</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- 店舗登録モーダル -->
    <div class="modal fade" id="restaurantCreateModal" tabindex="-1" aria-labelledby="restaurantCreateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('owner.restaurants.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="restaurantCreateModalLabel">店舗登録</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- 店舗名 -->
                        <div class="form-group">
                            <label for="name">店舗名</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <!-- 店舗概要 -->
                        <div class="form-group">
                            <label for="description">店舗概要</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        <!-- エリア選択 -->
                        <div class="form-group">
                            <label for="area_id">エリア</label>
                            <select class="form-control" id="area_id" name="area_id" required>
                                @foreach($areas as $area)
                                <option value="{{ $area->id }}">{{ $area->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- ジャンル選択 -->
                        <div class="form-group">
                            <label for="genre_id">ジャンル</label>
                            <select class="form-control" id="genre_id" name="genre_id" required>
                                @foreach($genres as $genre)
                                <option value="{{ $genre->id }}">{{ $genre->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- 画像URL/アップロード -->
                        <div class="form-group">
                            <label for="image_url">店舗画像</label>
                            <input type="file" class="form-control" id="image_url" name="image_url" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                        <button type="submit" class="btn btn-success">登録</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 店舗編集モーダル -->
    <div class="modal fade" id="restaurantEditModal" tabindex="-1" aria-labelledby="restaurantEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="" id="restaurantEditForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="restaurantEditModalLabel">更新</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- 店舗名 -->
                        <div class="form-group">
                            <label for="edit-name">店舗名</label>
                            <input type="text" class="form-control" id="edit-name" name="name" required>
                        </div>
                        <!-- 店舗概要 -->
                        <div class="form-group">
                            <label for="edit-description">概要</label>
                            <textarea class="form-control" id="edit-description" name="description" rows="3" required></textarea>
                        </div>
                        <!-- エリア選択 -->
                        <div class="form-group">
                            <label for="edit-area-id">エリア</label>
                            <select class="form-control" id="edit-area-id" name="area_id" required>
                                @foreach($areas as $area)
                                <option value="{{ $area->id }}">{{ $area->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- ジャンル選択 -->
                        <div class="form-group">
                            <label for="edit-genre-id">ジャンル</label>
                            <select class="form-control" id="edit-genre-id" name="genre_id" required>
                                @foreach($genres as $genre)
                                <option value="{{ $genre->id }}">{{ $genre->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- 画像URL/アップロード -->
                        <div class="form-group">
                            <label for="edit-image-url">店舗画像</label>
                            <input type="file" class="form-control" id="edit-image-url" name="image_url" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                        <button type="submit" class="btn btn-primary">更新</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // モーダルに店舗情報を反映する処理
    $('#restaurantEditModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // ボタンのデータ属性から情報を取得
        var id = button.data('id');
        var name = button.data('name');
        var description = button.data('description');
        var areaId = button.data('area-id');
        var genreId = button.data('genre-id');
        var imageUrl = button.data('image-url');

        var modal = $(this);

        modal.find('#edit-name').val(name);
        modal.find('#edit-description').val(description);

        modal.find('#edit-area-id').val(areaId);
        modal.find('#edit-genre-id').val(genreId);

        if (imageUrl) {
            var previewImage = modal.find('#edit-image-preview');
            if (!previewImage.length) {
                modal.find('.form-group').last().append('<img id="edit-image-preview" src="" alt="プレビュー画像" style="max-width: 100%; height: 50%; margin-top: 10px;">');
            }
            modal.find('#edit-image-preview').attr('src', imageUrl);
        } else {
            modal.find('#edit-image-preview').remove();
        }

        $('#restaurantEditForm').attr('action', '/owner/restaurants/' + id);
    });

    $(document).ready(function() {
    });
</script>