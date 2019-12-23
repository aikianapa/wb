<nav>
<ul id='{{id}}' class="pagination justify-content-center mb-40" data-wb-items='{{count}}' data-wb-cache='{{cache}}' data-wb-find='{{find}}' data-wb-role="foreach" data-wb-from="pages" data-wb-tpl="false">
    <li class="page-item" data-page='{{page}}'>
        <a class="page-link" flag='{{flag}}' href="javascript:void(0)" data-wb-ajaxpage='/{{href}}/'>{{page}}</a>
    </li>
</ul>

<li data-wb-prepend="#{{id}}" class="page-item" data-page="prev">
    <a class="page-link" href tabindex="-1">&laquo;</a>
</li>
<li data-wb-append="#{{id}}" class="page-item" data-page="next">
    <a class="page-link" href>&raquo;</a>
</li>
</nav>
