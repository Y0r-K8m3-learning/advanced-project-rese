<x-app-layout>
    <div class="container">
        <h1>{{$status}}</h1>
        <p><strong>店名:</strong> {{ $reservation->restaurant->name }}</p>
        <p><strong>日付:</strong> {{ $reservation->reservation_date }}</p>
        <p><strong>時間:</strong> {{ $reservation->reservation_time }}</p>
        <p><strong>人数:</strong> {{ $reservation->number_of_people }}人</p>
    </div>
</x-app-layout>