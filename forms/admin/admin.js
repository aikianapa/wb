$(document).ready(function () {
    $(document).undelegate("#adminMain select[name=merchant]","change");
    $(document).delegate("#adminMain select[name=merchant]","click", function () {
        $(this).parents(".form-group").find("a[data-wb-ajax]").attr("data-wb-ajax","/module/"+$(this).val()+"/settings");
    });


});
