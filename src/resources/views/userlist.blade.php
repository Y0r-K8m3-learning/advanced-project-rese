@extends('layouts/app')


@section('css')
<link rel="stylesheet" href="{{ asset('css/userlist.css')}}">
<link rel="stylesheet" href="{{ asset('css/paginate.css') }}">

@endsection

@section('link')
<form action="/logout" method="post">
    @csrf
    <input class="header__link" type="submit" value="logout">
</form>
@endsection

@section('content')
<div class="search__content">
    <div class="date-switcher">
        <div class="user-list-title">
            ユーザ一覧

        </div>
    </div>
    <div class="search__inner">
        <table class="search__table">
            <tr class="admin__header-row admin__row">
                <th class="admin__label">名前</th>
                <th class="admin__label">
                </th>
            </tr>
            @foreach($users as $user )
            <tr class="search__row">
                <td class="search__data">{{$user['name']}}</td>
                <td class="search__data">
                    <form action="/search/user/attendance" method="GET">
                        <input type="hidden" name="user_name" value="{{ $user['name'] }}">
                        <input type="hidden" name="user_id" value="{{ $user['id'] }}">
                        <div class="update-form__button">
                            <button class="update-form__button-submit" type="submit">勤怠</button>
                        </div>
                    </form>
            </tr>
            @endforeach
        </table>
    </div>
    <div class="search-form-subcontent__pagenation">

        @if(count($users) >0)
        {{ $users?? $users->appends(request()->query())->links('vendor.pagination.custom')}}
        @endif

    </div>

</div>
@endsection