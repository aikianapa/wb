<meta data-wb-role="include" src="template" data-wb-name="wrapper.inc.php">

<div data-wb-append="#content" data-wb-hide="*">
            <section class="site-section site-section-top site-section-light themed-background-dark">
                <div class="container text-center">
                    <h1 class="animation-fadeInQuickInv"><strong>{{lang[{{_SESS[lang]}}].name}}</strong></h1>
                </div>
            </section>

		<section class="site-section site-content">
			<div class="container">
				<div class="row">
					<div class="col-sm-8 col-sm-push-4 push" >
						<div class="media">
						  <div class="media-body text-justify">
						    <h4 class="media-heading">{{date("d.m.Y",strtotime("{{date}}"))}} {{lang[{{_SESS.lang}}].name}}</h4>
						     {{lang[{{_SESS.lang}}].data.text}}
						     {{text}}
						  </div>
						</div>
					</div>
					<div class="col-sm-4 col-sm-pull-8 clearfix push overflow-hidden">
						<img src="{{_image}}" alt="" class="img-responsive pull-left visibility-none" data-toggle="animation-appear" data-animation-class="animation-fadeInRight" data-element-offset="-200">
					</div>
				</div>
			</div>
		</section>

</div>
