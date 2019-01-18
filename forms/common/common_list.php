<div class="element-wrapper">
    <h6 class="element-header">
                     {{_LANG[title]}}
                     <button class="btn btn-sm btn-success pull-right" data-wb-ajax="/form/edit/{{_form}}/_new" data-wb-append="body">
                       <i class="fa fa-plus"></i> {{_LANG[add]}}
                     </button>
    </h6>
    <div class="element-box">
        <div class="table-responsive">
            <table class="table table-lightborder">
                <thead>
                    <tr>
                        <th>{{_LANG[name]}}</th>
                        <th>{{_LANG[header]}}</th>
                        <th class="text-center"> {{_LANG[visible]}} </th>
                        <th class="text-right"> {{_LANG[action]}} </th>
                    </tr>
                </thead>
                <tbody data-wb-role="foreach" data-wb-table="{{_form}}" data-wb-add="true" data-wb-size="{{_ENV[page_size]}}" data-wb-sort="id">
                    <tr>
                        <td class="nowrap">{{id}}</td>
                        <td class="nowrap">{{header}}</td>
                        <td class="text-center">
                            <div class="status-pill green" data-title="{{_LANG[visible]}}" data-wb-role="where" data='active="on"' data-toggle="tooltip"></div>
                            <div class="status-pill red" data-title="{{_LANG[invisible]}}" data-wb-role="where" data='active=""' data-toggle="tooltip"></div>
                        </td>
                        <td class="text-right" data-wb-role="include" src="form" data-wb-name="common_item_actions"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/locale">
[eng]
        title		= "Items list"
	name            = "Item name"
	header		= "Header"
	visible		= "Visible"
        invisible	= "Invisible"
	action		= "action"
        add             = "Add item"
[rus]
        title		= "Список записей"
	name            = "Имя записи"
	header		= "Заголовок"
	visible		= "Отображать"
        invisible	= "Не отображать"
	action		= "Действие"
        add             = "Добавить запись"
</script>
