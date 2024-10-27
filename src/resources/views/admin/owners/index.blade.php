<x-app-layout>
    <div class="container mt-5 w-75">
        <h2 class="fw-bold fs-4 mb-4">管理画面</h2>

        @if(session('success'))
        <script>
            alert("{{ session('success') }}");
        </script>
        @endif

        <!-- オーナー登録ボタン -->
        <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#ownerCreateModal">
            <i class="bi bi-person-plus"></i> 登録
        </button>

        <!-- メール送信ボタン -->
        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#emailSendModal">
            <i class="bi bi-envelope"></i> メール送信 @利用者
        </button>

        <!-- オーナー一覧 -->
        <div class="row mt-4">
            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            <div class="col-12 rounded">
                <table class="table table-striped table-hover  table-bordered align-middle table-secondary shadow ">
                    <thead class="table-secondary text-center">
                        <tr>
                            <th>ID</th>
                            <th>名前</th>
                            <th>メールアドレス</th>
                            <th>登録日</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($owners as $owner)
                        <tr class="text-center">
                            <td>{{ $owner->id }}</td>
                            <td>{{ $owner->name }}</td>
                            <td>{{ $owner->email }}</td>
                            <td>{{ $owner->created_at->format('Y-m-d') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>

    <!-- オーナー登録モーダル -->
    <div class="modal fade" id="ownerCreateModal" tabindex="-1" aria-labelledby="ownerCreateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.owners.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="ownerCreateModalLabel">登録</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- 名前 -->
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">名前</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <!-- メールアドレス -->
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">メールアドレス</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <!-- パスワード -->
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">パスワード</label>
                            <input type="password" class="form-control" id="password" name="password" required>
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

    <div class="modal fade" id="emailSendModal" tabindex="-1" aria-labelledby="emailSendModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.sendMailToAll') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="emailSendModalLabel">メール送信</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="subject" class="form-label">件名</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="message" class="form-label">本文</label>
                            <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                        <button type="submit" class="btn btn-primary">送信</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>