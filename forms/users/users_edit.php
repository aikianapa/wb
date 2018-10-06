<div class="modal fade" id="{{_GET[form]}}_{{_GET[mode]}}" data-keyboard="false" data-backdrop="true" role="dialog"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title">{{header}}</h5></div>
            <div class="modal-body">
                <form id="{{_GET[form]}}EditForm" data-wb-form="{{_GET[form]}}" data-wb-item="{{id}}"
                      class="form-horizontal" role="form" data-wb-allow="admin moder">
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label class="form-control-label">Логин</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="id" placeholder="Логин пользователя"
                                       required data-wb-enabled="admin">
                                <input type="hidden" class="form-control" name="password">
                                <div class="input-group-addon btn btn-warning fa fa-key" data-toggle="modal"
                                     data-target="#{{_GET[form]}}_{{_GET[mode]}}_pswd" data-wb-allow="admin"></div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-control-label">Группа</label>
                            <select class="form-control" placeholder="Без роли" name="role"
                                    data-wb-role="foreach" data-wb-form="users" data-wb-tpl="false"
                                    data-wb-where='isgroup="on"'>
                                <option value="{{id}}">{{id}}</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-control-label">Активный</label>
                            <div class="col">
                                <label class="switch switch-success">
                                    <input type="checkbox" name="active"><span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="nav-active-primary">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item"><a class="nav-link active" href="#{{_GET[form]}}Descr"
                                                    data-toggle="tab">Характеристики</a></li>
                            <li class="nav-item" data-wb-allow="admin"><a class="nav-link" href="#{{_GET[form]}}Group"
                                                                          data-toggle="tab">Группа</a></li>
                            <li class="nav-item"><a class="nav-link" href="#{{_GET[form]}}Text" data-toggle="tab">Контент</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#{{_GET[form]}}Images" data-toggle="tab">Изображения</a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content  p-a m-b-md">
                        <br/>
                        <div id="{{_GET[form]}}Descr" class="tab-pane fade show active" role="tabpanel">
                            <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">Никнейм</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="name" placeholder="Никнейм">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">Имя</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="first_name"
                                               placeholder="Имя пользователя">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">Фамилия</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="last_name" placeholder="Фамилия">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">Эл.почта</label>
                                    <div class="col-sm-8">
                                        <input type="email" class="form-control" name="email"
                                               placeholder="Электронная почта">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <div class="col-auto">
                                        <input type="hidden" name="avatar" data-wb-role="uploader"></div>
                                </div>
                            </div>
                            </div>
                        </div>
                        <div id="{{_GET[form]}}Group" class="tab-pane fade" role="tabpanel" data-wb-allow="admin">
                            <div class="form-group row">
                                <label class="col-5 form-control-label">Использовать как группу</label>
                                <div class="col-2">
                                    <label class="switch switch-success">
                                        <input type="checkbox" name="isgroup"><span></span></label>
                                </div>
                            </div>
                            <div class="form-group row" data-wb-allow="admin">
                                <label class="col-sm-2 form-control-label">Точка входа</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="point" placeholder="Точка входа">
                                </div>
                                <label class="col-12 form-control-label">Свойства группы</label>
                                <div class="col-12">
                                    <div data-wb-role="tree" name="roleprop"></div>
                                </div>
                            </div>
                        </div>

                        <div id="{{_GET[form]}}Text" class="tab-pane fade" data-wb-role="include" src="editor"
                             role="tabpanel"></div>
                        <div id="{{_GET[form]}}Images" class="tab-pane fade" data-wb-role="include" src="uploader"
                             role="tabpanel"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span
                            class="glyphicon glyphicon-remove"></span> Закрыть
                </button>
                <button type="button" class="btn btn-primary" data-wb-formsave="#{{_GET[form]}}EditForm"><span
                            class="glyphicon glyphicon-ok"></span> Сохранить изменения
                </button>
            </div>
        </div>
    </div>
</div>
</div>
<div data-wb-role="include" src="form" data-wb-name="common_changePassword_modal" data-wb-hide="*"></div>
