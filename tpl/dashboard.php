<div class="kt-sideleft" data-wb-role="formdata" data-wb-form="users" data-wb-form="users" data-wb-item="{{_SESS[user_role]}}" data-wb-hide="*">
        <div data-wb-role="tree" data-wb-from="roleprop" data-wb-branch="dashboard" data-wb-hide="*" data-wb-parent="false" data-wb-limit="1">

        <div class="col-12 col-sm-3 col-lg-4 mg-b-20">
        <div class="card">
          <div class="card-body">
            <meta role="variable" var="target" value=".content-box" data-wb-where='"{{data[ajax]}}">""'>
            <meta role="variable" var="target" value='{{data[target]}}' data-wb-where='"{{data[ajax]}}">"" AND "{{data[target]}}">""'>
            <meta role="variable" var="target" value="" data-wb-where='"{{data[url]}}">""'>

            <h5 class="card-title hidden-ovf">{{name}}</h5>
            <p class="card-text"></p>
            <a href tabindex data-wb-ajax="{{data[ajax]}}" data-wb-html="{{_VAR[target]}}" class="btn btn-sm btn-primary" role="where" data='"{{data[ajax]}}">""'>Перейти</a>
            <a href="{{data[url]}}" tabindex target="{{_VAR[target]}}" class="btn btn-sm btn-primary tx-white" role="where" data='"{{data[url]}}">""'>Перейти</a>
            <div class="card-icon"><i class="{{data[icon]}}"></i></div>
          </div>
        </div>
        </div>
        </div>
</div>
