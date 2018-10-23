<div id="sitemapGenerator">
    <div class="row">
        <div class="col">
            <h6 class="element-header">
            {{_LANG[generator]}}</h6>
            <p><span class="text-warning text-uppercase">{{_LANG[warning]}}!!!</span>
                <br>{{_LANG[message]}}</p>
            <hr>
            <form class="form-horizontal">
                <div class="form-group row">
                    <label class="col-12 form-control-label">{{_LANG[site]}}:</label>
                    <div class="input-group col-sm-6"> <span class="input-group-addon">{{_ENV[route][scheme]}}://</span>
                        <input type="text" class="form-control text-right" name="sub" placeholder="{{_LANG[subdomain]}}"> <span class="input-group-addon bg-white">{{_ENV[route][host]}}</span> </div>
                    <div class="input-group col-sm-4"> <a href="#" class="btn btn-secondary"><i class="fa fa-gear"></i> {{_LANG[start]}}</a> </div>
                </div>
            </form>
            <div class="progress col hidden">
                <div class="col-2">
                <div class="sk-wave">
                    <div class="sk-rect sk-rect1 bg-gray-800"></div>
                    <div class="sk-rect sk-rect2 bg-gray-800"></div>
                    <div class="sk-rect sk-rect3 bg-gray-800"></div>
                    <div class="sk-rect sk-rect4 bg-gray-800"></div>
                    <div class="sk-rect sk-rect5 bg-gray-800"></div>
                </div>
                </div>
                <div class="col-auto align-middle">
                    <div class="text-info current mg-t-50"></div>
                </div>
            </div>
            <div class="mg-t-20 alert alert-success hidden">{{_LANG[success]}}</div>
            <div class="mg-t-20 alert alert-danger hidden">{{_LANG[error]}}</div>
        </div>
    </div>
    <script language="javascript" src="/engine/modules/sitemap/sitemap.js"></script>
</div>

<script type="text/locale">
[eng]
        warning         = "Warning"
        start           = "Start"
        site            = "Site address"
        subdomain       = "Subdomain (not mandatory)"
        message         = "Sitemap generation may take a long time, please do not interrupt the process."
        generator       = "Sitemap generator (sitemap.xml)"
        success         = "sitemap.xml generation complete!"
        error           = "Ops! Something went wrong, sitemap.xml could not be generated!"
[rus]
        warning         = "Внимание"
        start           = "Старт"
        site            = "Адрес сайта"
        subdomain       = "Поддомен (не обязательно)"
        message         = "Генерация может занять продолжительное время, пожалуйста, не прерывайте процесс."
        generator       = "Генератор катры сайта (sitemap.xml)"
        success         = "Генерация sitemap.xml выполнена успешно!"
        error           = "Упс! Что-то пошло не так, sitemap.xml не удалось сгенерировать!"
</script>
