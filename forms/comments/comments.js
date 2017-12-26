$(document).ready(function(){
	function commens_widget() {
		if ($("#commentsWidget").length) {
		$("#commentsWidget #commentsEditForm").attr("item","_new");
		$("#commentsWidget .alert-success").hide().removeClass("hidden");
		$("#commentsWidget .alert-danger").hide().removeClass("hidden");
		$("#commentsWidget .sendbutton").hide().removeClass("hidden");
		$("#commentsWidget .norobot input").click(function(){
			$("#commentsWidget .norobot input").attr("disabled",true);
			$("#commentsWidget .sendbutton").show("fade");
		});
		$("#commentsWidget textarea[name=text]").val("");
		$(document).on("wb_required_true",function(event){
			//$("#contactForm .sendbutton a.btn").hide("fade");
			$(document).on("comments_after_formsave",function(event,name,item,form,ret){
			$("#commentsWidget #commentsEditInc").hide();
			if (ret==true) {
				$("#commentsWidget .alert-success").show("fade");
			} else {
				$("#commentsWidget .alert-danger").show("fade");
				setTimeout(function(){
					$("#commentsWidget .alert-danger").hide();
					$("#commentsWidget #commentsEditInc").show("fade");
					$("#commentsWidget .sendbutton a.btn").show();
				},3000);
			}
			});
		});
		}
	}
	commens_widget();
});
