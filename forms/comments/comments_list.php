<div class="element-wrapper">
    <h6 class="element-header">
        {{_LANG[list]}}
        <button class="btn btn-sm btn-success pull-right" data-wb-ajax="/form/edit/{{_form}}/_new" data-wb-append="body">
           <i class="fa fa-plus"></i> {{_LANG[add]}}
         </button>
    </h6>
    <div class="element-box">
        <div class="table-responsive">
            <table class="table table-lightborder">
                <thead>
                    <tr>
                        <th>{{_LANG[datetime]}}</th>
                        <th>{{_LANG[name]}}</th>
                        <th>{{_LANG[email]}}</th>
                        <th>{{_LANG[rating]}}</th>
                        <th>{{_LANG[status]}}</th>
                        <th class="text-right">{{_LANG[action]}}</th>
                    </tr>
                </thead>
                <tbody data-wb-role="foreach" data-wb-table="{{_form}}" data-wb-add="true" data-wb-size="{{_ENV[page_size]}}" data-wb-sort="date:d">
                    <tr>
                        <td class="{{class}}">{{dateshow}}</td>
                        <td class="{{class}}">{{name}}</td>
                        <td>{{email}}</td>
                        <td>{{rating}}</td>
                        <td class="text-center">
                            <div class="status-pill green" data-title="{{_LANG[published]}}" data-wb-role="where" data='active="on"' data-toggle="tooltip"></div>
                        </td>
                        <td class="text-right" data-wb-role="include" src="form" data-wb-name="common_item_actions"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/locale" data-wb-role="include" src="comments_common"></script>
