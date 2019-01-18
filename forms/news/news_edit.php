<div class="modal fade" id="{{_form}}_{{_mode}}" data-keyboard="false" data-backdrop="true" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
                <h5 class="modal-title">{{header}}</h5>
            </div>
            <div class="modal-body">

                <form autocomplete="off" id="{{_form}}EditForm" data-wb-form="{{_form}}" data-wb-item="{{_item}}" class="form-horizontal" role="form">
                    <input type="hidden" class="form-control" name="id" required>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">{{_LANG[datetime]}}</label>
                        <div class="col-sm-4"><input type="datetimepicker" class="form-control" data-format="" name="date" placeholder="{{_LANG[datetime]}}" required></div>
                        <label class="col-sm-2 form-control-label">{{_LANG[visible]}}</label>
                        <div class="col-sm-2"><label class="switch switch-success"><input type="checkbox" name="active"><span></span></label></div>
                    </div>

<div class="nav-active-primary">
<ul class="nav nav-tabs" role="tablist">
	<li class="nav-item"><a class="nav-link active" href="#{{_form}}Descr" data-toggle="tab">{{_LANG[content]}}</a></li>
	<li class="nav-item"><a class="nav-link" href="#{{_form}}Images" data-toggle="tab">{{_LANG[images]}}</a></li>
    <li class="nav-item"><a class="nav-link" href="#{{_form}}Seo" data-toggle="tab">{{_LANG[seo]}}</a></li>
</ul>
</div>
<div class="tab-content  p-a m-b-md">
<br />
<div id="{{_form}}Descr" class="tab-pane fade show active" role="tabpanel">

	<div class="form-group row">
	  <label class="col-sm-3 form-control-label">{{_LANG[header]}}</label>
	   <div class="col-sm-9"><input type="text" class="form-control" name="header" placeholder="{{_LANG[header]}}" required></div>
	</div>

	<div class="form-group row">
	  <label class="col-sm-3 form-control-label">{{_LANG[short]}}</label>
        <div class="col-sm-9"><textarea class="form-control" name="descr" placeholder="{{_LANG[short]}}"></textarea></div>
	</div>

	<div class="form-group row">
        <label class="col-sm-3 form-control-label">
            {{_LANG[content]}}
            <div class="row">
            <div class="col-6 col-sm-12">
                <label class="col-12 form-control-label">{{_LANG[home]}}</label>
                <div class="col-12"><label class="switch switch-success"><input type="checkbox" name="home"><span></span></label></div>
            </div>
            </div>
        </label>
	   <div class="col-sm-9" data-wb-role="include" src="editor"></div>
	</div>

</div>
<div id="{{_form}}Images" class="tab-pane fade" data-wb-role="include" src="uploader" role="tabpanel"></div>
<div id="{{_form}}Seo" class="tab-pane fade" data-wb-role="include" src="seo" role="tabpanel"></div>
</div>

                </form>
            </div>
            <div class="modal-footer" data-wb-role="include" src="form" data-wb-name="common_close_save" data-wb-hide="wb">
            </div>
        </div>
    </div>
</div>
<script type="text/locale" data-wb-role="inlclude" src="news_edit"></script>
