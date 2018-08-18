
<div class="element-wrapper">
                    <h6 class="element-header">
                     Список страниц
                     <button class="btn btn-sm btn-success pull-right" data-wb-ajax="/form/edit/{{_GET[form]}}/_new" data-wb-append="body">
                       <i class="fa fa-plus"></i> Добавить страницу
                     </button>
                    </h6>
                    <div class="element-box">
                      <div class="table-responsive">
                        <table class="table table-lightborder">
                          <thead>
                            <tr>
                              <th>Страница</th>
                              <th>Заголовок</th>
                              <th class="text-center">Статус</th>
                              <th class="text-right">Действие</th>
                            </tr>
                          </thead>
                          <tbody data-wb-role="foreach" data-wb-table="pages" data-wb-add="true" data-wb-size="{{_ENV[page_size]}}" data-wb-sort="id">
                            <tr>
                              <td class="nowrap">{{_SRV[HTTP_HOST]}}/{{id}}
                                <br><small data-wb-where='template!=""'>/tpl/{{template}}</small>
                                </td>
                              <td>{{header}}<br><small>{{techdescr}}</small></td>
                              <td class="text-center">
                                <div class="status-pill green" data-title="Активен" data-wb-role="where" data='active="on"' data-toggle="tooltip"></div>
                                <div class="status-pill red" data-title="Активен" data-wb-role="where" data='active=""' data-toggle="tooltip"></div>
                              </td>
                              <td class="text-right" data-wb-role="include" src="/engine/forms/common/item_actions.php">
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
</div>
