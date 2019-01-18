<div data-wb-role="include" src="form" data-wb-name="engine:news_edit"></div>

<div class="col-6 col-sm-12" data-wb-after="input[name=id]">
        <div class="form-group row" data-wb-before=".nav-active-primary">
                <label class="col-2 form-control-label">Идентификатор</label>
                <div class="col-10">
                        <input type="text" class="form-control" name="id" placeholder="ID записи" required>
                </div>
        </div>
</div>


<div class="col-6 col-sm-12" data-wb-append=".row.type">
    <label class="col-12 form-control-label">Раздел библиотеки</label>
    <div class="col-12">
        <select class="form-control" name="library" placeholder="Выберите..." data-wb-role="tree" data-wb-item="menu" data-wb-branch="side->interest->lib" data-wb-parent="false">
            <option value="{{id}}">{{name}}</option>
        </select>
    </div>
</div>

<div class="form-group row" data-wb-before=".nav-active-primary">

        <label class="col-3 form-control-label">На главную</label>
        <div class="col-2"><label class="switch switch-success"><input type="checkbox" name="home"><span></span></label></div>

        <label class="col-3 form-control-label">В Статьи</label>
        <div class="col-2"><label class="switch switch-success"><input type="checkbox" name="article"><span></span></label></div>


</div>


<button type="button" class="btn btn-secondary btn-sm translate" data-wb-append=".nav-active-primary .nav">Автоперевод</button>

<div class="form-group row"  data-wb-html="#newsDescr">
    <div class="col-sm-12">
        <div data-wb-role="tree" name="lang" data-wb-dict="test"></div>
    </div>
</div>
