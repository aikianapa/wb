<div class="modal fade" id="{{_GET[form]}}_{{_GET[mode]}}" data-keyboard="false" data-backdrop="true" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
	<div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h5 class="modal-title">{{header}}</h5>
      </div>
      <div class="modal-body">

<form id="pagesEditForm" data-wb-form="{{_form}}" data-wb-item="{{_item}}"  class="form-horizontal" role="form">

	<div class="form-group row">
	  <label class="col-sm-3 form-control-label">{{_LANG[page]}}</label>
    <div class="input-group col-sm-9">
      <span class="input-group-addon">{{_SRV[HTTP_HOST]}}/</span>
      <input type="text" class="form-control" name="id" placeholder="{{_LANG[page]}}" required >
    </div>
	</div>

  <div class="form-group row">
	  <label class="col-sm-3 form-control-label">{{_lang[tech]}}</label>
	   <div class="col-sm-9"><input type="text" class="form-control" name="techdescr" placeholder="{{_lang[tech]}}" ></div>
	</div>

<div class="nav-active-primary">
<ul class="nav nav-tabs" role="tablist">
	<li class="nav-item"><a class="nav-link active" href="#pageDescr" data-toggle="tab">{{_lang[content]}}</a></li>
	<li class="nav-item"><a class="nav-link" href="#pageImages" data-toggle="tab">{{_lang[images]}}</a></li>
    <li class="nav-item"><a class="nav-link" href="#pageSeo" data-toggle="tab">{{_lang[seo]}}</a></li>
</ul>
</div>
<div class="tab-content  p-a m-b-md">
<br />
<div id="pageDescr" class="tab-pane fade show active" role="tabpanel">

	<div data-wb-role="tree" name="lang" data-wb-dict="content"></div>

	<div class="form-group row">
	  <label class="col-sm-2 form-control-label">{{_lang[template]}}</label>
	   <div class="col-sm-10">
		   <select class="form-control" name="template" placeholder="{{_lang[template]}}" data-wb-role="foreach" data-wb-from="tpllist">
				<option value="{{%tpllist[{{_idx}}]}}">{{%tpllist[{{_idx}}]}}</option>
		   </select>
		</div>
	</div>

  <div class="form-group row">
    <label class="col-sm-2 form-control-label">{{_lang[visible]}}</label>
    <div class="col-sm-2"><label class="switch switch-success"><input type="checkbox" name="active"><span></span></label></div>
	</div>
</div>


<div id="pageImages" class="tab-pane fade" data-wb-role="include" src="uploader" role="tabpanel"></div>
<div id="pageSeo" class="tab-pane fade" data-wb-role="include" src="seo" role="tabpanel"></div>
</div>
</form>


    </div>
            <div class="modal-footer" data-wb-role="include" src="form" data-wb-name="common_close_save" data-wb-hide="wb"></div>

		</div>
</div>
</div>
<script type="text/locale" data-wb-role="inlclude" src="pages_edit"></script>
