	 <div class="cart-table">
			<div data-wb-role="foreach" data-wb-from="items" data-wb-where='"{{id}}"!==""' data-wb-item="*">
				<div class="cart-item row" id="{{form}}_{{item}}">
					<div class="col-12 col-sm-2 col-xs-12 text-center" data-wb-role="formdata" data-wb-table="{{form}}" data-wb-item="{{item}}" data-wb-hide="wb">
						<meta data-wb-role="variable" var="prod_name" value="{{name}}" data-wb-hide="*">
						<a href="/{{%form}}/show/{{%id}}">
							<img alt="" data-wb-role="thumbnail" size="100px;100px;src" contain="true" offset="50%;50%" class="img-responsive" src="0">
						</a>
					</div>
					<div class="col-12 col-sm-10 col-xs-12">
						<div class="row">
							<div class="col-12 col-sm-5 col-xs-12">
								<a href="/{{form}}/show/{{item}}">{{_VAR[prod_name]}} </a>
							</div>
							<div class="col-6 col-sm-3 col-xs-6 text-center">
								<input type="hidden" name="id" class="cart-item-id">
								    <input type="hidden" name="form" class="cart-item-form">
								    <input type="text" name="quant" class="cart-item-quant form-control form-control-sm" placeholder="{{quant}}">
									<div class="pull-right">
										<a class="btn btn-sm btn-primary cart-item-minus"><i class="fa fa-minus"></i></a>
										<a class="btn btn-sm btn-success cart-item-plus"><i class="fa fa-plus"></i></a>
										<a class="btn btn-sm btn-danger cart-item-remove"><i class="fa fa-trash"></i></a>
									</div>
							</div>
							<div class="col-3 col-sm-2 col-xs-3 text-center cart-item-price">
								{{price}}
							</div>
							<div class="col-3 col-sm-2 col-xs-3 text-center cart-item-total"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-12 text-right">
				<h5>{{_LANG[total]}}: <span class="cart-total"></span></h5>
			</div>
			<div class="actions">
                <div class="col-12">
				<a href="/" class="btn btn-primary">{{_LANG[continue]}}</a>
                    &nbsp;
				<a href="#" class="btn btn-success" onclick="wbapp.merchantModal('show')">{{_LANG[checkout]}}</a>
                    &nbsp;
				<a href="#" class="btn btn-danger cart-clear">{{_LANG[clear]}}</a>
                </div>
			</div>
	</div>
	<div class="cart-success alert alert-info row" style="display:none;">
		<div class="col-xs-1">
			<p><i class="fa fa-info-circle fa-2x"></i></p>
		</div>
		<div class="col-xs-11">
			<p>{{_LANG[success]}}</p>
		</div>
	</div>


<script type="text/locale" data-wb-role="include" src="cart_common.ini"></script>
