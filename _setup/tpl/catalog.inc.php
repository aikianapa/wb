<ul data-wb-role="tree" data-wb-item="products_category" data-wb-children="false">
	<li>
		<div data-wb-hide="*" data-wb-role="foreach" data-wb-from="data[lang]" data-wb-where='lang="{{_SESS[lang]}}"' data-wb-tpl="false">
		<a href="/products/vitrina/{{%id}}">{{value}}</a>
		<div class="megamenudown-sub" style="background-image:url(/uploads/tree/products_category/{{%id}}/{{%data[megaimg][0][img]}});">
			<div class="mega-item-menu" data-wb-where='children = "[]" ' data-hide="wb">
				<a class="mini" href="/products/vitrina/{{%id}}"><span>{{value}}</span></a>
			</div>
			<div class="mega-item-menu" >
				<div data-wb-role="foreach" data-wb-from="%children" data-wb-hide="*">
					<div data-wb-hide="*" data-wb-role="foreach" data-wb-from="data[lang]" data-wb-where='lang="{{_SESS[lang]}}"' data-wb-tpl="false">
					<a href="/products/vitrina/{{%id}}"><span>{{value}}</span></a>
					</div>
				</div>
			</div>
		</div>
		</div>
	</li>
</ul>
<script>
	$(".megamenudown-sub .mega-item-menu").each(function(){
		if ($(this).find("a.mini").length) {
			$(this).find("a.mini").removeClass("mini");
			$(this).parents(".megamenudown-sub").addClass("mini");
		}
	});
</script>
