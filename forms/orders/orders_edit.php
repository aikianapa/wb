<div class="modal fade" id="{{_form}}_{{_mode}}" data-keyboard="false" data-backdrop="true" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
                <h5 class="modal-title">{{header}}</h5>
            </div>
            <div class="modal-body">

                <form id="{{_form}}EditForm" data-wb-form="{{_form}}" data-wb-item="{{_GET[item]}}" class="form-horizontal" role="form">
                    <input type="hidden" name="id">
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">Дата</label>
                        <div class="col-sm-3">
                            <input type="datetimepicker" name="date" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                      <div class="col-sm-6">
                        <label class="form-control-label">Клиент</label>
                        <input type="text" name="name" class="form-control">
                      </div>
                      <div class="col-sm-3">
                        <label class="form-control-label">Телефон</label>
                        <input type="phone" name="phone" class="form-control">
                      </div>
                      <div class="col-sm-3">
                        <label class="form-control-label">Эл.почта</label>
                        <input type="email" name="email" class="form-control">
                      </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                        <label class="form-control-label">Комментарии</label>
                          <textarea rows="auto" name="comments" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="nav-active-primary">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item"><a class="nav-link active" href="#{{_form}}Descr" data-toggle="tab">Характеристики</a></li>
                            <li class="nav-item"><a class="nav-link" href="#{{_form}}Text" data-toggle="tab">Контент</a></li>
                        </ul>
                    </div>
                    <div class="tab-content  p-a m-b-md">
                        <br />
                        <div id="{{_form}}Descr" class="tab-pane fade show active" role="tabpanel">
                            <div class="wb-cart" data-wb-role="cart">

                            <div data-wb-role="multiinput" name="items">
                                <div class="cart-item col">
                                  <div class="row">
                                    <input type="hidden" name="item" class="cart-item-id">
                                    <input type="hidden" name="form" class="cart-item-form">
                                    <div class="col-5" role="formdata" data-wb-form="{{form}}" data-wb-item="{{item}}">
                                        <input type="text" class="form-control" value="{{name}}">
                                    </div>
                                    <div class="col-2">
                                        <input type="number" name="count" min="0" class="form-control cart-item-count" data-wb-enabled="admin manager">
                                    </div>
                                    <div class="col-2">
                                        <input type="number" name="price" min="0" class="form-control cart-item-price" data-wb-enabled="admin manager">
                                    </div>
                                    <div class="col-3">
                                        <input type="number" class="form-control cart-item-total" disabled>
                                    </div>
                                  </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-9">
                                    <label class="form-control">ИТОГО:</label>
                                </div>
                                <div class="col-3">
                                    <input type="number" name="total" readonly class="form-control cart-total">
                                </div>
                            </div>
                            </div>
                        </div>

                        <div id="{{_form}}Text" class="tab-pane fade" data-wb-role="include" src="editor" role="tabpanel"></div>
                    </div>
                </form>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="fa fa-close"></span> Закрыть</button>
                <button type="button" class="btn btn-primary" data-wb-formsave="#{{_form}}EditForm"><span class="fa fa-check"></span> Сохранить изменения</button>
            </div>

        </div>
    </div>
</div>
