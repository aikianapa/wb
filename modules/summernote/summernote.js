wbapp.scriptWait("/engine/modules/summernote/dist/summernote-bs4.min.js",[],function(){
    wb_include("/engine/modules/summernote/dist/summernote-bs4.css");
    
    function start(that,lang) {
        if ($(that).attr("data-height") !== undefined) {
            var height = parseInt($(that).attr("data-height"));
        } else {
            var height = 200;
        }
        
        if ($(that).attr("data-toolbar") == undefined) {
              var toolbar = [
                // [groupName, [list of button]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']]
              ];
                toolbar = undefined;
            } else {
                var toolbar = $(that).attr("data-toolbar");
                toolbar = eval("toolbar = "+toolbar);
            }
        
        $(that).summernote({
            height: height,
            lang: lang,
            toolbar: toolbar,
            callbacks: {
                onChange: function(contents, $editable) {
                    setTimeout(function(){
                        $(that).html(contents);
                        $(that).parents("form").trigger("change");
                    },50)
                    //console.log('onChange:', contents);
                },
                onInit: function() {
                    var id = wbapp.newId();
                    $(that).attr("data-id",id);
                    if ($(that).parents(".modal").length) {
                        $(that).parents(".modal").on("hide.bs.modal",function(){
                            $(that).summernote('destroy');
                        });
                    }
                    console.log('Summernote is launched');
                }
            }
        });
        $(that).data("wb-loaded",true);
    }

    
    
    function init() {
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
    }
    
    init();
    
    $(document).on("multiinput_after_add",function(){init();});
});
