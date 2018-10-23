<div class="modal fadeLeft" id="{{_GET[form]}}_{{_GET[mode]}}_pswd" data-keyboard="false" data-backdrop="true" data-show="false" role="dialog" data-wb-allow="admin">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">{{_LANG[title]}}</h5></div>
        <div class="modal-body">
            <form id="{{_GET[form]}}_{{_GET[mode]}}_pswdForm">
                <div class="form-group">
                        <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                <input type="password" class="form-control" name="newpassword" minlength="3" placeholder="{{_LANG[newpass]}}" required>
                        </div>
                </div>
                <div class="form-group">
                        <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-unlock-alt"></i></span>
                                <input type="password" class="form-control" name="newpassword_check" minlength="3" placeholder="{{_LANG[checkpass]}}" required>
                        </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
    <button type="button" class="btn btn-decondary" data-dismiss="modal"><span class="fa fa-close"></span> {{_LANG[cancel]}}</button>
    <button type="button" class="btn btn-primary"><span class="fa fa-arrow-circle-left"></span> {{_LANG[change]}}</button>
        </div>
    </div>
    </div>
    <script>
        var parent=$("#{{_GET[form]}}_{{_GET[mode]}}");
        var modal=$("#{{_GET[form]}}_{{_GET[mode]}}_pswd");
        var form=$("#{{_GET[form]}}_{{_GET[mode]}}_pswdForm");
        $(modal).find(".btn-primary").off("click");
        $(modal).find(".btn-primary").on("click",function(){
                if (wb_check_required(form)) {
                   $(parent).find("input[name=password]").val(md5($(form).find("input[name=newpassword]").val()));
                    $(form).find("input").val("");
                    $(modal).modal('hide');
                }
        });
    </script>
</div>

<script type="text/locale">
[eng]
title           = "Change password"
newpass         = "New password"
checkpass       = "New password (check)"
change          = "Change"
cancel          = "Cancel"
[rus]
title           = "Изменение пароля"
newpass         = "Новый пароль"
checkpass       = "Новый пароль (повторите)"
change          = "Изменить"
cancel          = "Отмена"
</script>
