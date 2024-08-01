@if (session('status') == 'verification-link-sent')
<div class="mb-4 font-medium text-sm text-green-600">
    新しい認証リンクがメールアドレスに送信されました。
</div>
@endif

<p>
    メールアドレスの確認リンクがメールに送信されました。
    メールが届いていない場合は、以下のリンクをクリックして再送信してください。
</p>

<form method="POST" action="{{ route('verification.send') }}">
    @csrf
    <button type="submit">
        認証リンクを再送信
    </button>
</form>