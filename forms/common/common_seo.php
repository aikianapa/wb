<div class="form-group row">
    <label class="col-sm-3 form-control-label">Заголовок (title)</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="title" placeholder="Заголовок окна (title)"> </div>
</div>
<div class="form-group row">
    <label class="col-sm-3 form-control-label">Описание</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" name="meta_description" placeholder="Описание"> </div>
</div>
<div class="form-group row">
    <label class="col-sm-3 form-control-label">Ключевые слова</label>
    <div class="col-sm-9">
        <input type="text" class="form-control input-tags" name="meta_keywords" placeholder="Ключевые слова"> </div>
</div>

<ul class="nav nav-tabs" role="tablist">
	<li class="nav-item"><a class="nav-link active" href="#{{_form}}SeoHead" data-toggle="tab">Вставка в HEAD</a></li>
	<li class="nav-item"><a class="nav-link" href="#{{_form}}SeoBody" data-toggle="tab" >Вставка в BODY</a></li>
</ul>

<div class="tab-content  p-a m-b-md">
<br />
<div id="{{_form}}SeoHead" class="tab-pane fade show active" role="tabpanel">
  <div class="form-group row">
      <div class="col-sm-3">
          <h6 class="form-control-label">Вставка в HEAD</h6>
          <label class="col-12 form-control-label">Включить локальную вставку</label>
          <div class="col-sm-2">
              <label class="switch switch-success">
                  <input type="checkbox" name="head_add_active"><span></span></label>
          </div>
          <label class="col-12 form-control-label">Отключить глобальную вставку</label>
          <div class="col-sm-2">
              <label class="switch switch-success">
                  <input type="checkbox" name="head_noadd_glob"><span></span></label>
          </div>
      </div>
      <div class="col-sm-9">
          <div data-wb-role="include" data-wb-name="head_add" src="source"></div>
      </div>
  </div>
</div>
<div id="{{_form}}SeoBody" class="tab-pane fade show" role="tabpanel">

  <div class="form-group row">
      <div class="col-sm-3">
          <h6 class="form-control-label">Вставка в BODY</h6>
          <label class="col-12 form-control-label">Включить локальную вставку</label>
          <div class="col-sm-2">
              <label class="switch switch-success">
                  <input type="checkbox" name="body_add_active"><span></span></label>
          </div>
          <label class="col-12 form-control-label">Отключить глобальную вставку</label>
          <div class="col-sm-2">
              <label class="switch switch-success">
                  <input type="checkbox" name="body_noadd_glob"><span></span></label>
          </div>
      </div>
      <div class="col-sm-9">
          <div data-wb-role="include" data-wb-name="body_add" src="source"></div>
      </div>
  </div>
</div>
</div>
