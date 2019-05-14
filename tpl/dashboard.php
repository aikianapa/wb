<div data-wb-role="tree" data-wb-call="wbGetUserUi" data-wb-branch="dashboard" data-wb-hide="*" data-wb-parent="false" data-wb-limit="1">
<div class="col-12 col-sm-3 col-lg-4 mg-b-20" data-wb-where='"{{data[visible]}}" = "on"'>
<meta data-wb-role="variable" var="dash_card_name" data-wb-if='"{{data[lang][{{_SESS[lang]}}][name]}}" > ""' value="{{data[lang][{{_SESS[lang]}}][name]}}" else="{{name}}">
<div class="card">
  <div class="card-body">
    <meta role="variable" var="target" value=".content-box" data-wb-where='"{{data[ajax]}}">""'>
    <meta role="variable" var="target" value='{{data[target]}}' data-wb-where='"{{data[ajax]}}">"" AND "{{data[target]}}">""'>
	<meta role="variable" var="target" value="" data-wb-where='"{{data[url]}}">""'>
    <meta role="variable" var="target" value='{{data[target]}}' data-wb-where='"{{data[url]}}">"" AND "{{data[target]}}">""'>

    <h5 class="card-title hidden-ovf">{{_VAR[dash_card_name]}}</h5>
    <p class="card-text"></p>
        <a href tabindex data-wb-ajax="{{data[ajax]}}" data-wb-html="{{_VAR[target]}}" class="btn btn-sm btn-primary" role="where" data='"{{data[ajax]}}">""'>
                <i class="fa fa-list"></i> &nbsp; {{_LANG[goto]}}
        </a>
        <a href="{{data[url]}}" tabindex target="{{_VAR[target]}}" class="btn btn-sm btn-primary tx-white" role="where" data='"{{data[url]}}">""'>
                <i class="fa fa-list"></i> &nbsp; {{_LANG[goto]}}
        </a>
    <div class="card-icon"><i class="{{data[icon]}}"></i></div>
  </div>
</div>
</div>
</div>


<script type="text/locale">
[rus]
        goto    = "Перейти"
[eng]
        goto    = "View"
</script>
