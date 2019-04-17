<div data-wb-role="include" src="form" data-wb-name="engine:news_edit"></div>

<div class="col-6 col-sm-12" data-wb-after="input[name=id]">
        <div class="form-group row" data-wb-before=".nav-active-primary">
                <label class="col-2 form-control-label">ID</label>
                <div class="col-10">
                        <input type="text" class="form-control" name="id" placeholder="ID" required>
                </div>
        </div>
</div>



<div class="form-group row" data-wb-before=".nav-active-primary">

        <label class="col-3 form-control-label">{{_LANG[home]}}</label>
        <div class="col-2"><label class="switch switch-success"><input type="checkbox" name="home"><span></span></label></div>

        <label class="col-3 form-control-label">{{_LANG[article]}}</label>
        <div class="col-2"><label class="switch switch-success"><input type="checkbox" name="article"><span></span></label></div>


</div>


<div class="form-group row"  data-wb-html="#newsDescr">
    <div class="col-sm-12">
        <div data-wb-role="tree" name="lang" data-wb-dict="test"></div>
    </div>
</div>
