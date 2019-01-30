<h5 class="element-header">
        {{_LANG[settings]}}
        <a type="button" href="#" class="btn btn-primary pull-right" data-wb-formsave="#admin_settings"><i class="fa fa-save"></i> &nbsp; {{_LANG[btn_save]}}</a>
</h5>
<form method="post" id="admin_settings" data-wb-form="admin" data-wb-item="settings" data-wb-allow="admin" class="row">
    <div class="nav-active-primary col-xs-row col-md-2">
        <ul class="nav nav-pills flex-md-column flex-xs-row" role="tablist">
                <li class="d-none d-md-inline-block"><label class="content-left-label">{{_LANG[settings]}}</label></li>
                <li class="nav-item"><a class="nav-link active" href="#adminMain" data-toggle="tab">{{_LANG[tab_main]}}</a></li>
                <li class="nav-item"><a class="nav-link" href="#adminCache" data-toggle="tab">{{_LANG[tab_cache]}}</a></li>
                <li class="nav-item"><a class="nav-link" href="#adminAdd" data-toggle="tab">{{_LANG[tab_appends]}}</a></li>
                <li class="nav-item"><a class="nav-link" href="#adminTree" data-toggle="tab">{{_LANG[tab_prop]}}</a></li>
                <li class="d-none d-md-inline-block"><label class="content-left-label">{{_LANG[utilites]}}</label></li>
                <li class="nav-item"><a class="nav-link" data-wb-ajax="/module/backup" data-wb-html=".content-box">{{_LANG[tab_backup]}}</a></li>
                <li class="nav-item"><a class="nav-link" data-wb-ajax="/module/update" data-wb-html=".content-box">{{_LANG[tab_update]}}</a></li>
                <li class="nav-item"><a class="nav-link" data-wb-ajax="/module/filemanager" data-wb-html=".content-box">{{_LANG[tab_fileman]}}</a></li>
                <li class="nav-item"><a class="nav-link" data-wb-ajax="/module/shortcodes" data-wb-html=".content-box">{{_LANG[tab_shortcode]}}</a></li>
                <li class="nav-item"><a class="nav-link" data-wb-ajax="/form/list/users" data-wb-html=".content-box">{{_LANG[tab_users]}}</a></li>
                <li class="nav-item"><a class="nav-link" data-wb-ajax="/form/list/tree" data-wb-html=".content-box">{{_LANG[tab_tree]}}</a></li>
        </ul>
    </div>
    <div class="tab-content pd-y-20 col-xs-row col-md-10">
        <div id="adminMain" class="tab-pane active" role="tabpanel">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group row">
                        <label class="col-sm-3">{{_LANG[header]}}</label>
                        <input class="col-sm-9 form-control" placeholder="{{_LANG[header]}}" type="text" name="header" required> </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group row">
                        <label class="col-sm-3">{{_LANG[email]}}</label>
                        <input class="col-sm-9 form-control" placeholder="{{_LANG[email]}}" type="text" name="email" required> </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group row">
                        <label class="col-sm-3">{{_LANG[lang]}}</label>
                            <select class="col-sm-3 col-6 form-control" name="lang" data-wb-role="foreach" data-wb-call="wbListLocales" value="{{lang}}" data-wb-hide="wb">
                                <option value="{{id}}">{{id}} [{{_locale}}]</option>
                            </select>

                        </div>
                </div>
                <div class="col-sm-12">
                    <div class="row form-group">
                        <label class="col-sm-3 col-12">{{_LANG[merchant]}}</label>
                            <select class="col-sm-3 col-6 form-control" name="merchant" data-wb-role="foreach" data-wb-from="merchants" value="{{merchant}}" data-wb-hide="wb" placeholder="{{_LANG[merchant]}}">
                                <option value="{{name}}">{{name}} [{{type}}]</option>
                            </select>


                        <div class="col-sm-3 col-6">
                                <a href="" tabindex data-wb-ajax="/module/{{merchant}}/settings" data-wb-html="#adminMain .merchant-settings" class="btn btn-secondary form-control">
                                    <i class="fa fa-gear"></i> {{_LANG[settings]}}
                                </a>
                            <div class="merchant-settings"></div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12">
                    <div class="form-group row">
                        <label class="col-sm-3">{{_LANG[path_tpl]}}</label>
                        <input class="col-sm-9 form-control" placeholder="{{_LANG[dirTpl]}}" type="text" name="path_tpl" required> </div>
                </div>

                <div class="col-sm-12">
                    <div class="row form-group">
                        <label class="col-sm-3 col-12">{{_LANG[ulogin]}}</label>
                        <div class="col-sm-3 col-6">
                                <div class="row">
                                <label class="switch switch-sm switch-success">
				<input type="checkbox" name="ulogin" value="">
				<span></span>
                                </div>
                        </div>

                        <!--div class="col-sm-3 col-6">
                                <a href="" tabindex data-wb-ajax="/module/ulogin/settings" data-wb-html="#adminMain .ulogin-settings" class="btn btn-secondary form-control">
                                    <i class="fa fa-gear"></i> {{_LANG[settings]}}
                                </a>
                            <div class="ulogin-settings"></div>
                        </div-->
                    </div>
                </div>

                <div class="col-sm-12">
                        <div class="row form-group">
                                <label class="col-sm-3 col-6">PHPmailer</label>
                                <label class="switch switch-sm switch-success">
				<input type="checkbox" name="phpmailer" value="">
				<span></span>
			</label>
                        </div>
                </div>

                <div class="col-sm-12">
                        <div class="row form-group">
                                <label class="col-sm-3 col-6">{{_LANG[error_log]}}</label>
                                <label class="switch switch-sm switch-success">
				<input type="checkbox" name="log" value="">
				<span></span>
			</label>
                        </div>
                </div>

                <div class="col-sm-12">
                        <div class="row form-group">
                                <label class="col-sm-3 col-6">{{_LANG[beautifyHtml]}}</label>
                                <label class="switch switch-sm switch-success">
				<input type="checkbox" name="beautifyHtml" value="">
				<span></span>
			</label>
                        </div>
                </div>
                <div class="col-sm-12" data-wb-role="multiinput" name="variables">
                    <div class="col-sm-3 col-xs-12">
                        <input class="form-control" placeholder="{{_LANG[variable]}}" type="text" name="var"> </div>
                    <div class="col-sm-4 col-xs-12">
                        <input class="form-control" placeholder="{{_LANG[value]}}" type="text" name="value"> </div>
                    <div class="col-sm-5 col-xs-12">
                        <input class="form-control" placeholder="{{_LANG[description]}}" type="text" name="header"> </div>
                </div>
            </div>
        </div>
        <div id="adminCache" class="tab-pane" role="tabpanel" data-wb-role="include" src="form" data-wb-name="admin_cache">

        </div>
        <div id="adminAdd" class="tab-pane" role="tabpanel">
            <div class="row">
                <div class="col-xl-6">
                    <div class="row">
                        <div class="col-6"><h5>{{_LANG[head_inc]}}</h5></div>
                        <div class="col-6"><label class="switch switch-success"><input type="checkbox" name="head_add_active"><span></span></label></div>
                    </div>
                    <div data-wb-role="module" data-wb-name="head_add" data-wb-toolbar="false" data-wb-height="450" src="editarea" role="tabpanel"></div>
                </div>
                <div class="col-xl-6">
                    <div class="row">
                        <div class="col-6"><h5>{{_LANG[body_inc]}}</h5></div>
                        <div class="col-6"><label class="switch switch-success"><input type="checkbox" name="body_add_active"><span></span></label></div>
                    </div>
                    <div data-wb-role="module" data-wb-name="body_add" data-wb-toolbar="false" data-wb-height="450" src="editarea" role="tabpanel"></div>
                </div>
            </div>
        </div>

        <div id="adminTree" class="tab-pane fade" role="tabpanel">
            <div class="row">
                <div class="col-sm-12">
                    <div data-wb-role="tree" name="tree"></div>
                </div>
            </div>
        </div>
        <div id="adminUpdate" class="tab-pane fade" role="tabpanel" data-wb-role="include" src="form" data-wb-name="admin_update" data-wb-hide="wb">
        </div>
        <div id="adminBackups" class="tab-pane fade" role="tabpanel" data-wb-role="include" src="module" data-wb-name="backup" data-wb-hide="wb">
        </div>
    </div>
</form>

<script src="/engine/forms/admin/admin.js"></script>
<script type="text/locale" data-wb-role="include" src="admin_common"></script>
