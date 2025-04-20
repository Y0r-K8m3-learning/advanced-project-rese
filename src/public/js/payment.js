document.addEventListener("DOMContentLoaded", function() {
    /* 基本設定 */
    const stripe = Stripe(window.stripePublicKey);
    const elements = stripe.elements();

    // カード情報の入力フィールドを生成
    const cardNumber = elements.create('cardNumber');
    cardNumber.mount('#card-number');

    const cardExpiry = elements.create('cardExpiry');
    cardExpiry.mount('#card-expiry');

    const cardCvc = elements.create('cardCvc');
    cardCvc.mount('#card-cvc');

    // フォームの送信イベントリスナー
    const form = document.getElementById('card-form');
    form.addEventListener('submit', function(event) {
        event.preventDefault();

        if (!confirm('予約を確定します。よろしいですか？')) {
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

    // Stripeトークンをフォームに追加して送信
    function stripeTokenHandler(token) {
        // 必要なデータを追加
        const fields = {
            stripeToken: token.id,
            total_price: document.getElementById('total_price').value.replace(/[^\d]/g, ''),
            date: document.getElementById('date').value,
            time: document.getElementById('time').value,
            restaurant_id: document.getElementById('restaurant_id').value,
        };

        Object.keys(fields).forEach(name => {
            const input = document.createElement('input');
            input.setAttribute('type', 'hidden');
            input.setAttribute('name', name);
            input.setAttribute('value', fields[name]);
            form.appendChild(input);
        });

        form.submit();
    }

    // 合計金額の計算
    function calculateTotal() {
        const numberOfPeople = parseFloat(document.getElementById('number_of_people').value) || 0;
        const pricePerPerson = parseFloat(document.getElementById('price_per_person').value) || 0;
        const totalPrice = numberOfPeople * pricePerPerson;
        document.getElementById('total_price').value = `¥${totalPrice}`;
    }

    // 人数変更時に合計金額を更新
    document.getElementById('number_of_people').addEventListener('input', calculateTotal);

    // 初期合計金額の計算
    calculateTotal();
});
