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
            return;
        }

        // PaymentMethodを作成してフォーム送信
        createPaymentMethodAndSubmit();
    });

    // PaymentMethodを作成してフォームに追加して送信
    async function createPaymentMethodAndSubmit() {
        const {paymentMethod, error} = await stripe.createPaymentMethod({
            type: 'card',
            card: cardNumber,
        });

        if (error) {
            document.getElementById('card-errors').textContent = error.message;
            return;
        }

        // PaymentMethodIDをフォームに追加
        const paymentMethodInput = document.createElement('input');
        paymentMethodInput.setAttribute('type', 'hidden');
        paymentMethodInput.setAttribute('name', 'payment_method_id');
        paymentMethodInput.setAttribute('value', paymentMethod.id);
        form.appendChild(paymentMethodInput);

        // 合計金額を追加
        const totalPriceInput = document.createElement('input');
        totalPriceInput.setAttribute('type', 'hidden');
        totalPriceInput.setAttribute('name', 'total_price');
        totalPriceInput.setAttribute('value', document.getElementById('total_price').value.replace(/[^\d]/g, ''));
        form.appendChild(totalPriceInput);

        // フォーム送信
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
