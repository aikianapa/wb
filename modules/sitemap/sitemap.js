function moduleSitemapGenerator() {
	if ($("#sitemapGenerator").length) {
        $("#sitemapGenerator .alert-success").hide().removeClass("hidden");
        $("#sitemapGenerator .alert-danger").hide().removeClass("hidden");
        $("#sitemapGenerator .progress").hide().removeClass("hidden");

        $("#sitemapGenerator .btn").on("click",function(event){
            $("#sitemapGenerator").data("sub",$("#sitemapGenerator form [name=sub]").val());
            $("#sitemapGenerator a.btn").hide();
            $("body").addClass("cursor-wait");
            moduleSitemapGeneratorAjax();

        });
	}
}

function moduleSitemapGeneratorAjax(url) {
		$("#sitemapGenerator .progress").show("fade");
		if ($("#sitemapGenerator").data("ready")==undefined || $("#sitemapGenerator").data("ready")=="") {$("#sitemapGenerator").data("ready",[]);}
		if ($("#sitemapGenerator").data("stack")==undefined || $("#sitemapGenerator").data("stack")=="") {$("#sitemapGenerator").data("stack",[]);}
		var url=url;
        var sub=$("#sitemapGenerator").data("sub");
		$.ajax({
			async: 		true,
			type:		'POST',
			data:		{link:url,sub:sub},
			url: "/module/sitemap/ajax",
			success: function(data){
				if (url=="__finish__") {
					$("#sitemapGenerator .alert-success").show("fade");
					setTimeout(function(){
						$("#sitemapGenerator .alert-success").hide();
						$("#sitemapGenerator .progress").hide();
						$("#sitemapGenerator  a.btn").show();
					},3000);
					$("body").removeClass("cursor-wait");
				} else {
					$("#sitemapGenerator").data("ready").push(url);
					var links=$.parseJSON(data);
					$.each(links,function(i,link){
						if (!in_array(link,$("#sitemapGenerator").data("ready")) && !in_array(link,$("#sitemapGenerator").data("stack"))) {
							$("#sitemapGenerator").data("stack").push(link);
						}
					});
				}
				if ($("#sitemapGenerator").data("stack").length) {
					var link=$("#sitemapGenerator").data("stack").pop();
					$("#sitemapGenerator .progress .current").html(url);
					moduleSitemapGeneratorAjax(link);
				} else {
					if (url!=="__finish__") {
						moduleSitemapGeneratorAjax("__finish__");
						$("#sitemapGenerator .progress .current").html("Финиш");
						$("#sitemapGenerator").data("ready","");
						$("#sitemapGenerator").data("stack","");
					}
				}

				
			},
			error: function(){
				$("#sitemapGenerator .alert-danger").show("fade");
				setTimeout(function(){
					$("#sitemapGenerator .alert-danger").hide();
					$("#sitemapGenerator .progress").hide();
					$("#contactForm .sendbutton a.btn").show();
				},3000);				
				$("body").removeClass("cursor-wait");
			}
		});	
		
}

moduleSitemapGenerator();