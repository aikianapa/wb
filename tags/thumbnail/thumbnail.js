$(document).ready(function(){
  setTimeout(function(){
    $("img[data-lazy]").each(function(){
        $(this).attr("src",$(this).attr("data-lazy")).removeAttr("data-lazy");
    });
  },200);
});
