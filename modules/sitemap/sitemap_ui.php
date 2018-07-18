<div id="sitemapGenerator">
    <div class="row">
        <div class="col">
            <h6 class="element-header">
            Генератор sitemap.xml</h6>
            <p><span class="text-warning">ВНИМАНИЕ!!!</span>
                <br>Генерация может занять продолжительное время, пожалуйста, не прерывайте процесс.</p>
            <hr>
            <form class="form-horizontal">
                <div class="form-group row">
                    <label class="col-12 form-control-label">Адрес сайта:</label>
                    <div class="input-group col-sm-6"> <span class="input-group-addon">{{_ENV[route][scheme]}}://</span>
                        <input type="text" class="form-control text-right" name="sub" placeholder="Поддомен (не обязательно)"> <span class="input-group-addon bg-white">{{_ENV[route][host]}}</span> </div>
                    <div class="input-group col-sm-4"> <a href="#" class="btn btn-secondary"><i class="fa fa-gear"></i> Старт</a> </div>
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
            <div class="mg-t-20 alert alert-success hidden">Генерация sitemap.xml выполнена успешно!</div>
            <div class="mg-t-20 alert alert-danger hidden">Упс! Что-то пошло не так, sitemap.xml не удалось сгенерировать!</div>
        </div>
    </div>
    <script language="javascript" src="/engine/modules/sitemap/sitemap.js"></script>
</div>