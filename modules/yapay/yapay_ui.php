<div data-wb-role="include" src="modal" data-wb-id="yapayModal">
    <form method="POST" action="https://money.yandex.ru/quickpay/confirm.xml" data-wb-html="#yapayModal .modal-body">
        <h3 class="modal-title" data-wb-html="#yapayModal .modal-header">{{_LANG[header]}}</h3>
        <div class="row" data-wb-role="formdata" data-wb-form="orders" data-wb-item="{{_SESS[order_id]}}">
			<div class="col-12 col-xs-12 col-sm-6">
				<input type="hidden" name="receiver" value="{{receiver}}">
				<input type="hidden" name="short-dest" value="Оплата заказа {{_ENV[route][host]}} № {{id}}">
				<input type="hidden" name="label" value="{{id}}">
                                <input type="hidden" name="language" value="{{_SESS[lang]}}">
				<input type="hidden" name="quickpay-form" value="shop">
				<input type="hidden" name="successURL" value="{{_ENV[route][hostp]}}/module/yapay/success/{{id}}/{{apikey}}">
				<input type="hidden" name="targets" value="Заказ {{id}}">
				<input type="hidden" name="sum" value="{{total}}" data-type="number">
				<input type="hidden" name="comment" value="">
				<input type="hidden" name="need-fio" value="false">
				<input type="hidden" name="need-email" value="false">
				<input type="hidden" name="need-phone" value="false">
				<input type="hidden" name="need-address" value="false">
				<label><i class="fa ya-card"></i> <input type="radio" name="paymentType" value="AC" checked> {{_LANG[ac]}}</label>
				<br>
				<label><i class="fa ya-wallet"></i> <input type="radio" name="paymentType" value="PC"> {{_LANG[pc]}}</label>
				<br>
				<label> <i class="fa ya-phone"></i> <input type="radio" name="paymentType" value="MC"> {{_LANG[mc]}}</label>
			</div>
			<div class="col-12 col-xs-12 col-sm-6">
				<h1>{{total}}</h1>
			</div>
		</div>
        <input type="button" onClick="$('#yapayModal form').trigger('submit');" class="btn btn-primary yapay-button" value="{{_LANG[continue]}}"  data-wb-html="#yapayModal .modal-footer">
    </form>
</div>
<style>
    #yapayModal .fa {background-size: contain;  width: 30px; height: 30px; background-size: contain; top:8px; position:relative; margin:0 20px;}
    #yapayModal .fa.ya-wallet {background-image:url(/engine/modules/yapay/img/wallet.svg);}
    #yapayModal .fa.ya-card {background-image:url(/engine/modules/yapay/img/card.svg);}
    #yapayModal .fa.ya-phone {background-image:url(/engine/modules/yapay/img/phone.svg);}
    #yapayModal label {line-height:25px;}
</style>
<script>
    var yapay = function() {
        $.ajax({
            async: true
            , type: 'POST'
            , url: "/module/yapay/checkout/"
            , success: function (order) {
                order=$.parseJSON(order);
                var uid=$('#checkout').attr("data-user");
                if (order.id !== undefined && (order.total * 1) > 0 && uid!==undefined && uid>"0") {

                }
            }
        });
    };
</script>
<script type="text/locale">
[eng]
header          = "Choose payment method"
continue        = "Continue"
ac              = "Credit card"
pc              = "Yandex.Money"
mc              = "Mobile ballnce"
[rus]
header          = "Выберите способ оплаты"
continue        = "Продолжить"
ac              = "Банковской картой"
pc              = "Яндекс.Деньгами"
mc              = "С баланса телефона"
</script>
