<div class="element-wrapper">
    <h6 class="element-header">
                     Список новостей
                     <button class="btn btn-sm btn-success pull-right" data-wb-ajax="/form/edit/{{_GET[form]}}/_new" data-wb-append="body">
                       <i class="fa fa-plus"></i> Добавить новость
                     </button>
    </h6>
    <div class="element-box">
        <div class="table-responsive">
            <table class="table table-lightborder">
                <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Заголовок</th>
                        <th class="text-center">Главн.</th>
                        <th class="text-center">Статус</th>
                        <th class="text-right">Действие</th>
                    </tr>
                </thead>
                <tbody data-wb-role="foreach" data-wb-table="{{_GET[form]}}" data-wb-add="true" data-wb-size="12" data-wb-sort="date:d">
                    <tr>
                        <td class="nowrap"> {{datetime}} </td>
                        <td class="nowrap"> {{header}} </td>
                        <td class="text-center">
                            <div class="status-pill green" data-title="На главной" data-wb-role="where" data='home="on"' data-toggle="tooltip"></div>
                            <div class="status-pill red" data-title="Не на главной" data-wb-role="where" data='home=""' data-toggle="tooltip"></div>
                        </td>
                        <td class="text-center">
                            <div class="status-pill green" data-title="Активен" data-wb-role="where" data='active="on"' data-toggle="tooltip"></div>
                            <div class="status-pill red" data-title="Не активен" data-wb-role="where" data='active=""' data-toggle="tooltip"></div>
                        </td>
                        <td class="text-right" data-wb-role="include" src="/engine/forms/common/item_actions.php"> </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>