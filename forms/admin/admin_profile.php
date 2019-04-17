<h5 class="element-header">
        <i class="fa fa-user"></i> {{_LANG[profile]}}
        <a type="button" href="#" class="btn btn-primary pull-right" data-wb-formsave="#admin_profile"><i class="fa fa-save"></i> &nbsp; {{_LANG[btn_save]}}</a>
</h5>
        <form data-wb-role="formdata" class="row" id="admin_profile" data-wb-form="users" data-wb-item="{{_SESS[user_id]}}" data-parsley-validate>
	<div data-wb-where='"{{id}}"="{{_SESS[user_id]}}"' data-wb-hide="*">
          <div class="col-md-4 col-lg-3">
            <label class="content-left-label">{{_LANG[your_photo]}}</label>
            <figure class="edit-profile-photo">
              <input type="hidden" name="avatar" data-wb-role="uploader" value="{{avatar}}" width="300" height="300" data-wb-size="contain" data-wb-form="users" data-wb-item="{{_SESS[user_id]}}">
              <!--figcaption>
                <a href class="btn btn-dark">Edit Photo</a>
              </figcaption-->
            </figure>

            <label class="content-left-label mg-t-30">{{_LANG[your_tags]}}</label>
            <input type="text" class="input-tags" name="tags" placeholder="tags">
          </div>
          <div class="col-md-8 col-lg-9 mg-t-30 mg-md-t-0">
            <label class="content-left-label">{{_LANG[login_info]}}</label>
            <div class="card bg-gray-200 bd-0">
              <div class="edit-profile-form">
                <div class="form-group row">
                  <label class="col-sm-3 form-control-label">{{_LANG[login]}}:  <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 col-xl-6 mg-t-10 mg-sm-t-0">
                    <input class="form-control" placeholder="{{_LANG[login]}}" name="id" type="text"  readonly required>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-3 form-control-label">{{_LANG[password]}}:  <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                        <input type="hidden" class="form-control" name="password">
                        <a href="" data-toggle="modal" data-target="#{{_form}}_{{_mode}}_pswd" class="btn btn-default"><i class="fa fa-key"></i> {{_LANG[change_pass]}}</a>
                  </div>
                </div>

                <div class="form-group row mg-b-0">
                  <label class="col-sm-3 form-control-label">{{_LANG[lang]}}:  <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 col-xl-6 mg-t-10 mg-sm-t-0">
			<select class="form-control" name="lang" data-wb-role="foreach" data-wb-call="wbListLocales" value="{{lang}}" data-wb-hide="wb">
			<option value="{{id}}">{{id}} [{{_locale}}]</option>
			</select>

                  </div>
                </div>

              </div>
            </div>

            <hr class="invisible">

            <label class="content-left-label">{{_LANG[person_info]}}</label>
            <div class="card bg-gray-200 bd-0">
              <div class="edit-profile-form">
                <div class="form-group row">
                  <label class="col-sm-3 form-control-label">{{_LANG[firstname]}}:</label>
                  <div class="col-sm-8 col-xl-6 mg-t-10 mg-sm-t-0">
                    <input class="form-control" placeholder="{{_LANG[firstname]}}" type="text" name="first_name">
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-3 form-control-label">{{_LANG[lastname]}}:</label>
                  <div class="col-sm-8 col-xl-6 mg-t-10 mg-sm-t-0">
                    <input class="form-control" placeholder="{{_LANG[lastname]}}" type="text" name="last_name">
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-3 form-control-label">{{_LANG[nickname]}}:</label>
                  <div class="col-sm-8 col-xl-6 mg-t-10 mg-sm-t-0">
                    <input class="form-control" placeholder="{{_LANG[nickname]}}" type="text" name="nickname">
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-3 form-control-label">{{_LANG[email]}}: <span class="tx-danger">*</span></label>
                  <div class="col-sm-8 col-xl-6 mg-t-10 mg-sm-t-0">
                    <input class="form-control" placeholder="{{_LANG[email]}}" type="email" name="email" required>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-3 form-control-label">{{_LANG[phone]}}:</label>
                  <div class="col-sm-8 col-xl-6 mg-t-10 mg-sm-t-0">
                    <input class="form-control" placeholder="{{_LANG[phone]}}" type="mask" data-wb-mask="+9 (999) 999-99-99" name="phone">
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-sm-3 form-control-label">{{_LANG[location]}}:</label>
                  <div class="col-sm-8 col-xl-6 mg-t-10 mg-sm-t-0">
                    <input class="form-control" placeholder="{{_LANG[location]}}" type="text" name="location">
                  </div>
                </div>
                <div class="form-group row mg-b-0">
                  <label class="col-sm-3 form-control-label">{{_LANG[about]}}:</label>
                  <div class="col-sm-9 col-xl-8 mg-t-10 mg-sm-t-0">
                    <textarea class="form-control" placeholder="{{_LANG[about1]}}" rows="auto" name="text"></textarea>
                  </div>
                </div>
              </div>
            </div>


          </div>
          </div>
        </form>

<div data-wb-role="include" src="form" data-wb-name="common_changePassword_modal" data-wb-hide="*"></div>

<script type="text/locale" !data-wb-role="include" !src="form" data-wb-name="admin_profile">
[eng]
profile		= "Profile"
your_photo	= "Your Profile Photo"
your_tags	= "Your Tags"
login_info	= "Login Information"
person_info	= "Personal Information"
btn_save	= "Save"
login           = "Login"
password        = "Password"
firstname       = "First Name"
lastname        = "Last Name"
nickname        = "Nickname"
email           = "Email"
location	= "Location"
portfolio	= "Portfolio URL"
about		= "About You"
about1		= "Enter some description of yourself"
lang		= "Language"
phone           = "Phone"
change_pass     = "Change Password"

[rus]
profile		= "Профиль"
your_photo	= "Ваше фото"
your_tags	= "Ваши тэги"
login_info	= "Учётная информация"
person_info	= "Персональные данные"
btn_save	= "Сохранить"
login           = "Логин"
password        = "Пароль"
firstname       = "Имя"
lastname        = "Фамилия"
nickname        = "Никнейм"
email           = "Эл.почта"
location	= "Местонахождение"
portfolio	= "URL вашего портфолио"
about		= "О себе"
about1		= "Напишите немного о себе"
lang		= "Язык"
phone           = "Телефон"
change_pass     = "Сменить пароль"
</script>
