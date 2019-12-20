    <section id="{{_var.qid}}" data-name="Виджет - Задать вопрос">
        <!-- tpl/section.question.inc.php -->
        <div class="container">
            <h2>Задайте ваш вопрос</h2>
            <form class="question__border" data-wb-role="formdata" data-wb-json='{"phone"=>"","email"=>"","fullname"=>""}' data-wb-hide="wb">
                <div class="question__wrap flex">
                    <div class="question__right">
                        <meta data-wb-role="variable" var="mailto" data-wb-if='"{{_sett.email_question}}">""' value="{{_sett.email_question}}" else="{{_sett.email}}">
                        <input type="hidden" name="_subject" value="Вопрос с сайта">
                        <input type="hidden" name="_mailto" value="{{_var.mailto}}">
                        <input type="text" name="fullname" placeholder="Ваше Имя" required>
                        <input type="phone" data-mask="+7 (999) 999-99-99" name="phone" placeholder="Телефон" required>
                        <input type="email" name="email" placeholder="E-mail" required>
                    </div>
                    <div class="question__left">
                        <textarea placeholder="Комментарий"></textarea>
                        <div class="question__bottom flex">
                            <a href="javascript:void(0)" data-wb-ajax="/ajax/mail/" class="btn" style="cursor:pointer;">Отправить</a>
                            <p>Нажимая кнопку «Отправить», Вы соглашаетесь с обработкой персональных данных</p>
                        </div>
                    </div>

                </div>

            </form>


        </div>
    </section>

<meta data-wb-role="variable" var="qid" data-wb-if='"{{data.content.color}}">""' value="{{data.content.color}}" else="question">
<meta data-wb-role="select" data-wb-attr="id" value="{{_var.qid}}">
