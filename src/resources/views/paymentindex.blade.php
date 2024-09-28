<x-app-layout>
    <div class="container">
        @if (session('flash_alert'))
        <div class="alert alert-danger">{{ session('flash_alert') }}</div>
        @elseif(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
        @endif
        <div class="p-5">
            <div class="col-6 card">
                <div class="card-header">決済</div>
                <div class="card-body">
                    <!-- 人数選択 -->
                    <div class="form-group">
                        <label for="number_of_people">人数</label>
                        <input type="number" id="number_of_people" class="form-control" name="number" value="{{$number}}" min="1" readonly>
                    </div>

                    <!-- 単価を設定 -->
                    <div class="form-group">
                        <label for="price_per_person">単価</label>
                        <input type="text" id="price_per_person" class="form-control" value="1000" readonly>
                    </div>

                    <!-- 合計金額表示 -->
                    <div class="form-group">
                        <label for="total_price">合計金額</label>
                        <input type="text" id="total_price" class="form-control" readonly>
                    </div>



                    <form id="card-form" action="{{ route('payment.store') }}" method="POST">
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
                        <button class="btn btn-secondary" onclick="window.history.back()">戻る</button>
                    </form>
                </div>
            </div>
        </div>

        <script src="https://js.stripe.com/v3/"></script>
        <script>
            /* 基本設定 */
            const stripe_public_key = "{{ config('stripe.stripe_public_key') }}";
            const stripe = Stripe(stripe_public_key);
            const elements = stripe.elements();

            var cardNumber = elements.create('cardNumber');
            cardNumber.mount('#card-number');

            var cardExpiry = elements.create('cardExpiry');
            cardExpiry.mount('#card-expiry');

            var cardCvc = elements.create('cardCvc');
            cardCvc.mount('#card-cvc');

            var form = document.getElementById('card-form');
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                if (!confirm('支払いを確定しますか？')) {
                    return; // 確認しない場合は中断
                }
                stripe.createToken(cardNumber).then(function(result) {
                    if (result.error) {
                        document.getElementById('card-errors').textContent = result.error.message;
                    } else {
                        stripeTokenHandler(result.token);
                    }
                });
            });

            function stripeTokenHandler(token) {
                var form = document.getElementById('card-form');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', token.id);
                form.appendChild(hiddenInput);

                // 合計金額
                var totalPriceInput = document.createElement('input');
                totalPriceInput.setAttribute('type', 'hidden');
                totalPriceInput.setAttribute('name', 'total_price');
                totalPriceInput.setAttribute('value', document.getElementById('total_price').value.replace(/[^\d]/g, ''));
                form.appendChild(totalPriceInput);
                
                form.submit();
            }

            // 合計金額の計算
            function calculateTotal() {
                const numberOfPeople = document.getElementById('number_of_people').value;
                const pricePerPerson = document.getElementById('price_per_person').value;
                const totalPrice = numberOfPeople * pricePerPerson;
                document.getElementById('total_price').value = `¥${totalPrice}`;
            }

            // イベントリスナーを人数変更時に追加
            document.getElementById('number_of_people').addEventListener('input', calculateTotal);

            // 初期合計金額の計算
            calculateTotal();
        </script>
</x-app-layout>