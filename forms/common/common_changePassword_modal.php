<div class="modal fadeLeft" id="{{_GET[form]}}_{{_GET[mode]}}_pswd" data-keyboard="false" data-backdrop="true" role="dialog" data-wb-allow="admin">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Изменение пароля</h5></div>
        <div class="modal-body">
            <form id="{{_GET[form]}}_{{_GET[mode]}}_pswdForm">
            <div class="form-group row">
              <label class="col-12 form-control-label">Новый пароль</label>
               <div class="col-12"><input type="password" class="form-control" name="newpassword" minlength="3" placeholder="Новый пароль" required></div>
                <label class="col-12 form-control-label">Повторите</label>
                <div class="col-12">
                <input type="password" class="form-control" name="newpassword_check" minlength="3" placeholder="Проверка пароля" required>
                </div>
            </div>
            </form>
        </div>
        <div class="modal-footer">
    <button type="button" class="btn btn-decondary" data-dismiss="modal"><span class="fa fa-close"></span> Отмена</button>
    <button type="button" class="btn btn-primary"><span class="fa fa-arrow-circle-left"></span> Изменить</button>
        </div>
    </div>
    </div>
    <script language="javascript">
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