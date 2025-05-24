    @section('js')
    <script script src="https://js.stripe.com/v3/"></script>

    <script>
        window.stripePublicKey = "{{ config('stripe.stripe_public_key') }}";
    </script>
    <script src="{{ asset('js/payment.js') }}"></script>
    @endsection
    <x-app-layout>

        <div class="container">
            @if (session('flash_alert'))
            <div class="alert alert-danger">{{ session('flash_alert') }}</div>
            @elseif(session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
            @endif
            <div class="mx-auto" style="width: 600px;">
                <div class="col-6 card fw-bold">
                    <div class="card-header  bg-blue-100 ">-決済-
                        カード情報を入力してください。

                    </div>
                    <form id="card-form" action="{{ route('payment.store') }}" method="POST">
                        <input id="restaurant_id" type="hidden" name="restaurant_id" value="{{ $restaurant_id }}">
                        <input type="hidden" id="date" name="date" value="{{ $date }}">
                        <input type="hidden" id="time" name="time" value="{{ $time }}">

                        <div class="card-body">
                            <!-- 日付 -->
                            <div class="form-group">
                                <label for="date_labale">日付</label>
                                <input type="text" id="date" class="form-control p-1" name="date" value="{{$date}}" readonly>
                            </div>

                            <!-- 時刻 -->
                            <div class="form-group">
                                <label for="date_labale">時刻</label>
                                <input type="text" id="time" class="form-control  p-1" name="time" value="{{$time}}" readonly>
                            </div>

                            <!-- 人数選択 -->
                            <div class="form-group">
                                <label for="number_of_people">人数</label>
                                <input type="number" id="number_of_people" class="form-control p-0" name="number" value="{{$number}}" min="1" readonly>
                            </div>

                            <!-- 単価を設定 -->
                            <div class="form-group">
                                <label for="price_per_person">単価</label>
                                <input type="text" id="price_per_person" class="form-control p-1" value="1000" readonly>
                            </div>

                            <!-- 合計金額表示 -->
                            <div class="form-group">
                                <label for="total_price">合計金額</label>
                                <input type="text" id="total_price" name="amount" class="form-control p-1" readonly>
                            </div>



                            @csrf
                            <div>テスト: 4242 4242 4242 4242</div>
                            <div>
                                <label for="card_number">カード番号</label>
                                <div id="card-number" class="form-control"></div>
                            </div>

                            <div>
                                <label for="card_expiry">有効期限</label>
                                <div id="card-expiry" class="form-control"></div>
                            </div>

                            <div>
                                <label for="card-cvc">セキュリティコード</label>
                                <div id="card-cvc" class="form-control"></div>
                            </div>

                            <div id="card-errors" class="text-danger"></div>

                            <button class="mt-3 btn btn-primary" id="payment-button">支払い</button>
                            <!-- 戻るボタン -->
                            <button class="btn btn-secondary mt-3 " onclick="window.history.back()">戻る</button>
                    </form>
                </div>
            </div>
        </div>


    </x-app-layout>