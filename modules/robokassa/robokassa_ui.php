<div data-wb-role="include" src="modal" data-wb-id="robokassaModal">
    <h3 class="modal-title" data-wb-html="#robokassaModal .modal-header">{{_LANG[warning]}}</h3>
    <form action="{{url}}" method="POST" data-wb-disallow="noname" data-wb-html="#robokassaModal .modal-body">
        <input type=hidden name=MrchLogin value="{{login}}">
        <input type=hidden name=OutSum class="total" value="{{summ}}">
        <input type=hidden name=InvId value="{{inv_id}}">
        <input type=hidden name=Desc value="{{inv_desc}}">
        <input type=hidden name=SignatureValue value="{{crc}}">
        <input type=hidden name=IncCurrLabel value="{{currency}}">
        <input type=hidden name=Culture value="{{culture}}">
        <input type=hidden name=IsTest value="{{test}}">
        <input type=hidden name=Shp_orderId value="{{Shp_orderId}}">
        <input type=hidden name=ResultURL value="{{ResultURL}}">

        <p>
                {{_LANG[info1]}}
                        <a href='http://www.robocassa.com' target='_blank'> RoboKassa</a>
                {{_LANG[info2]}}
        </p>
        <p><b>{{_LANG[summ]}}: <span class="total">{{summ}}</span> {{_SETT[currency]}}</b></p>
        <p>
            <img src="http://robokassa.ru/ru/Images/logo.png" style="width: 200px;" class="pull-right">
            <input class="btn btn-primary" type="submit" value="{{_LANG[continue]}}">
        </p>
    </form>

    <div data-wb-allow="noname" data-wb-html="#robokassaModal .modal-body">
        <h4>{{_LANG[warning]}}</h4>
        <p>Оплата доступна только авторизованным пользователям.</p>
        <p>Если вы уже зарегистрированы в системе,
        пожалуйста, авторизуйтесь, нажав кнопку "Войти".</p>
        <a href="/login" class="btn btn-primary"><i class="fa fa-check"></i> {{_LANG[signin]}}</a>
    </div>


</div>
<script data-wb-append="body">
    $(document).on("cart-recalc-done",function(){
        $("#robokassaModal input.total").val($(".cart-total").text());
        $("#robokassaModal span.total").text($(".cart-total").text());
    });
    $('#checkout').on("click",function(){$("#robokassaModal").modal("show");});
</script>

<script type="text/locale">
[eng]
warning         = "Warning"
continue        = "Continue"
info1           = "After clicking &quot;continue&quot; button you will be redirected to the"
info2           = "payment system website , where you can choose the most convenient form of payment for your order and make a payment. After completing the payment procedure, you will be automatically redirected back to our website."
summ            = "Amount to be paid"
warn1           = "Payment is available only to authorized users."
warn2           = "If you are already registered in the system, please sign in by clicking &quot;Sign in&quot; button."
signin          = "Sign in"
[rus]
warning         = "Внимание"
continue        = "Продолжить"
info1           = "После нажания кнопки &quot;продолжить&quot; вы будете перенаправлены на сайт платёжной системы"
info2           = ", где сможете выбрать наиболее удобную для Вас форму оплаты заказа и совершить платёж. После завершения процедуры оплаты Вы автоматически будете перенаправлены обратно на наш сайт."
summ            = "Сумма к оплате"
warn1           = "Оплата доступна только авторизованным пользователям."
warn2           = "Если вы уже зарегистрированы в системе, пожалуйста, авторизуйтесь, нажав кнопку &quot;Войти&quot;."
signin          = "Войти"
</script>
