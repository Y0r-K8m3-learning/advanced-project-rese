@extends('layouts/app')


@section('css')
<link rel="stylesheet" href="{{ asset('css/search.css')}}">
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
    <form method="GET" action="/search">
        @csrf
        <div class="date-switcher">
            <div class="date-switcher-inner">
                <button class="date-switcher__button" type="submit" name="prev_date">&lt;</button>
                <input class="search_date" name="search_date" content__heading" value="{{$search_date}}" readonly>
                <button class="date-switcher__button" name="next_date">&gt;</button>
            </div>
        </div>
    </form>
    <div class="search__inner">
        <table class="search__table">
            <tr class="search__header-row search__row">
                <th class="search__label">名前</th>
                <th class="search__label">勤務開始</th>
                <th class="search__label">勤務終了</th>
                <th class="search__label">休憩時間</th>
                <th class="search__label">勤務時間</th>
                <th class="search__label"></th>
            </tr>
            @foreach($sumdata as $data )
            <tr class="search__row">
                <td class="search__data">{{$data['name']}}</td>
                <td class="search__data">{{$data['work_start']}}</td>
                <td class="search__data">{{$data['work_end']}}</td>
                <td class="search__data">{{$data['break_total_time']}}</td>
                <td class="search__data">{{$data['work_total_time']}}</td>
            </tr>
            @endforeach
        </table>
    </div>
    <div class="search-form-subcontent__pagenation">
        <input class="search_date" name="search_date" content__heading" hidden="{{$search_date}}" readonly>
        @if(count($sumdata) >0)
        {{ $sumdata?? $sumdata->appends(request()->query())->links('vendor.pagination.custom')}}
        @endif

    </div>

</div>
@endsection