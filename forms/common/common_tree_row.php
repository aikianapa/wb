<li class='wb-tree-item dd-item' data-id="{{id}}" data-name="{{name}}" data-open="{{open}}" data-data="" title="{{id}}">
	<div class='dd-handle dd3-handle btn-primary'><i class='fa fa-arrows'></i></div>
    <div class="dd3-btn float-right btn-primary"><i class='fa fa-pencil'></i> <span>{{id}}</span></div>
	<!--div class='dd-content dd3-content' contenteditable='true'>{{name}}</div-->
    <input type="text" class='dd-content dd3-content' value="{{name}}" disabled>
    
	<script type="text/json" class="data">
		<![CDATA[{{data}}]]>
	</script>
</li>
