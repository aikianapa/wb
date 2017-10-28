<div class="modal fade" id="{{_GET[form]}}_{{_GET[mode]}}" data-keyboard="false" data-backdrop="true" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-max" role="document">
	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h5 class="modal-title">{{name}}</h5>
      </div>
      <div class="modal-body">

<form id="{{_GET[form]}}EditForm" data-wb-form="{{_GET[form]}}" data-wb-item="{{_GET[item]}}"  class="form-horizontal" role="form">
    <input type="hidden" name="id" placeholder="Имя записи"  >
	<div class="form-group row">
	  <label class="col-sm-3 form-control-label">Номер счёта</label>
	   <div class="col-sm-9"><input type="text" class="form-control" name="number" placeholder="Номер счёта"></div>
	</div>

	<div class="form-group row">
	  <label class="col-sm-3 form-control-label">Плательщик</label>
	   <div class="col-sm-9">
           <select class="form-control select2" role="foreach" data-wb-ajax="/form/select2/partners" name="payer" data-wb-where='active="on"' placeholder="Плательщик">
               <option value="{{id}}">
                   {{inn}} {{name}} 
               </option>
           </select>
        </div>
	</div>
    
	<div class="form-group row">
	  <label class="col-sm-3 form-control-label">Получатель</label>
	   <div class="col-sm-9">
           <select class="form-control select2" role="foreach" data-wb-ajax="/form/select2/partners" data-wb-where='self="on" AND active="on"' name="recipient" placeholder="Получатель">
               <option value="{{id}}">
                   {{inn}} {{name}} 
               </option>
           </select>
        </div>
	</div>
    
    <div class="form-group" data-wb-role="multiinput" name="products">
        <div class="col-5">
            <select placeholder="Выберите продукцию" class="form-control select2" role="foreach" data-wb-ajax="/form/select2/products" name="recipient">
                <option value="{{id}}">{{name}} арт.{{articul}}</option>
            </select>
        </div>
        <div class="col-2">
            <input type="number" min="0" class="form-control text-right" placeholder="Кол-во">
        </div>
        <div class="col-2">
            <input type="number" min="0" class="form-control text-right" placeholder="Цена">
        </div>
        <div class="col-3">
            <input type="number" min="0" class="form-control text-right" placeholder="Сумма">
        </div>
    </div>
    
</form>


    </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Закрыть</button>
			<button type="button" class="btn btn-primary" data-wb-formsave="#{{_GET[form]}}EditForm"><span class="glyphicon glyphicon-ok"></span> Сохранить изменения</button>
		  </div>

		</div>
</div>
</div>
