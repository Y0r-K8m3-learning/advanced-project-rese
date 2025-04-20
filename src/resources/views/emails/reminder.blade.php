<h1>リマインダー</h1>
<p>{{ $reservation->user->name }} 様、{{ $reservation->restaurant->name }} の予約確認です。</p>
<p>予約日時: {{ $reservation->reservation_date }} {{ $reservation->reservation_time }}</p>