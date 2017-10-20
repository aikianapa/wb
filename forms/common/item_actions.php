<div class="btn-group">
  <button class="btn btn-sm btn-white" type="button" data-wb-ajax="/form/edit/{{_table}}/{{id}}" data-wb-append="body">
    <i class="fa fa-pencil"></i>
  </button>
  <button aria-expanded="false" aria-haspopup="true" class="btn btn-sm btn-white dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" type="button">
    <span class="sr-only">Toggle Dropdown</span>
  </button>
  <div class="dropdown-menu dropdown-menu-right">
    <a class="dropdown-item" href="javascript:void(0);"
      data-wb-ajax="/form/edit/{{_table}}/{{id}}" data-wb-append="body">
      <i class="fa fa-pencil"></i> Изменить
    </a>
    <!--a class="dropdown-item" href="#"> <i class="fa fa-pencil"></i> Переименовать</a>
    <a class="dropdown-item" href="#"> <i class="fa fa-pencil"></i> Дублировать</a-->
    <div class="dropdown-divider"></div>
    <a class="dropdown-item" href="javascript:void(0);"
      data-wb-ajax="/form/remove/{{_table}}/{{id}}/?confirm=true" data-wb-append="body">
      <i class="fa fa-trash"></i> Удалить
    </a>
  </div>
</div>
