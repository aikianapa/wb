<div data-wb-role="include" src="modal" data-wb-id="payeerModal">
    <form method="post" action="https://payeer.com/merchant/" data-wb-html="#payeerModal .modal-body">
        <h3 class="modal-title" data-wb-html="#payeerModal .modal-header">{{_LANG[header]}}</h3>
        <div class="row" data-wb-role="formdata" data-wb-form="orders" data-wb-item="{{_SESS[order_id]}}">
			<div class="col-12 col-xs-12 col-sm-6">
				<input type="hidden" name="m_shop">
				<input type="hidden" name="m_orderid">
				<input type="hidden" name="m_amount">
				<input type="hidden" name="m_curr">
				<input type="hidden" name="m_desc">
				<input type="hidden" name="m_sign">
				<input type="hidden" name="form[ps]" value="2609">
				<input type="hidden" name="form[curr[2609]]" value="USD">
			</div>
			<div class="col-12 col-xs-12">
				<h1 class="text-center">{{total}} {{_SETT.payeer.currency}}</h1>
			</div>
		</div>
        <input type="button" name="m_process" onClick="$('#payeerModal form').trigger('submit');" class="btn btn-primary payeer-button" value="{{_LANG[continue]}}"  data-wb-html="#payeerModal .modal-footer">
    </form>
</div>
<style>
    #payeerModal label {line-height:25px;}
</style>
<script>
    var payeer = function() {
        $.ajax({
            async: true
            , type: 'POST'
            , url: $("#payeerModal form").attr("action")
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
header          = "Confirm checkout"
continue        = "Continue"
checkout	= "Checkout"
[rus]
header          = "Подтвердите оплату"
continue        = "Продолжить"
checkout	= "Оплатить"
</script>
