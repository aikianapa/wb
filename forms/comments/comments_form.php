<form id="commentsEditForm" data-wb-form="comments" data-wb-item="{{_GET[item]}}" class="form-horizontal" role="form">
    <input type="hidden" name="date">
    <input type="hidden" name="user_id" value="{{_SESS[user_id]}}" data-wb-role="where" data-wb-data='"{{_GET[item]}}"="_new"' data-wb-hide="wb">
    <input type="hidden" name="target_form" value="{{_ENV[route][form]}}" data-wb-role="where" data-wb-data='target_form=""' data-wb-hide="wb">
    <input type="hidden" name="target_item" value="{{_ENV[route][item]}}" data-wb-role="where" data-wb-data='target_item=""' data-wb-hide="wb">
    <div role="where" data='"{{_ENV[route][mode]}}"="edit"'>
        <div class="form-group row" data-wb-allow="admin">
            <label class="col-sm-3 form-control-label">Дата</label>
            <div class="col-sm-5">
                <input type="datetimepicker" class="form-control" name="date" placeholder="Дата/время отзыва">
            </div>
        </div>

        <div class="form-group row" data-wb-allow="admin">
            <label class="col-sm-3 form-control-label">Пользователь <i class="fa fa-times-circle float-right" onclick='$("[name=user_id]").val("").empty().trigger("change");'></i></label>
            <div class="col-sm-9">
                <select class="form-control select2" id="uid" role="foreach" data-wb-ajax="/form/select2/users" data-wb-where='active="on"' name="user_id" placeholder="Пользователь">
                    <option value="{{id}}">
                        [ {{id}} ] - {{name}}
                    </option>
                   </select>
            </div>
        </div>
    </div>

    <div role="formdata" data-wb-json="{{_SESS[user]}}" data-wb-hide="*">
        <div class="form-group row">
            <label class="col-sm-3 control-label label-name">Ваше имя</label>
            <div class="col-sm-9"><input type="text" class="form-control" name="name" placeholder="Ваше имя" value="{{name}}" required></div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 control-label label-email">Эл.почта</label>
            <div class="col-sm-9"><input type="email" class="form-control" name="email" placeholder="Электронная почта" required></div>
        </div>

    </div>
    <div class="form-group row">
        <label class="col-sm-3 control-label label-comment">Ваш отзыв</label>
        <div class="col-sm-9">
            <textarea name="text" class="form-control" rows="5" required placeholder="Ваш отзыв"></textarea>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 control-label label-rating">Рейтинг</label>
        <div class="col-sm-9 comments-rating">
            <input type="hidden" class="rating" name="rating" data-fractions="2">
        </div>
    </div>

    <div>
        <div class="form-group row" data-wb-allow="admin moder" data-wb-allow="admin moder" data-wb-role="where" data='"{{_ENV[route][form]}}"="comments" AND "{{_ENV[route][mode]}}"="edit"'>
            <label class="col-sm-3 form-control-label label-publish">Опубликовать</label>
            <div class="col-sm-2"><label class="switch switch-success"><input type="checkbox" name="active"><span></span></label></div>
        </div>
        <div class="form-group row" data-wb-allow="admin moder" data-wb-role="where" data='"{{_ENV[route][form]}}"="comments" AND "{{_ENV[route][mode]}}"="edit"'>
            <label class="col-sm-3 control-label label-reply">Ответ</label>
            <div class="col-sm-9">
                <textarea name="reply" class="form-control" rows="3" placeholder="Ответ на отзыв"></textarea>
            </div>
        </div>
    </div>
    <div class="form-group row" data-wb-role="where" data='"{{_ENV[route][mode]}}"<>"edit"'>
        <label class="col-sm-3 control-label label-norobot">Я не робот</label>
        <div class="col-sm-1 norobot">
            <label class="switch switch-success"><input type="checkbox" name="norobot"><span></span></label>
        </div>
        <div class="col-sm-4">
            <a class="btn btn-default btn-block btn-back" href='javascript:$("a[href=#commentsList]").trigger("click");'>Назад</a>
        </div>

        <div class="col-sm-4 sendbutton hidden">
            <a class="btn btn-primary btn-block btn-send" data-wb-formsave="#commentsEditForm">Отправить</a>
        </div>
    </div>
</form>
