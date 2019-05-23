<div class="element-wrapper" data-wb-where='"{{_SESS.user.isgroup}}"="on"' data-wb-hide="wb">
    <h6 class="element-header">
     {{_LANG[group]}}
     <button class="btn btn-sm btn-success pull-right" data-wb-append="body" data-wb-formsave="#{{_form}}Group">
       <i class="fa fa-save"></i> {{_LANG[save]}}
     </button>
    </h6>
	<form id="{{_form}}Group" data-wb-role="formdata" data-wb-form="users" data-wb-item="{{_SESS.user_id}}" data-wb-hide="data-wb-role">
    <div class="element-box row">

		<div class="form-group row">
			<div class="col-sm-6">
					<label class="form-control-label">{{_LANG[login_url]}}</label>
					<input type="text" class="form-control" name="login_url" placeholder="{{_LANG[login_url]}}">
			</div>
			<div class="col-sm-6">
					<label class="form-control-label">{{_LANG[logout_url]}}</label>
					<input type="text" class="form-control" name="logout_url" placeholder="{{_LANG[logout_url]}}">
			</div>
			<label class="col-12 form-control-label">{{_LANG[group_prop]}}</label>
			<div class="col-12">
				<div data-wb-role="tree" name="roleprop"></div>
			</div>
		</div>
	</div>
	</form>
</div>
<script type="text/locale" data-wb-role="include" src="users_common"></script>
