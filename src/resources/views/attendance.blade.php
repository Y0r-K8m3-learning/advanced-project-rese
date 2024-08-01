@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="attendance__alert">

</div>
<form action="/attendance" method="post">
    @csrf
    <p class = "attendance_user">{{Auth::user()['name']}}さんお疲れ様です！</p>
    <input type="text" name="user_id" value="{{Auth::user()['id']}}" hidden>
    <div class="attendance__content">
        <div class="attendance__panel">
        </div>
        <div class="attendance-input-table">

            <table class="attendance-input-table__inner">
                <tr class="attendance-table__row">
                    <th class="attendance-table__item">
                        <button class="attendance__button-submit  {{ $readonly_status['attendance_start'] ? 'readonly' : '' }}" type="submit" readonly name="start" {{ $readonly_status['attendance_start']? 'readonly' : '' }}>勤務開始</button>

                    </th>
                    <th class=" attendance-table__item">
                        <button class="attendance__button-submit  {{ $readonly_status['attendance_end'] ? 'readonly' : '' }}" type="submit" name="end" {{ $readonly_status['attendance_end'] ? 'readonly' : '' }}>勤務終了</button>
                    </th>
                </tr>
                <tr class="attendance-table__row">
                    <th class="attendance-table__item">
                        <button class="attendance__button-submit {{ $readonly_status['break_start'] ? 'readonly' : '' }}" type="submit" name="break-start" {{ $readonly_status['break_start'] ? 'readonly' : '' }}>休憩開始</button>
                    </th>
                    <th class="attendance-table__item"><button class="attendance__button-submit  {{ $readonly_status['break_end'] ? 'readonly' : '' }}" type="submit" name="break-end" {{ $readonly_status['break_end'] ? 'readonly' : '' }}>休憩終了</button></th>

                </tr>
            </table>
        </div>
    </div>
</form>
@endsection