<div id="wb_cookies_alert" class="alerf alert-info" data-wb-where='"{{_COOK.wbcookies}}"="" AND "{{_SESS.user.wbcookies}}"=""' data-wb-hide="wb">
	<button class="btn btn-success" onClick="var cookdate = new Date(new Date().getTime() + 60 * 1000 * 60 * 60 *24 *365 ); document.cookie = 'wbcookies = true; path=/; expires=\"' + cookdate + '\"'; $('#wb_cookies_alert').remove();">{{_LANG.wb_cook_accept}}</button>
	<p>{{_LANG.wb_cook_alert}}</p>
	<style>
		#wb_cookies_alert {
			position:absolute;
			display:block;
			width:100%;
			height:auto;
			top:0;
			left:0;
			padding:15px;
			z-index:99999;
		}
		#wb_cookies_alert p {margin:0; padding:0; height: 50px; display: table-cell; vertical-align:middle;}
		#wb_cookies_alert button {position:relative;float:right;}
	</style>
<script type="text/locale">
[eng]
	wb_cook_alert	= "This site uses cookies, as explained in our privacy policy. If you use this site without adjusting your cookie settings, you agree to our use of cookies!"
	wb_cook_accept	= "Accept"
[rus]
	wb_cook_alert	= "Этот сайт использует файлы cookies, как описано в нашей политике конфиденциальности. Если вы используете этот сайт без настройки параметров cookies, вы соглашаетесь с использованием файлов cookies!"
	wb_cook_accept	= "Принимаю"
</script>
</div>
