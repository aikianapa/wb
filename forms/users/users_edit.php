<div class="modal fade" id="{{_GET[form]}}_{{_GET[mode]}}" data-keyboard="false" data-backdrop="true" role="dialog"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title">{{_LANG[header]}}</h5></div>
            <div class="modal-body">
                <form id="{{_GET[form]}}EditForm" data-wb-form="{{_GET[form]}}" data-wb-item="{{id}}"
                      class="form-horizontal" role="form" data-wb-allow="admin moder">
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label class="form-control-label">{{_LANG[login]}}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="id" placeholder="{{_LANG[login]}}"
                                       required data-wb-enabled="admin">
                                <input type="hidden" class="form-control" name="password">
                                <div class="input-group-addon btn btn-warning fa fa-key" data-toggle="modal"
                                     data-target="#{{_GET[form]}}_{{_GET[mode]}}_pswd" data-wb-allow="admin"></div>
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
                        <div class="col-sm-4">
                            <label class="form-control-label">{{_LANG[active]}}</label>
                            <div class="col">
                                <label class="switch switch-success">
                                    <input type="checkbox" name="active"><span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="nav-active-primary">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item"><a class="nav-link active" href="#{{_GET[form]}}Descr"
                                                    data-toggle="tab">{{_LANG[properties]}}</a></li>
                            <li class="nav-item" data-wb-allow="admin"><a class="nav-link" href="#{{_GET[form]}}Group"
                                                                          data-toggle="tab">{{_LANG[group]}}</a></li>
                            <li class="nav-item"><a class="nav-link" href="#{{_GET[form]}}Text" data-toggle="tab">{{_LANG[content]}}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content  p-a m-b-md">
                        <br/>
                        <div id="{{_GET[form]}}Descr" class="tab-pane fade show active" role="tabpanel">
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
                        <div id="{{_GET[form]}}Group" class="tab-pane fade" role="tabpanel" data-wb-allow="admin">
                            <div class="form-group row">
                                <label class="col-5 form-control-label">{{_LANG[use_as_group]}}</label>
                                <div class="col-2">
                                    <label class="switch switch-success">
                                        <input type="checkbox" name="isgroup"><span></span></label>
                                </div>
                            </div>
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

                        <div id="{{_GET[form]}}Text" class="tab-pane fade" role="tabpanel">

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
