<div class="element-wrapper">
    <h6 class="element-header">
        Список коментариев
        <button class="btn btn-sm btn-success pull-right" data-wb-ajax="/form/edit/{{_form}}/_new" data-wb-append="body">
           <i class="fa fa-plus"></i> Добавить коментарий
         </button>
    </h6>
    <div class="element-box">
        <div class="table-responsive">
            <table class="table table-lightborder">
                <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Имя</th>
                        <th>Эл.почта</th>
                        <th>Рейтинг</th>
                        <th>Статус</th>
                        <th class="text-right"> Действие </th>
                    </tr>
                </thead>
                <tbody data-wb-role="foreach" data-wb-table="{{_form}}" data-wb-add="true" data-wb-size="{{_ENV[page_size]}}" data-wb-sort="date:d">
                    <tr>
                        <td class="{{class}}">{{dateshow}}</td>
                        <td class="{{class}}">{{name}}</td>
                        <td>{{email}}</td>
                        <td>{{rating}}</td>
                        <td class="text-center">
                            <div class="status-pill green" data-title="Активен" data-wb-role="where" data='active="on"' data-toggle="tooltip"></div>
                            <div class="status-pill red" data-title="Активен" data-wb-role="where" data='active=""' data-toggle="tooltip"></div>
                        </td>
                        <td class="text-right" data-wb-role="include" src="/engine/forms/common/item_actions.php"> </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
