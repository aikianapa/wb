<div class="modal fade" id="{{_form}}_{{_mode}}" data-keyboard="false" data-backdrop="static" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xxl" role="document">
        <div class="modal-content">
          <div class="modal-header pb-0 pt-1" data-wb-role="include" src="/engine/forms/common/common_tree_edit_nav.php">
    		  </div>

            <!--div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h5 class="modal-title">{{header}}</h5>
      </div-->
            <div class="modal-body">
                <form id="{{_form}}EditForm" data-wb-form="{{_form}}" data-wb-item="{{_item}}" class="form-horizontal" role="form">
                    <div class="row">
                        <div class="col-md-5 col-12 tree-view">
                            <div class="form-group row">
                                <label class="col-sm-3 form-control-label">{{_LANG[name]}}</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="id" placeholder="{{_LANG[name]}}" required>
                                </div>
                            </div>
                            <div class="tab-content p-a m-b-md">
                                <div class="form-group row">
                                    <label class="col-sm-3 form-control-label">{{_LANG[header]}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="header" placeholder="{{_LANG[header]}}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 form-control-label">{{_LANG[tech]}}</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="techdescr" placeholder="{{_LANG[tech]}}">
                                    </div>
                                </div>
                                <div data-wb-role="tree" name="tree"></div>
                            </div>
                        </div>
                        <div class="col-sm-7 hidden-sm-down tree-edit">
                            <div class="col"> </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer" data-wb-role="include" src="form" data-wb-name="common_close_save" data-wb-hide="wb">
            </div>
        </div>
    </div>
</div>

<script type="text/locale" data-wb-role="include" src="tree_common"></script>
