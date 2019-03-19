$("[data-wb-editable]").each(function(){
	var edit=$(this);
	var html=edit.html();
	edit.data("editable",edit.attr("data-wb-editable"));
	edit.data("editable-inner",edit.attr("data-wb-editable-inner"));
	edit.data("editable-route",edit.attr("data-wb-editable-route"));
	edit.data("editable-item",edit.attr("data-wb-editable-item"));
	edit.removeAttr("data-wb-editable").removeAttr("data-wb-editable-inner").removeAttr("data-wb-editable-route").removeAttr("data-wb-editable-item");
	edit.attr("contenteditable",true);
	edit.on("blur",function(){
		var text=$(this).html();
		if (html !== text) {
			$.post("/ajax/wb_tag_editable/",{
				param:edit.data("editable"),
				text:text,
				inner:edit.data("editable-inner"),
				item:edit.data("editable-item"),
				route:edit.data("editable-route")
				},function(data){
				html = text;
				console.log(data);
			});
		}
	});
});

