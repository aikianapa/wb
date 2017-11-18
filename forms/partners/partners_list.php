<div class="element-wrapper">
    <h6 class="element-header">
        Список партнёров
    </h6>
    <div class="element-box">
        <button class="btn btn-sm btn-success pull-right" data-wb-ajax="/form/edit/{{_GET[form]}}/_new" data-wb-append="body">
                       <i class="os-icon os-icon-ui-22"></i> Добавить партнёра
                     </button>
        <div class="table-responsive">
            <table class="table table-lightborder">
                <thead>
                    <tr>
                        <th>Наименование</th>
                        <th>ИНН</th>
                        <th class="text-center">
                            Статус
                        </th>
                        <th class="text-center">
                            Своё
                        </th>
                        <th class="text-right">
                            Действие
                        </th>
                    </tr>
                </thead>
                <tbody data-wb-role="foreach" data-wb-table="{{_GET[form]}}" data-wb-add="true" data-wb-size="12" data-wb-sort="id">
                    <tr>
                        <td class="nowrap">
                            {{name}}
                        </td>
                        <td>{{inn}}</td>
                        <td class="text-center">
                            <div class="status-pill green" data-title="Активен" data-wb-role="where" data='active="on"' data-toggle="tooltip"></div>
                            <div class="status-pill red" data-title="Отключен" data-wb-role="where" data='active=""' data-toggle="tooltip"></div>
                        </td>
                        <td class="text-center">
                            <div class="status-pill green" data-title="Своё" data-wb-role="where" data='self="on"' data-toggle="tooltip"></div>
                            <div class="status-pill grey" data-title="Партнёр" data-wb-role="where" data='self=""' data-toggle="tooltip"></div>
                        </td
                        <td class="text-right" data-wb-role="include" src="/engine/forms/common/item_actions.php">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
