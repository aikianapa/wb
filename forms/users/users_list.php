
<div class="element-wrapper">
    <h6 class="element-header">
     {{_LANG[list]}}
     <button class="btn btn-sm btn-success pull-right" data-wb-ajax="/form/edit/{{_GET[form]}}/_new" data-wb-append="body">
       <i class="fa fa-plus"></i> {{_LANG[add]}}
     </button>
    </h6>
    <div class="element-box">
      <div class="table-responsive">
        <table class="table table-lightborder">
          <thead>
            <tr>
              <th>{{_LANG[login]}}</th>
              <th>{{_LANG[nickname]}}</th>
              <th class="text-center">{{_LANG[status]}}</th>
              <th class="text-right">{{_LANG[action]}}</th>
            </tr>
          </thead>
          <tbody data-wb-role="foreach" data-wb-table="{{_GET[form]}}" data-wb-add="true" data-wb-size="{{_ENV[page_size]}}" data-wb-sort="id">
            <tr>
              <td class="nowrap">{{id}}</td>
              <td>{{name}}</td>
              <td class="text-center">
                <div class="status-pill green" data-title="{{_LANG[enabled]}}" data-wb-role="where" data='active="on"' data-toggle="tooltip"></div>
                <div class="status-pill red" data-title="{{_LANG[disabled]}}" data-wb-role="where" data='active=""' data-toggle="tooltip"></div>
                <div class="status-pill yellow" data-title="{{_LANG[group]}}" data-wb-role="where" data='isgroup="on"' data-toggle="tooltip"></div>
              </td>
              <td class="text-right" data-wb-role="include" src="form" data-wb-name="common_item_actions"></td>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
</div>
<script type="text/locale" data-wb-role="include" src="users_common"></script>
