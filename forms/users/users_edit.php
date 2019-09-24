<div class="modal fade" id="{{_form}}_{{_mode}}" data-keyboard="false" data-backdrop="true" role="dialog"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title">
			{{_LANG[header]}}
		</h5></div>
            <div class="modal-body">
                <form id="{{_form}}EditForm" data-wb-form="{{_form}}" data-wb-item="{{_item}}"
                      class="form-horizontal" role="form" data-wb-allow="admin moder">
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label class="form-control-label">{{_LANG[login]}} <span class="text-danger" data-wb-where='super == "on"'>[superuser]</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="id" placeholder="{{_LANG[login]}}"
                                       required data-wb-enabled="admin">
                                <input type="hidden" class="form-control" name="password">
                                <div class="input-group-addon btn btn-warning fa fa-key" data-toggle="modal"
                                     data-target="#{{_form}}_{{_mode}}_pswd" data-wb-allow="admin"></div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-control-label">{{_LANG[group]}}</label>
                            <select class="form-control" placeholder="" name="role"
                                    data-wb-role="foreach" data-wb-form="users" data-wb-tpl="false"
                                    data-wb-where='isgroup="on"'>
                                <option value="{{id}}">{{id}}</option>
                            </select>
                        </div>
                    </div>
                <div class="form-group row">
                            <label class="form-control-label col-sm-3">{{_LANG[active]}}</label>
                            <div class="col-sm-3">
                                <label class="switch switch-success">
                                    <input type="checkbox" name="active"><span></span></label>
                            </div>
                            <label data-wb-where='super !== "on"' class="form-control-label col-sm-3">{{_LANG[use_as_group]}}</label>
                            <div class="col-sm-3">
                                <label class="switch switch-success">
                                    <input type="checkbox" name="isgroup"><span></span></label>
                            </div>
                            <input type="hidden" value="on" name="isgroup" data-wb-where='super == "on"'>

		</div>

                    <div class="nav-active-primary">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item"><a class="nav-link active" href="#{{_form}}Descr"
                                                    data-toggle="tab">{{_LANG[properties]}}</a></li>
                            <li class="nav-item" data-wb-allow="admin"><a class="nav-link" href="#{{_form}}Group"
                                                                          data-toggle="tab">{{_LANG[group]}}</a></li>
                            <li class="nav-item"><a class="nav-link" href="#{{_form}}Text" data-toggle="tab">{{_LANG[content]}}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content  p-a m-b-md">
                        <br/>
                        <div id="{{_form}}Descr" class="tab-pane fade show active" role="tabpanel">
                            <div class="row">
                            <div class="col-sm-7">
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">{{_LANG[firstname]}}</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="first_name" placeholder="{{_LANG[firstname]}}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">{{_LANG[lastname]}}</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="last_name" placeholder="{{_LANG[lastname]}}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">{{_LANG[email]}}</label>
                                    <div class="col-sm-8">
                                        <input type="email" class="form-control" name="email" placeholder="{{_LANG[email]}}" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 form-control-label">{{_LANG[nickname]}}</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="nickname" placeholder="{{_LANG[nickname]}}">
                                    </div>
                                </div>

                        <div class="form-group row">
                                <label class="col-sm-4 form-control-label">{{_LANG[lang]}}</label>
                                <div class="col-sm-8">
                                        <select class="form-control" name="lang" data-wb-role="foreach" data-wb-call="wbListLocales" value="{{lang}}" data-wb-hide="wb">
                                                <option value="{{id}}">{{id}} [{{_locale}}]</option>
                                        </select>
                                </div>

                        </div>

                            </div>
                            <div class="col-sm-5">
                                <div class="form-group row">
                                    <div class="col">
                                        <input type="hidden" name="avatar" data-wb-role="uploader"></div>
                                </div>
                            </div>
                            </div>
                        </div>
                        <div id="{{_form}}Group" class="tab-pane fade" role="tabpanel" data-wb-allow="admin">
                            <div class="form-group row" data-wb-allow="admin">
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

                        <div id="{{_form}}Text" class="tab-pane fade" role="tabpanel">

                                <div class="form-group row">
                                  <label class="col-12 form-control-label">{{_LANG[about]}}:</label>
                                  <div class="col-12">
                                    <textarea class="form-control" placeholder="{{_LANG[about]}}" rows="auto" name="text"></textarea>
                                  </div>
                                </div>

                        </div>
                    </div>
            </form>
            </div>
            <div class="modal-footer" data-wb-role="include" src="form" data-wb-name="common_close_save" data-wb-hide="wb">
            </div>
        </div>
    </div>
</div>
</div>
<div data-wb-role="include" src="form" data-wb-name="common_changePassword_modal" data-wb-hide="*"></div>

<script type="text/locale" data-wb-role="include" src="users_common"></script>
<script>
	$("#{{_form}}_{{_mode}} [name=isgroup]").on("change",function(){
		if ( $("#{{_form}}_{{_mode}} [name=isgroup]").prop("checked") == true ) {
                $("#{{_form}}Group").addClass("show active");
				$("[href='#{{_form}}Group']").show().addClass("active");
				$("#{{_form}}Descr").removeClass("show active");
				$("[href='#{{_form}}Descr']").removeClass("active"); 
		} else {
                $(this).parents("form").find(".tab-pane").removeClass("show active");
            $("#{{_form}}Group").parents("form").find(".nav-tabs").find(".nav-link").removeClass("show active");
				$("[href='#{{_form}}Group']").hide().removeClass("active");
				$("#{{_form}}Descr").addClass("show active");
				$("[href='#{{_form}}Descr']").show().addClass("active");         
        }
	});

	$("#{{_form}}_{{_mode}} [name=isgroup]").trigger("change");
</script>
