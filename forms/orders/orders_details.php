<div class="row" id="orderDetails">
	<input type="hidden" name="user_id">
	<div class="col-12 col-sm-6">
		<div class="form-group">
			<div class="col-12">
				<label class="form-control-label">{{_LANG[client]}}</label>
				<input type="text" name="fullname" class="form-control" placeholder="{{_LANG[client]}}" required>
			</div>
			<div class="col-12">
				<label class="form-control-label">{{_LANG[phone]}}</label>
				<input type="phone" name="phone" class="form-control" placeholder="{{_LANG[phone]}}" required>
			</div>
			<div class="col-12">
				<label class="form-control-label">{{_LANG[email]}}</label>
				<input type="email" name="email" class="form-control" placeholder="{{_LANG[email]}}" required>
			</div>

			<div class="col-12">
				<label class="form-control-label">{{_LANG[address]}}</label>
				<input type="text" name="address" class="form-control" placeholder="{{_LANG[address]}}" required>
				<script>
					$("#orderDetails [name=address]").on("keyup",function(){
						$("#orderDetails .yamap_editor .finder").val($(this).val()).trigger("keyup");

					});
				</script>
			</div>

			<div class="col-12">
				<label class="form-control-label">{{_LANG[comments]}}</label>
				<textarea rows="auto" name="comments" class="form-control" placeholder="{{_LANG[comments]}}"></textarea>
			</div>
		</div>
	</div>
	<div class="col-12 col-sm-6">
		<div class="row">
			<div class="col-12 col-sm-12">
				<label class="form-control-label">{{_LANG[address]}}</label>
				<div data-wb-role="module" src="yamap" class="form-control" editable height="300" zoom="16">
				</div>
				<meta data-wb-addclass="hidden" data-wb-selector=".yamap_editor">
			</div>
		</div>
	</div>
</div>
<script type="text/locale" data-wb-role="inlclude" src="orders_common"></script>

