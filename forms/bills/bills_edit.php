<div class="modal fade" id="{{_form}}_{{_mode}}" data-keyboard="false" data-backdrop="true" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-max" role="document">
	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h5 class="modal-title">{{name}}</h5>
      </div>
      <div class="modal-body">

<form id="{{_form}}EditForm" data-wb-form="{{_form}}" data-wb-item="{{_item}}"  class="form-horizontal" role="form">
    <input type="hidden" name="id" placeholder="Имя записи"  >
	<div class="form-group row">
        <label class="col-sm-3 form-control-label">Номер счёта</label>
        <div class="col-sm-3"><input type="text" class="form-control" name="number" placeholder="Номер счёта"></div>
        <label class="col-sm-3 form-control-label">Дата счёта</label>
        <div class="col-sm-3"><input type="datepicker" class="form-control" name="date" placeholder="Дата счёта"></div>
	</div>

	<div class="form-group row">
	  <label class="col-sm-3 form-control-label">Поставщик</label>
	   <div class="col-sm-9">
           <select class="form-control select2" role="foreach" data-wb-ajax="/form/select2/partners" data-wb-where='self="on" AND active="on"' name="organisation" placeholder="Получатель">
               <option value="{{id}}">
                   {{inn}} {{name}}
               </option>
           </select>
        </div>
	</div>

	<div class="form-group row">
	  <label class="col-sm-3 form-control-label">Покупатель</label>
	   <div class="col-sm-9">
           <select class="form-control select2" role="foreach" data-wb-ajax="/form/select2/partners" name="partner" data-wb-where='active="on"' placeholder="Плательщик">
               <option value="{{id}}">
                   {{inn}} {{name}}
               </option>
           </select>
        </div>
	</div>


    <div class="form-group" data-wb-role="multiinput" name="products">
        <div class="col-5">
            <select placeholder="Выберите продукцию" class="form-control select2" role="foreach" data-wb-ajax="/form/select2/products" data-wb-where='active="on"' name="product">
                <option value="{{id}}">{{name}} арт.{{articul}}</option>
            </select>
        </div>
        <div class="col-2">
            <input type="number" name="count" min="0" class="form-control text-right" placeholder="Кол-во">
        </div>
        <div class="col-2">
            <input type="number" name="price" min="0" class="form-control text-right" placeholder="Цена">
        </div>
        <div class="col-3">
            <input type="number" name="summ" min="0" class="form-control text-right" placeholder="Сумма" readonly>
        </div>
    </div>
    <div class="form-group row" name="totals">
        <div class="col-5"><label>Итого:</label></div>
        <div class="col-2 hidden-xs"><input type="number" data-name="count" class="form-control text-right" placeholder="Кол-во" readonly></div>
        <div class="col-2 hidden-xs">&nbsp;</div>
        <div class="col-3 .summ"><input type="number" name="summ" class="form-control text-right" placeholder="Сумма" readonly></div>
    </div>

</form>


    </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> Закрыть</button>
			<button type="button" class="btn btn-default" onclick="{{_form}}_print()"><span class="fa fa-print"></span> Печать</button>
            <button type="button" class="btn btn-primary" data-wb-formsave="#{{_form}}EditForm"><span class="fa fa-save"></span> Сохранить изменения</button>
		  </div>

		</div>
<div class="wb-print-box">
</div>

</div>
</div>

<script language="javascript">
    {{_form}}_count();
    $("#{{_form}}EditForm").delegate("[name=products] :input","change",function(){
        if ($(this).val()>"") {
            var item=$(this).parents(".row.wb-multiinput").find("[data-wb-field=product]").data("item");
            if ($(this).is("[data-wb-field=product]")) {
                $(this).parents(".row.wb-multiinput").find("[data-wb-field=price]").val(item.price);
            }
            var price=$(this).parents(".row.wb-multiinput").find("[data-wb-field=price]").val()*1;
            var count=$(this).parents(".row.wb-multiinput").find("[data-wb-field=count]").val()*1;

            if (price<0) {price=0;}
            if (count<1) {count=1;}
            $(this).parents(".row.wb-multiinput").find("[data-wb-field=price]").val(price);
            $(this).parents(".row.wb-multiinput").find("[data-wb-field=count]").val(count);
            {{_form}}_count();
        }
    });
    function {{_form}}_count() {
        var prods=$("#{{_form}}EditForm [name=products]");
        var totals=$("#{{_form}}EditForm [name=totals]");
        totals.find("[data-name=count]").val("0");
        totals.find("[name=summ]").val("0");
         prods.find(".row.wb-multiinput").each(function(){
                var t_count=totals.find("[data-name=count]").val()*1;
                var t_summ=totals.find("[name=summ]").val()*1;
                var price=$(this).find("[data-wb-field=price]").val()*1;
                var count=$(this).find("[data-wb-field=count]").val()*1;
                $(this).find("[data-wb-field=summ]").val(price*count);
                totals.find("[data-name=count]").val(t_count+count);
                totals.find("[name=summ]").val(t_summ+price*count);
        });
    }
    function {{_form}}_print() {
        $.ajax({
            async: false,
            type: 'POST',
            url: "/form/print/bills/_form",
            data: $("#{{_form}}EditForm").serialize(),
            success: function (data) {
                var res = data;
                console.log(res);
                $(".wb-print-box").html(data);
                $(".wb-print-box").print({
                            globalStyles : false,
                            deferred: $.Deferred().done(function() {
                                console.log('Printing done', arguments);
                                $(".wb-print-box").html("");
                            })
                });
            },
            error: function (data) {
                var res = "Ошибка!";
            }
        });
    }
</script>
