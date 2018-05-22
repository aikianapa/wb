<div data-wb-role="include" src="modal" data-wb-id="robokassaModal">
    <h3 class="modal-title" data-wb-html="#robokassaModal .modal-header">Внимание</h3>
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

        <p>После нажания кнопки "продолжить" вы будете перенаправлены на сайт платёжной системы
        <a href="http://www.robocassa.com" target="_blank">RoboKassa</a>,
        где сможете выбрать наиболее удобную для Вас форму оплаты заказа и совершить платёж. 
        После завершения процедуры оплаты Вы автоматически будете перенаправлены обратно на наш сайт.</p>
        <p><b>Сумма к оплате: <span class="total">{{summ}}</span> рублей</b></p>
        <p>
            <img src="http://robokassa.ru/ru/Images/logo.png" style="width: 200px;" class="pull-right">
            <input class="btn btn-primary" type="submit" value="Продолжить">
        </p>
    </form>

    <div data-wb-allow="noname" data-wb-html="#robokassaModal .modal-body">
        <h4>Внимание</h4>
        <p>Оплата доступна только авторизованым пользователям.</p>
        <p>Если вы уже зарегистрированы в системе,
        пожалуйста, авторизуйтесь, нажав кнопку "Войти".</p>
        <a href="/login" class="btn btn-primary"><i class="fa fa-check"></i> Войти</a>
    </div>


</div>
<script data-wb-append="body">
    $(document).on("cart-recalc-done",function(){
        $("#robokassaModal input.total").val($(".cart-total").text());
        $("#robokassaModal span.total").text($(".cart-total").text());
    });
    $('#checkout').on("click",function(){$("#robokassaModal").modal("show");});
</script>
