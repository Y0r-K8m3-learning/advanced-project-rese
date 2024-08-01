@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/search-user.css') }}">
<link rel="stylesheet" href="{{ asset('css/paginate.css') }}">
@endsection

@section('link')
<form action="/logout" method="post">
    @csrf
    <input class="header__link" type="submit" value="logout">
</form>
@endsection

@section('content')
<div class="admin">
    <form method="GET" action="/search/user/attendance">
        @csrf
        <div class="date-switcher">
            <div class="date-switcher-inner">
                <input class="user_name" name="user_name" value="{{ $user_name }}" readonly>
                <input class="user_id" name="user_id" value="{{ $user_id }}" readonly hidden>
            </div>
        </div>
    </form>
    <div class="search__inner">
        <table class="search__table">
            <tr class="search__header-row search__row">
                <th class="search__label">日付</th>
                <th class="search__label">勤務開始</th>
                <th class="search__label">勤務終了</th>
                <th class="search__label">休憩時間</th>
                <th class="search__label">勤務時間</th>
                <th class="search__label"></th>
            </tr>
            @foreach($sumdata as $data)
            <tr class="search__row">
                <td class="search__data">{{ $data['work_date'] }}</td>
                <td class="search__data">{{ $data['work_start'] }}</td>
                <td class="search__data">{{ $data['work_end'] }}</td>
                <td class="search__data">{{ $data['break_total_time'] }}</td>
                <td class="search__data">{{ $data['work_total_time'] }}</td>
            </tr>
            @endforeach
        </table>
    </div>
    <div class="search-form-subcontent__pagenation">
        @if(count($sumdata) > 0)
        {{ $sumdata->appends(['user_name' => $user_name, 'user_id' => $user_id])->links() }}
        @endif
    </div>
</div>
@endsection