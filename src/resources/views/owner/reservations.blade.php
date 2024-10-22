<x-app-layout>
    <div class="container mt-5">
        <div class="mb-2">
            <a href="{{ route('owner') }}" class="btn btn-secondary">戻る</a>
        </div>
        <h2>予約一覧 - {{ $restaurant->name }} -</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>日付</th>
                    <th>時間</th>
                    <th>人数</th>
                    <th>予約者</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reservations as $reservation)
                <tr>
                    <td>{{ $reservation->reservation_date }}</td>
                    <td>{{ $reservation->reservation_time }}</td>
                    <td>{{ $reservation->number_of_people }}</td>
                    <td>{{ $reservation->user->name }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>