@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login__content">
  <div class="login-form__heading">
    ログイン
  </div>
  <form class="form" method="post" action="/login">
    @csrf
    <div class="form__group">

      <div class="form__group-content">
        <div class="form__input--text">
          <input type="text" name="email" value="{{ old('email') }}" placeholder="メールアドレス" />
        </div>
        <div class="form__error">
          @error('email')
          {{ $message }}
          @enderror
        </div>
      </div>
    </div>
    <div class="form__group">

      <div class="form__group-content">
        <div class="form__input--text">
          <input type="password" name="password" placeholder="パスワード" />
        </div>
        <div class="form__error">
          @error('password')
          {{ $message }}
          @enderror
        </div>
      </div>
    </div>
    <div class="form__group">
      <div class="form__button">
        <button class="form__button-submit" type="submit">ログイン</button>
      </div>
      <div class="register__link">
        <p class="register__label">アカウントをお持ちでない方はこちらから</p>
        <a class="register__button-submit" href="/register">会員登録</a>
      </div>
    </div>
  </form>

</div>
@endsection