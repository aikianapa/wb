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
                        <th>{{_LANG[id]}}</th>
                        <th>{{_LANG[datetime]}}</th>
                        <th>{{_LANG[client]}}</th>
                        <th class="text-center">{{_LANG[status]}}</th>
                        <th class="text-right">{{_LANG[action]}}</th>
                    </tr>
                </thead>
                <tbody data-wb-role="foreach" data-wb-table="{{_GET[form]}}" data-wb-add="true" data-wb-size="{{_ENV[page_size]}}" data-wb-sort="date:d active:d">
                    <tr>
                        <td class="nowrap"> {{id}} </td>
                        <td class="nowrap"> {{date}} </td>
                        <td class="nowrap"> {{name}} </td>
                        <td class="text-center">
                            <div class="status-pill green" data-title="{{_LANG[active]}}" data-wb-where='"{{active}}"="on"' data-toggle="tooltip"></div>
                            <div class="status-pill red" data-title="{{_LANG[inactive]}}" data-wb-where='"{{active}}"=""' data-toggle="tooltip"></div>
                            <div class="status-pill green" data-title="{{_LANG[paid]}}" data-wb-where='"{{payed}}"="on"' data-toggle="tooltip"></div>
                            <div class="status-pill red" data-title="{{_LANG[notpaid]}}" data-wb-where='"{{payed}}"=""' data-toggle="tooltip"></div>
                            <div class="status-pill green" data-title="{{_LANG[shipped]}}" data-wb-where='"{{shipped}}"="on"' data-toggle="tooltip"></div>
                            <div class="status-pill red" data-title="{{_LANG[notshipped]}}" data-wb-where='"{{shipped}}"=""' data-toggle="tooltip"></div>
                        </td>
                        <td class="text-right" data-wb-role="include" src="form" data-wb-name="common_item_actions"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/locale" data-wb-role="inlclude" src="orders_common"></script>
