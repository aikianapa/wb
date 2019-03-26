<h6 class="element-header">
	<div class="row">
	<div class="col-4 col-sm-6">{{_LANG[formbuilder]}}</div>
	<div class="col-4 col-sm-3">
	<select id="modFormBuilderSelectForm" class="form-control" data-wb-role="foreach" data-wb-from='_ENV.forms' placeholder="Select form">
		<option value="{{_ENV.forms[{{_idx}}]}}">{{_ENV.forms[{{_idx}}]}}</option>
	</select>
	</div>
	<div class="col-4 col-sm-3">
	<button id="modFormBuilderCreator" class="btn btn-success pull-right"><i class="fa fa-plus"></i> &nbsp; {{_LANG[createform]}}</button>
	</div>
	</div>
</h6>
<div class="col-12" id="modFormBuilder">
    <div class="content-wrapper">
        <div class="content-left" id="modFormBuilderMenu">
		<div class="nav-active-primary">
		<ul class="nav nav-tabs" role="tablist">
			<li class="nav-item"><a class="nav-link active" href="#modFormBuilderFilesList" data-toggle="tab" >{{_lang[files]}}</a></li>
			<li class="nav-item"><a class="nav-link disabled" href="#modFormBuilderSnippets" data-toggle="tab">{{_lang[snippets]}}</a></li>
		</ul>
		</div>
		<div class="tab-content  p-a m-b-md">
		<div id="modFormBuilderFilesList" class="tab-pane fade show active" role="tabpanel">
			    <label class="content-left-label">{{_LANG[files]}}</label>
			    <ul class="nav mg-t-1-force">
				<script type="text/template" id="_modFormBuilderFilesList">
				<li class="nav-item">
					<a data-wb-href class="nav-link">
					<i class="fa fa-id-card-o"></i>
					<span>{{file}}</span>
					</a>
				</li>
				</script>
			    </ul>
		</div>


		<div id="modFormBuilderSnippets" class="tab-pane fade" role="tabpanel">
			<div id="modFormBuilderSnippetsAccordion" class="accordion" role="tablist" aria-multiselectable="true" data-wb-role="tree" data-wb-from="snippets" data-wb-children="false" data-wb-hide="wb">
				<meta data-wb-role="variable" var="show" data-wb-if='"{{_idx}}"="0"' value="show" else="" data-wb-hide="*">
				<meta data-wb-role="variable" var="collapsed" data-wb-if='"{{_idx}}"="0"' value="" else="collapsed" data-wb-hide="*">
			  <div class="card">
			    <div class="card-header" role="tab" id="heading_{{_idx}}">
			      <h6 class="mg-b-0">
				<a data-toggle="collapse" data-parent="#modFormBuilderSnippetsAccordion" href="#modFormBuilder-ac{{_idx}}" aria-expanded="true" aria-controls="collapse_{{_idx}}" class="tx-gray-800 transition {{_VAR.collapsed}}">
				  <strong>{{id->ucwords()}}</strong>
				</a>
			      </h6>
			    </div><!-- card-header -->
			    <div id="modFormBuilder-ac{{_idx}}" class="collapse {{_VAR.show}}" role="tabpanel" aria-labelledby="heading_{{_idx}}">
			      <div class="card-block">
				<ul class="nav mt-0" data-wb-role="foreach" data-wb-from="children" data-wb-hide="wb">
				<li class="nav-item">
					<a class="nav-link nav-snip wb-mod-snippet" data-wb-href>{{data.code}}</a>
				</li>
				</ul>
			      </div>
			    </div>
			  </div><!-- card -->
			  <!-- ADD MORE CARD HERE -->
			</div>

			<meta data-wb-selector="#modFormBuilderSnippets ul.nav ul" data-wb-addclass="nav-sub">
		</div>
		</div>
        </div>
        <!-- content-left -->
        <div class="content-body" id="modFormBuilderPanel">
		<div class="nav-active-primary">
		<a class="btn pull-right btn-secondary" style="margin-top:-1px;" data-wb-href id="modFormBuilderSaveBtn"><i class="fa fa-save"></i> {{_lang[save]}}</a>
		<ul class="nav nav-tabs" role="tablist">
			<li class="nav-item"><a class="nav-link active" href="#modFormBuilderView" data-toggle="tab" ><i class="fa fa-id-card-o"></i> {{_lang[view]}}</a></li>
			<li class="nav-item"><a class="nav-link" href="#modFormBuilderCode" data-toggle="tab"><i class="fa fa-code"></i> {{_lang[code]}}</a></li>
		</ul>
		</div>
		<div class="tab-content  p-a m-b-md">
			<br>
			<div id="modFormBuilderView" class="tab-pane fade show active" role="tabpanel">
			</div>
			<div id="modFormBuilderCode" class="tab-pane fade" role="tabpanel">
				<textarea class="source" name="source"></textarea>
			</div>
		</div>

        </div>
    </div>

    <meta data-wb-role="include" src="snippet" data-wb-mode="modal" data-wb-id="modFormBuilderModal" data-wb-class="hidden">
    <meta data-wb-role="include" src="snippet" data-wb-mode="modal" data-wb-id="modFormBuilderSave" data-wb-class="hidden">

	<script type="text/template" id="_modCreateForm">
		<form id="modCreateForm" class="form-horizontal" role="form">
			<div class="form-group row">
				<label class="col-12 form-control-label">{{_lang[formid]}}</label>
				<div class="col-12 col-sm-7"><input type="text" class="form-control" name="formname" placeholder="{{_lang[formid]}} (pages, news, etc...)"></div>
				<div class="col-12 col-sm-5">
					<button type="button" class="btn btn-success mt-2 mt-sm-0 pull-right"><i class="fa fa-id-card-o"></i> {{_LANG.createform}}</button>
				</div>
			</div>
		</form>
	</script>

	<div data-wb-html="#modFormBuilderSave .modal-body">
		<p>Please, choose save or discard changes.</p>
		<span data-wb-html="#modFormBuilderSave .modal-title">Form changed!</span>
		<button data-wb-append="#modFormBuilderSave .modal-footer" class="btn btn-seconary" data-dismiss="modal">{{_lang.cancel}}</button>
		<button data-wb-append="#modFormBuilderSave .modal-footer" class="btn btn-danger">{{_lang.discard}}</button>
		<button data-wb-append="#modFormBuilderSave .modal-footer" class="btn btn-success">{{_lang.save}}</button>
	</div>


</div>

<script src="/engine/modules/formbuilder/formbuilder.js?{{_ENV[new_id]}}"></script>
<script>
        wb_include("/engine/modules/formbuilder/formbuilder.css");
</script>
<script type="text/locale">
[eng]
formbuilder		= "Form Builder"
createform		= "Create form"
formid			= "Form ID"
files			= "Files"
code			= "Code"
view			= "View"
files			= "Files"
snippets		= "Snippets"
save			= "Save"
discard			= "Discard"
cancel			= "Cancel"

[rus]
formbuilder		= "Редактор форм"
createform		= "Создать форму"
formid			= "Идетификатор формы"
files			= "Файлы"
code			= "Код"
view			= "Вид"
files			= "Файлы"
snippets		= "Шаблоны"
save			= "Сохранить"
discard			= "Не сохранять"
cancel			= "Отмена"
</script>
