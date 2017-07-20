
<div class="element-wrapper">
                    <h6 class="element-header">
                     Список сзаписей
                    </h6>
                    <div class="element-box">
                      <button  data-wb-ajax="/form/edit/{{_ENV[route][form]}}/_new" data-wb-append="body">Новая запись</button>
                      <div class="table-responsive">
                        <table class="table table-lightborder">
                          <thead>
                            <tr>
                              <th>
                                Страница
                              </th>
                              <th>
                                Products Ordered
                              </th>
                              <th class="text-center">
                                Status
                              </th>
                              <th class="text-right">
                                Действие
                              </th>
                            </tr>
                          </thead>
                          <tbody data-wb-role="foreach" data-wb-table="{{_ENV[route][form]}}" data-wb-add="true" data-wb-size="20" data-wb-sort="id">
                            <tr>
                              <td class="nowrap">
									{{id}} {{header}}
                              </td>
                              <td>
                                <div class="cell-image-list">
                                  <div class="cell-img" style="background-image: url(img/portfolio9.jpg)"></div>
                                  <div class="cell-img" style="background-image: url(img/portfolio2.jpg)"></div>
                                  <div class="cell-img" style="background-image: url(img/portfolio12.jpg)"></div>
                                  <div class="cell-img-more">
                                    + 5 more
                                  </div>
                                </div>
                              </td>
                              <td class="text-center">
                                <div class="status-pill green" data-title="Complete" data-toggle="tooltip"></div>
                              </td>
                              <td class="text-right">
                                <i class="fa fa-pencil" data-wb-ajax="/form/edit/{{_ENV[route][form]}}/{{id}}" data-wb-append="body"></i>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
</div>
