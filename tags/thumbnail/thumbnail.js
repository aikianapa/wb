$(document).ready(function(){
  var wbThumbLazy = "/engine/tags/thumbnail/lazy.js";
  $(document).off('wbThumbLazy');
  $(document).on('wbThumbLazy',function(){
        $('[data-lazy]').Lazy({
         // your configuration goes here
         effect: 'fadeIn',
         threshold: 200,
         attribute: 'data-lazy',
         afterLoad: function(e) {
            var src = $(e).attr("src");
            $("[data-lazy='"+src+"']").attr("src",src).removeAttr("data-lazy");
         }
        });

  });
  if ($(document).data("wbThumbLazy") == undefined) {
      $.getScript(wbThumbLazy,function(){
          $(document).data("wbThumbLazy",true);
          $(document).trigger('wbThumbLazy');
      });
  } else {
      $(document).trigger('wbThumbLazy');
  }
});
