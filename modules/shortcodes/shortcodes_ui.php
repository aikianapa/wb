<div id="moduleShortcodes">
                        <h6 class="element-header">
                                <br>{{_LANG[header]}}
                                <button type="button" class="btn btn-primary pull-right" data-wb-formsave="#EditShortcodes">
                                            <i class="fa fa-save"></i> {{_LANG[save]}}
                                </button>
                        </h6>
        <div class="row">
                <div class="col">
                        <form id="EditShortcodes" data-wb-form="admin" data-wb-item="shortcodes">
                                        <div class="row">
                                                <div class="col">
                                                <div class="card cart-danger p-1">
                                                        <h6 class="card-title p-2 m-0">{{_LANG[card_title]}}</h6>
                                                        <div class="card-body pt-3 pb-3">
                                                                &lt;link data-wb-role="include" src="shortcode" data-wb-name="fonts->font-awesome"&gt;
                                                                <hr>
                                                                &lt;link data-wb-role="include" src="shortcode" data-wb-name="javascript->bootstrap4->js"&gt;
                                                                <hr>
                                                                &lt;script data-wb-role="include" src="shortcode" data-wb-name="javascript->plugins->somecode"&gt;&lt;/script&gt;
                                                        </div>
                                                </div>
                                                </div>
                                        </div>
<hr>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div data-wb-role="tree" name="shortcode"></div>
                                        </div>
                                    </div>
                        </form>
                </div>
        </div>
</div>
<script type="text/locale">
[eng]
header		= "Shortcodes"
card_title	= "Sample to use shortcode"
save            = "Save"
[rus]
header		= "Вставки кода"
card_title	= "Как использовать shortcode"
save            = "Сохранить"
</script>
