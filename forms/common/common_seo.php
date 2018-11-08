<div class="form-group row">
    <label class="col-sm-3 form-control-label">{{_LANG[title]}}</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="title" placeholder="{{_LANG[title]}}"> </div>
</div>
<div class="form-group row">
    <label class="col-sm-3 form-control-label">{{_LANG[descr]}}</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="meta_description" placeholder="{{_LANG[descr]}}"> </div>
</div>
<div class="form-group row">
    <label class="col-sm-3 form-control-label">{{_LANG[keywords]}}</label>
    <div class="col-sm-9">
        <input type="text" class="form-control input-tags" name="meta_keywords" placeholder="{{_LANG[keywords]}}"> </div>
</div>

<ul class="nav nav-tabs" role="tablist">
	<li class="nav-item"><a class="nav-link active" href="#{{_form}}SeoHead" data-toggle="tab">{{_LANG[head_inc]}}</a></li>
	<li class="nav-item"><a class="nav-link" href="#{{_form}}SeoBody" data-toggle="tab" >{{_LANG[body_inc]}}</a></li>
</ul>

<div class="tab-content  p-a m-b-md">
<br />
<div id="{{_form}}SeoHead" class="tab-pane fade show active" role="tabpanel">
  <div class="form-group row">
      <div class="col-sm-3">
          <h6 class="form-control-label">{{_LANG[head_inc]}}</h6>
          <label class="col-12 form-control-label">{{_LANG[local_on]}}</label>
          <div class="col-sm-2">
              <label class="switch switch-success">
                  <input type="checkbox" name="head_add_active"><span></span></label>
          </div>
          <label class="col-12 form-control-label">{{_LANG[glob_off]}}</label>
          <div class="col-sm-2">
              <label class="switch switch-success">
                  <input type="checkbox" name="head_noadd_glob"><span></span></label>
          </div>
      </div>
      <div class="col-sm-9">
                <div data-wb-role="module" src="editarea" data-wb-name="head_add" data-wb-id=""></div>
        </div>
  </div>
</div>
<div id="{{_form}}SeoBody" class="tab-pane fade show" role="tabpanel">

  <div class="form-group row">
      <div class="col-sm-3">
          <h6 class="form-control-label">{{_LANG[body_inc]}}</h6>
          <label class="col-12 form-control-label">{{_LANG[local_on]}}</label>
          <div class="col-sm-2">
              <label class="switch switch-success">
                  <input type="checkbox" name="body_add_active"><span></span></label>
          </div>
          <label class="col-12 form-control-label">{{_LANG[glob_off]}}</label>
          <div class="col-sm-2">
              <label class="switch switch-success">
                  <input type="checkbox" name="body_noadd_glob"><span></span></label>
          </div>
      </div>
      <div class="col-sm-9">
                <div data-wb-role="module" src="editarea" data-wb-name="body_add" data-wb-id=""></div>
      </div>
  </div>
</div>
</div>

<script type="text/locale">
[eng]
title           = "Title"
descr           = "META Description"
keywords        = "META Keywords"
head_inc        = "Append to HEAD"
body_inc        = "Append to BODY"
local_on        = "Turn On local append"
glob_off        = "Turn Off global append"
[rus]
title           = "Заголовок (title)"
descr           = "META Описание"
keywords        = "META Ключевые слова"
head_inc        = "Вставка в HEAD"
body_inc        = "Вставка в BODY"
local_on        = "Включить локальную вставку"
glob_off        = "Отключить глобальную вставку"
</script>
