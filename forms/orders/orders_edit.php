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

                <form id="{{_form}}EditForm" data-wb-form="{{_form}}" data-wb-item="{{_item}}" class="form-horizontal" role="form">
                    <input type="hidden" name="id">
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">{{_LANG[datetime]}}</label>
                        <div class="col-sm-3">
                            <input type="datetimepicker" name="date" class="form-control" placeholder="{{_LANG[datetime]}}">
                        </div>
                    </div>

                    <div class="nav-active-primary">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item"><a class="nav-link active" href="#{{_form}}Descr" data-toggle="tab">{{_LANG[prop]}}</a></li>
                            <li class="nav-item"><a class="nav-link" href="#{{_form}}Delivery" data-toggle="tab">{{_LANG[delivery]}}</a></li>
                            <li class="nav-item"><a class="nav-link" href="#{{_form}}Text" data-toggle="tab">{{_LANG[content]}}</a></li>
                        </ul>
                    </div>
                    <div class="tab-content  p-a m-b-md">
                        <br />
                        <div id="{{_form}}Descr" class="tab-pane fade show active" role="tabpanel">

                            <div class="wb-cart" data-wb-role="cart">

                            <div data-wb-role="multiinput" name="items">
                                <div class="cart-item col">
                                  <div class="row">
				    <input type="hidden" name="id" class="cart-item-id">
				    <input type="hidden" name="form" class="cart-item-form">
                                    <div class="col-12 col-sm-5" role="formdata" data-wb-form="{{form}}" data-wb-item="{{id}}">
                                        <input type="text" class="form-control" value="{{name}}">
                                    </div>
                                    <div class="col-4 col-sm-2">

					<div class="input-group">
					    <style>.wb-cart .btn-sm {line-height:30px;}</style>
					  <div class="input-group-prepend">
					    <a class="btn btn-sm btn-primary cart-item-minus"><i class="fa fa-minus"></i></a>
					  </div>
					  <input type="number" name="quant" min="0" class="text-center form-control cart-item-quant" data-wb-enabled="admin manager">
					  <div class="input-group-append">
					    <a class="btn btn-sm btn-success cart-item-plus"><i class="fa fa-plus"></i></a>
					  </div>
					</div>

                                    </div>
                                    <div class="col-4 col-sm-2">
                                        <input type="number" name="price" min="0" class="form-control cart-item-price" data-wb-enabled="admin manager">
                                    </div>
                                    <div class="col-4 col-sm-3">
                                        <input type="number" class="form-control cart-item-total" disabled>
                                    </div>
                                  </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-9">
                                    <label class="form-control">{{_LANG[total]}}:</label>
                                </div>
                                <div class="col-3">
                                    <input type="number" name="total" readonly class="form-control cart-total">
                                </div>
                            </div>
                            </div>
                        </div>
			<div id="{{_form}}Delivery" class="tab-pane fade" data-wb-role="include" src="form" data-wb-name="orders_details" data-wb-hide="wb" role="tabpanel"></div>
                        <div id="{{_form}}Text" class="tab-pane fade" data-wb-role="include" src="editor" role="tabpanel"></div>
                    </div>
                </form>


            </div>
            <div class="modal-footer" data-wb-role="include" src="form" data-wb-name="common_close_save" data-wb-hide="wb"></div>

        </div>
    </div>
</div>
<script type="text/locale" data-wb-role="inlclude" src="orders_common"></script>
<script>$(document).trigger("wbapp");</script>
