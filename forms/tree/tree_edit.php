<div class="modal fade" id="{{_GET[form]}}_{{_GET[mode]}}" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <!--div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h5 class="modal-title">{{header}}</h5>
      </div-->
            <div class="modal-body">
                <form id="{{_GET[form]}}EditForm" data-wb-form="{{_GET[form]}}" data-wb-item="{{_GET[item]}}" class="form-horizontal" role="form">
                    <div class="row">
                        <div class="col-md-5 col-12">
                            <div class="form-group row">
                                <label class="col-sm-3 form-control-label">Имя записи</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="id" placeholder="Имя записи" required>
                                </div>
                            </div>
                            <div class="tab-content p-a m-b-md">
                                <div class="form-group row">
                                    <label class="col-sm-3 form-control-label">Заголовок</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="header" placeholder="Заголовок">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 form-control-label">Техническое описание</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="techdescr" placeholder="Техническое описание">
                                    </div>
                                </div>
                                <div data-wb-role="tree" name="tree"></div>
                            </div>
                        </div>
                        <div class="col-sm-7 hidden-sm-down tree-edit">
                            <div class="col"> </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item"><a class="nav-link active" href="#treeData" data-toggle="tab">Данные</a></li>
                    <li class="nav-item"><a class="nav-link" href="#treeDict" data-toggle="tab">Словарь</a></li>
                </ul>
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> Закрыть</button>
                <button type="button" class="btn btn-primary" data-wb-formsave="#{{_GET[form]}}EditForm"><span class="fa fa-save"></span> Сохранить изменения</button>
            </div>
        </div>
    </div>
</div>