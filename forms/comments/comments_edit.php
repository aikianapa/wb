<div class="modal fade" id="{{_GET[form]}}_{{_GET[mode]}}" data-keyboard="false" data-backdrop="true" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h5 class="modal-title">{{header}}</h5>
      </div>
      <div class="modal-body">

<form id="{{_GET[form]}}EditForm" data-wb-form="comments" data-wb-item="{{_GET[item]}}"  class="form-horizontal" role="form">
	<input type="hidden" name="date">
	<input type="hidden" name="user_id" value="{{_SESS[user_id]}}">
	<input type="hidden" name="target_form" value="{{_ENV[route][form]}}" role="where" data='target_form=""' data-wb-hide="wb">
	<input type="hidden" name="target_item" value="{{_ENV[route][item]}}" role="where" data='target_item=""' data-wb-hide="wb">
	<div class="form-group row">
	  <label class="col-sm-3 control-label">Ваше имя</label>
	   <div class="col-sm-9"><input type="text" class="form-control" name="name" placeholder="Ваше имя" required value="{{_COOK[person_name]}}"></div>
	</div>
	<div class="form-group row">
	  <label class="col-sm-3 control-label">Эл.почта</label>
	   <div class="col-sm-9"><input type="email" class="form-control" name="email" placeholder="Электронная почта" required value="{{_COOK[person_email]}}"></div>
	</div>
	<div class="form-group">
	  <label class="col-sm-3 control-label">Телефон</label>
	   <div class="col-sm-9"><input type="phone" class="form-control" name="phone" placeholder="Контактный телефон" value="{{_COOK[person_phone]}}"></div>
	</div>
	<div class="form-group">
	  <label class="col-sm-3 control-label">Ваш отзыв</label>
		<div class="col-sm-9">
		   <textarea name="text" class="form-control" rows="5" required placeholder="Ваш отзыв"></textarea>
		</div>
	</div>

	<div class="form-group comments-rating">
	  <label class="col-sm-3 control-label">Рейтинг</label>
		<div class="col-sm-9">
		   <input type="hidden" class="rating" name="rating" data-fractions="2" >
		</div>
	</div>

	<div class="form-group" data-wb-allow="admin moder">
        <label class="col-sm-3 form-control-label">Опубликовать</label>
        <div class="col-sm-2"><label class="switch switch-success"><input type="checkbox" name="active"><span></span></label></div>
	</div>
	<div class="form-group"  data-wb-allow="admin moder">
	  <label class="col-sm-3 control-label">Ответ</label>
		<div class="col-sm-9">
		   <textarea name="reply" class="form-control" rows="3" placeholder="Ответ на отзыв"></textarea>
		</div>
	</div>

	<div class="form-group" data-wb-disallow="admin">
		<label class="col-sm-3 control-label">Я не робот</label>
		<div class="col-sm-1 col-md-1 col-lg-1 norobot">
			<input type="checkbox" name="norobot" class="form-control" >
		</div>
		<div class="col-sm-3 sendbutton hidden">
		   <a class="btn btn-primary btn-block" data-formsave="#commentsEditForm">Отправить отзыв</a>
		</div>
	</div>

</form>


    </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> Закрыть</button>
			<button type="button" class="btn btn-primary" data-wb-formsave="#{{_GET[form]}}EditForm"><span class="fa fa-check"></span> Сохранить изменения</button>
		  </div>

		</div>
</div>
</div>











<style>
.comments-rating .rating-symbol {font-size:25px; color:#FFA500;}
</style>
