<ul id='{{id}}' class='pagination' data-wb-size='{{size}}' data-wb-items='{{count}}' data-wb-cache='{{cache}}' data-wb-find='{{find}}' data-wb-role="foreach" data-wb-from="pages" data-wb-tpl="false">
	<li class="page-item" data-page='{{page}}'>
		<a flag='{{flag}}' data-wb-ajaxpage='/{{href}}/' class="page-link" data='{{data}}'>{{page}}</a>
	</li>
</ul>
<a data-wb-prepend=".pagination" class="prev pull-left"><i class="icon-arrow-left-circle"></i> <span>{{_VAR[btn_prev]}}</span></a>
<a data-wb-append=".pagination" class="next pull-right">{{_VAR[btn_next]}} <i class="icon-arrow-right-circle"></i></a>
