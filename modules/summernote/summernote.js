wbapp.scriptWait("/engine/modules/summernote/dist/summernote-bs4.min.js",[],function(){
    wb_include("/engine/modules/summernote/dist/summernote-bs4.css");
    
    function start(that,lang) {
        $(that).summernote({
            height: 250,
            lang: lang,
            callbacks: {
                onChange: function(contents, $editable) {
                    $(that).html(contents);
                    $(that).parents("form").trigger("change");
                    //console.log('onChange:', contents);
                }
            }
        });
        $(that).data("wb-loaded",true);
    }

    
    $('.summernote').each(function(){
        var that = this;
        if ($(that).data("wb-loaded") == undefined) {
            var lang = wbapp.settings.i18n;
            if (lang == "en-EN") {
                start(that,"");
            } else {
                wbapp.scriptWait("/engine/modules/summernote/dist/lang/summernote-"+lang+".js",[],function(){
                    start(that,lang);
                });
            }
        }        
    });     
});
