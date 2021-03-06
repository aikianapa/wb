<div class="element-wrapper">
                    <h6 class="element-header">
                     Список накладных
                     <button class="btn btn-sm btn-success pull-right" data-wb-ajax="/form/edit/{{_form}}/_out" data-wb-append="body">
                       <i class="fa fa-plus"></i> Расходная
                     </button>
                     <span class="pull-right">&nbsp;</span>
                     <button class="btn btn-sm btn-warning pull-right" data-wb-ajax="/form/edit/{{_form}}/_in" data-wb-append="body">
                       <i class="fa fa-plus"></i> Приходная
                     </button>

                    </h6>
                    <div class="element-box">
                      <div class="table-responsive">
                        <table class="table table-lightborder">
                          <thead>
                            <tr>
                                <th>Номер</th>
                                <th>Дата</th>
                                <th>Поставщик</th>
                                <th>Получатель</th>
                                <th>Сумма</th>
                                <th class="text-right">Действие</th>
                            </tr>
                          </thead>
                          <tbody data-wb-role="foreach" data-wb-table="bills" data-wb-add="true" data-wb-size="{{_ENV[page_size]}}" data-wb-sort="date number">
                            <tr>
                              <td>{{number}}</td>
                              <td>{{date}}</td>
                              <td role="formdata" data-wb-table="partners" data-wb-item="{{payer}}" class="hidden-ovf">
                                  {{name}}
                              </td>
                              <td role="formdata" data-wb-table="partners" data-wb-item="{{recipient}}" class="hidden-ovf">
                                  {{name}}
                              </td>
                                <td>{{summ}}</td>
                              <td class="text-right" data-wb-role="include" src="/engine/forms/common/item_actions.php">
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
</div>
