<section class="site-content site-section-mini site-section-light themed-background-danger">
	<div class="container">
	<h2 class="site-heading h3 site-block">
	<i class="fa fa-fw fa-code"></i> {{_LANG[news]}}
	</h2>
	</div>
</section>

<section class="site-section site-content border-bottom overflow-hidden">
	<div class="container">
		<div class="row">
			<div class="col-sm-8 col-sm-push-4 push" data-wb-where='active = "on"'
			data-wb-role="foreach" data-wb-form="news" data-wb-sort="date:d" data-wb-limit="2" data-wb-where='home="on"'>


<div class="media">
  <div class="media-left media-middle">
    <a href="/news/{{id}}" class="thumbnail">
		<img class="media-object" data-wb-role="thumbnail" data-wb-size="150px;150px;src" src="0" style="max-width:150px;">
    </a>
  </div>
  <div class="media-body text-justify">
	<h4 class="media-heading">
		<a href="/news/{{id}}">
		{{date("d.m.Y",strtotime("{{date}}"))}} {{lang[{{_SESS.lang}}].name}}
		</a>
	</h4>
    {{lang[{{_SESS.lang}}].data.text->wbGetWords(@,90)}}
  </div>
</div>

			</div>
			<div class="col-sm-4 col-sm-pull-8 clearfix push overflow-hidden">
				<img src="img/placeholders/screenshots/promo_2.png" alt="" class="img-responsive pull-left visibility-none" data-toggle="animation-appear" data-animation-class="animation-fadeInRight" data-element-offset="-200" style="max-width: 450px; margin-left: -130px;">
			</div>
		</div>
	</div>
</section>
