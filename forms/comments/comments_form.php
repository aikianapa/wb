<form id="commentsEditForm" data-wb-form="comments" data-wb-item="{{_item}}" class="form-horizontal" role="form">
    <input type="hidden" name="date">
    <input type="hidden" name="user_id" value="{{_SESS[user_id]}}" data-wb-where='"{{_GET[item]}}"="_new"' data-wb-hide="wb">
    <input type="hidden" name="target_form" value="{{_ENV[route][form]}}" data-wb-where='"{{target_form->strlen()}}" = 0' data-wb-hide="wb">
    <input type="hidden" name="target_item" value="{{_ENV[route][item]}}" data-wb-where='"{{target_item->strlen()}}" = 0' data-wb-hide="wb">
    <div data-wb-where='"{{_ENV[route][mode]}}"="edit"'>
        <div class="form-group row" data-wb-allow="admin">
            <label class="col-sm-3 form-control-label">{{_LANG[datetime]}}</label>
            <div class="col-sm-5">
                <input type="datetimepicker" class="form-control" name="date" placeholder="{{_LANG[datetime]}}">
            </div>
        </div>

        <div class="form-group row" data-wb-allow="admin">
            <label class="col-sm-3 form-control-label">{{_LANG[user]}} <i class="fa fa-times-circle fa-2x float-right" onclick='$("[name=user_id]").val("").empty().trigger("change");return false;'></i></label>
            <div class="col-sm-9">
                <select class="form-control select2" id="uid" role="foreach" data-wb-ajax="/form/select2/users" data-wb-where='active="on"' name="user_id" placeholder="{{_LANG[user]}}">
                    <option value="{{id}}">
                        [ {{id}} ] - {{nickname}}
                    </option>
                   </select>
            </div>
        </div>
    </div>

        <meta data-wb-role="variable" var="u_name" value="{{_SESS.user.first_name}}" else="{{name}}"
		data-wb-if='"{{_mode}}" !== "edit" AND "{{_SESS.user.first_name}}" !== ""' >
        <meta data-wb-role="variable" var="u_email" value="{{_SESS.user.email}}" else="{{email}}"
		data-wb-if='"{{_mode}}" !== "edit" AND "{{_SESS.user.email}}" > ""' data-wb-hide="*">

        <div class="form-group row">
            <label class="col-sm-3 control-label label-name">{{_LANG[u_name]}}</label>
            <div class="col-sm-9"><input type="text" class="form-control" name="name" value="{{_VAR[u_name]}}" placeholder="{{_LANG[u_name]}}" required></div>
        </div>
        <div class="form-group row">
            <label class="col-sm-3 control-label label-email">{{_LANG[u_email]}}</label>
            <div class="col-sm-9"><input type="email" class="form-control" name="email" value="{{_VAR[u_email]}}" placeholder="{{_LANG[u_email]}}" required></div>
        </div>
        <div class="form-group row" data-wb-where='"{{_ENV[route][mode]}}" == "edit"'>
            <label class="col-sm-3 control-label label-ip">IP</label>
            <div class="col-sm-9"><input type="email" name="ip" class="form-control" value="{{ip}}" placeholder="IP" readonly disabled></div>
        </div>


    <div class="form-group row">
        <label class="col-sm-3 control-label label-comment">{{_LANG[u_comment]}}</label>
        <div class="col-sm-9">
            <textarea name="text" class="form-control" rows="auto" required placeholder="{{_LANG[u_comment]}}"></textarea>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 control-label label-rating">{{_LANG[rating]}}</label>
        <div class="col-sm-9 comments-rating">
            <input type="hidden" class="rating" name="rating" data-fractions="2">
        </div>
    </div>

    <div>
        <div class="form-group row" data-wb-allow="admin moder" data-wb-allow="admin moder" data-wb-role="where" data='"{{_form}}"="comments" AND "{{_mode}}"="edit"'>
            <label class="col-sm-3 form-control-label label-publish">{{_LANG[publish]}}</label>
            <div class="col-sm-2"><label class="switch switch-success"><input type="checkbox" name="active"><span></span></label></div>
        </div>
        <div class="form-group row" data-wb-allow="admin moder" data-wb-role="where" data='"{{_form}}"="comments" AND "{{_mode}}"="edit"'>
            <label class="col-sm-3 control-label label-reply">{{_LANG[reply]}}</label>
            <div class="col-sm-9">
                <textarea name="reply" class="form-control" rows="auto" placeholder="{{_LANG[reply]}}"></textarea>
            </div>
        </div>
    </div>
    <div class="form-group row" data-wb-role="where" data='"{{_mode}}"<>"edit"'>
        <label class="col-sm-3 control-label label-norobot">{{_LANG[norobot]}}</label>
        <div class="col-sm-1 norobot">
            <label class="switch switch-success"><input type="checkbox" name="norobot"><span></span></label>
        </div>

        <div class="col-sm-4 sendbutton hidden">
            <a class="btn btn-primary btn-block btn-send" data-wb-formsave="#commentsEditForm">{{_LANG[send]}}</a>
        </div>
    </div>
</form>
<script type="text/locale" data-wb-role="include" src="comments_common"></script>
