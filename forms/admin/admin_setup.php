<div id="setup" class="wbengine">
	<h6 class="card-body-title"><h1 class="wblogo"><i class="text-dark">Web</i><i class="text-primary">Basic</i></h1></h6>
        <p class="mg-b-20 mg-sm-b-30">{{_LANG[slogan]}}</p>
        <form id="setup" method="post">
		<input type="hidden" name="setup" value="done" />
		<div id="wizard">
                <h3>{{_LANG[step1]}}</h3>
		<section>
		    <div class="row success">
			<div class="col-sm-12">
			    <div class="form-group">
				<label for="">{{_LANG[header]}}</label>
				<input class="form-control" placeholder="{{_LANG[header]}}" type="text" name="header" required> </div>
			</div>
			<div class="col-sm-6">
			    <div class="form-group">
				<label for="">{{_LANG[email]}}</label>
				<input class="form-control" placeholder="{{_LANG[email]}}" type="email" name="email" required> </div>
			</div>
		    </div>
		</section>
                                <h3>{{_LANG[step2]}}</h3>
                                <section>
                                    <div class="form-group">
                                        <label for="">{{_LANG[login]}}</label>
                                        <input class="form-control" placeholder="{{_LANG[login]}}" type="text" value="admin" name="login" required> </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="">{{_LANG[password]}}</label>
                                                <input class="form-control" placeholder="{{_LANG[password]}}" type="password" name="password" required> </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="">{{_LANG[chkpass]}}</label>
                                                <input class="form-control" placeholder="{{_LANG[chkpass]}}" type="password" name="password_check" required> </div>
                                        </div>
                                    </div>
                                </section>
                                <h3>{{_LANG[step3]}}</h3>
                                <section>
                                    <div class="form-group">
                                        <label>Demo template</label>
                                        <select class="form-control" placeholder="Base template" required>
                                            <option value=""> Select Base template </option>
                                            <option value="single"> Online Store Demo </option>
                                        </select>
                                    </div>
                                </section>
                                <h3>{{_LANG[step4]}}</h3>
                                <section>
                                    <p>{{_LANG[msg_ready]}}</p>
                                </section>
                            </div>
                        </form>
                    </div>
                    <div id="error" class="alert alert-warning d-none">
                        <h4 class="text-secondary">Installation Error!</h4>
                        <p>
                        Work directory <i>{{_ENV[path_app]}}</i> include files of previous installation.
                        </p>
                        <p>
                            For new installation, please, remove all files and directories except <i>/engine</i> and try again.
                        </p>
                        <a class="btn btn-warning" href="/engine">Ready</a>
                    </div>
                    <div id="errors" class="d-none">
                        <div id="rights">
                                        <p><h4>Installation Error!</h4>
                                        Dirrectory <i>{{_ENV[path_app]}}</i> do not have access rights.
                                        Please, set access rights to 766 and try again.</p>
                                </div>
                        </div>

        <div class="wizard-tpl hidden">
                <span class=number >#index#</span> <span class=title >#title#</span>
                <meta name="btn_prev" value="{{_LANG[btn_prev]}}">
		<meta name="btn_next" value="{{_LANG[btn_next]}}">
		<meta name="btn_install" value="{{_LANG[btn_install]}}">
        </div>
<script type="text/locale" data-wb-role="include" src="admin_common"></script>
