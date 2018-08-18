<div class='wb-gallery'>
    <meta data-wb-role="variable" var="gal_width"  data-wb-if='"{{images_position[width]}}">"0"' value="{{images_position[width]}}" else="{{_ENV[thumb_width]}}">
    <meta data-wb-role="variable" var="gal_height" data-wb-if='"{{images_position[height]}}">"0"' value="{{images_position[height]}}" else="{{_ENV[thumb_height]}}">
    <div data-wb-role="foreach" data-wb-from="images" data-wb-tpl="false" data-wb-hide="*" data-wb-where='visible="1"'>
		<a href="/uploads/{{%_table}}/{{%id}}/{{img}}">
			<img data-wb-role="thumbnail" data-wb-size="{{_VAR[gal_width]}};{{_VAR[gal_height]}};src" src="/uploads/{{%_table}}/{{%id}}/{{img}}" >
		</a>
    </div>
</div>
