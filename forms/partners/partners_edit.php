<div class="modal fade" id="{{_form}}_{{_mode}}" data-keyboard="false" data-backdrop="true" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
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
    <input type="hidden" name="user_id" placeholder="ID в Users"  >
	<div class="form-group row">
	  <label class="col-sm-3 form-control-label">Наименование</label>
	   <div class="col-sm-9"><input type="text" class="form-control" name="name" placeholder="Наименование"></div>
	</div>
  <div class="nav-active-primary">
  <ul class="nav nav-tabs" role="tablist">
  	<li class="nav-item"><a class="nav-link active" href="#{{_form}}Descr" data-toggle="tab">Характеристики</a></li>
    <li class="nav-item"><a class="nav-link" href="#{{_form}}Text" data-toggle="tab">Описание</a></li>
    <li class="nav-item"><a class="nav-link" href="#{{_form}}Map" data-toggle="tab">Карта</a></li>
    <li class="nav-item"><a class="nav-link" href="#{{_form}}Images" data-toggle="tab">Изображения</a></li>
  </ul>
  </div>
  <div class="tab-content  p-a m-b-md">
  <br />
    <div id="{{_form}}Descr" class="tab-pane fade show active" role="tabpanel">
    	<div class="form-group row">
    	  <label class="col-sm-3 form-control-label">Полное наименование</label>
    	   <div class="col-sm-9"><input type="text" class="form-control" name="fullname" placeholder="Полное наименование"></div>
    	</div>

    	<div class="form-group row">
    	  <label class="col-sm-3 form-control-label">Телефон</label>
    	   <div class="col-sm-3"><input type="phone" class="form-control" name="phone" placeholder="Телефон"></div>

    	  <label class="col-sm-2 form-control-label">Эл.почта</label>
    	   <div class="col-sm-4"><input type="email" class="form-control" name="email" placeholder="Эл.почта"></div>
    	</div>

    	<div class="form-group row">

    	   <div class="col-sm-4"><label class="form-control-label">ОГРН</label><input type="text" class="form-control" name="ogrn" placeholder="ОГРН"></div>
    	   <div class="col-sm-4"><label class="form-control-label">ИНН</label><input type="text" class="form-control" name="inn" placeholder="ИНН"></div>
    	   <div class="col-sm-4"><label class="form-control-label">КПП</label><input type="text" class="form-control" name="kpp" placeholder="КПП"></div>
    	</div>

      <div class="form-group row">
            <div class="col-sm-4"><label class="form-control-label">Расч.счёт</label><input type="text" class="form-control" name="account" placeholder="Расчётный счёт"></div>
            <div class="col-sm-4"><label class="form-control-label">БИК</label><input type="text" class="form-control" name="bik" placeholder="БИК" onchange="javascript:$(this).bankBikSearch();"></div>
            <div class="col-sm-4"><label class="form-control-label">Корр.счёт</label><input type="text" class="form-control" name="ks" placeholder="Корреспондентский счёт"></div>
    	</div>

      <div class="form-group row">
        <label class="col-sm-3 form-control-label">Банк</label>
         <div class="col-sm-9"><input type="text" class="form-control" name="bank" placeholder="Наименование банка"></div>
      </div>

      <div class="form-group row">
    	  <label class="col-sm-3 form-control-label">Адрес юридический</label>
    	   <div class="col-sm-9"><input type="text" class="form-control" name="address_jur" placeholder="Адрес юридический"></div>
      </div>

        <div class="form-group row">
      	  <label class="col-sm-3 form-control-label">Адрес фактический</label>
      	   <div class="col-sm-9"><input type="text" class="form-control" name="address" placeholder="Адрес фактический"></div>
        </div>


        <div class="form-group row">
            <label class="col-sm-3 form-control-label">Руководитель</label>
            <div class="col-sm-4"><input type="text" class="form-control" name="boss" value="Генеральный директор" placeholder="Должность руководителя"></div>
            <div class="col-sm-5"><input type="text" class="form-control" name="bossname" placeholder="ФИО руководителя"></div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 form-control-label">Гл.бухгалтер</label>
            <div class="col-sm-4"><input type="text" class="form-control" name="buch" value="Главный бухгалтер" placeholder="Главный бухгалтер"></div>
            <div class="col-sm-5"><input type="text" class="form-control" name="buchname" placeholder="ФИО гл.бухгалтера"></div>
        </div>

    	<div class="form-group row">
    		<label class="col-sm-2 form-control-label">Отображать</label>
    		<div class="col-sm-2"><label class="switch switch-success"><input type="checkbox" name="active"><span></span></label></div>
    		<label class="col-sm-3 form-control-label">Своё предприятие</label>
    		<div class="col-sm-2"><label class="switch switch-success"><input type="checkbox" name="self"><span></span></label></div>
    	</div>
    </div>
    <div id="{{_form}}Map" class="tab-pane fade show" role="tabpanel">
      <div data-wb-role="module" src="yamap" editable zoom="12"></div>
    </div>
    <div id="{{_form}}Text" class="tab-pane fade" data-wb-role="include" src="editor" role="tabpanel"></div>
    <div id="{{_form}}Images" class="tab-pane fade" data-wb-role="include" src="uploader" role="tabpanel">
        <div class="form-group row">
            <label class="col-sm-2 form-control-label">Логотип</label>
            <div class="col-sm-3">
                <input type="hidden" name="logo" data-wb-role="uploader"> </div>
        </div>
    </div>
  </div>
</form>


    </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Закрыть</button>
			<button type="button" class="btn btn-primary" data-wb-formsave="#{{_form}}EditForm"><span class="glyphicon glyphicon-ok"></span> Сохранить изменения</button>
		  </div>

		</div>
</div>
</div>

<script language="javascript">
    jQuery.fn.bankBikSearch = function() {
        $.get("http://www.bik-info.ru/api.html?type=json&bik="+$(this).val(),function(data){
            if (data!==undefined && data.error==undefined) {
                $("#{{_form}}_{{_mode}} form [name=bank]").val(data.namemini+" Г."+data.city);
                $("#{{_form}}_{{_mode}} form [name=ks]").val(data.ks);
            }
        });

    }

</script>
