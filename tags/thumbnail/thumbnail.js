$(document).ready(function(){
      $.getScript("/engine/tags/thumbnail/lazy.js",function(){
        $("[data-lazy]").lazy({
          'attribute':'data-lazy',
          'threshold':250
        });        
      });
});
