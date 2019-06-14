<meta data-wb-role="include" src="template" data-wb-name="wrapper.inc.php">

<div data-wb-append="#content" data-wb-hide="*">
            <section class="site-section site-content border-bottom overflow-hidden" style="margin-top:80px;">
                <div class="container" style="margin:0;padding:0; width:100%;">
					<div id="home-top" class="carousel slide" data-ride="carousel">
					  <!-- Indicators -->
					  <ol class="carousel-indicators" data-wb-role="foreach" data-wb-from="images" data-wb-tpl="false" data-wb-hide="wb">
						<li data-target="#home-top" data-slide-to="{{_idx}}"></li>
					  </ol>

					  <div class="carousel-inner" data-wb-role="foreach" data-wb-from="images" data-wb-tpl="false" data-wb-hide="wb">
						<div class="item center-block bg-white">
						  <img data-wb-role="thumbnail" data-wb-size="660px;350px;src" src="/uploads/pages/home/{{img}}" alt="">
						  <div class="carousel-caption">
								<div class="push text-center">
									<h1 class="animation-fadeInQuick2Inv"><strong>{{%lang[{{_SESS[lang]}}].name}}</strong></h1>
									<h2 class="text-light-op animation-fadeInQuickInv push-bit"><strong>{{%footer}}</strong></h2>
									<a href="https://github.com/aikianapa/wb/archive/master.zip" target="_blank" class="btn btn-lg btn-danger push-right-left">
										<i class="fa fa-download"></i>
										&nbsp;&nbsp;
										<strong>{{_LANG[download]}}</strong>
									</a>
								</div>
						  </div>
						</div>
					  </div>

					  <!-- Controls -->
					  <a class="left carousel-control" href="/#home-top" data-slide="prev">
						<span class="fa fa-chevron-left"></span>
					  </a>
					  <a class="right carousel-control" href="/#home-top" data-slide="next">
						<span class="fa fa-chevron-right"></span>
					  </a>
					</div>
                </div>
            </section>

<section class="site-content site-section-mini site-section-light themed-background-social">
<div class="container">
<h2 class="site-heading h3 site-block">
<i class="fa fa-fw fa-laptop"></i> {{_LANG[best_sales]}}
</h2>
</div>
</section>

			<section class="site-section site-content border-bottom overflow-hidden">
				<div class="container">
					<div class="row">
					<div class="col-sm-3" style="padding-top:45px;">
					<div class="categori-menu">
					<div class="sidebar-menu-title themed-background-social">
					<h2><i class="fa fa-th-list"></i>{{_LANG[categories]}}</h2>
					</div>
					<div class="sidebar-menu" data-wb-role="include" src="template" data-wb-name="catalog.inc.php"></div>
					</div>
					<br/>
					<div class="panel panel-info">
								<div class="panel-body">
								{{_LANG[adm_note]}}:<br/>
								<a href="{{_ENV[route][hostp]}}/login">{{_ENV[route][hostp]}}/login</a><br/>
								{{_LANG[login]}}: <b>admin</b><br/>
								{{_LANG[password]}}: <b>admin</b><br/>
								</div>
							</div>

					</div>
					<div class="col-sm-9">
						<div data-wb-role="include" src="template" data-wb-name="sales.inc.php" data-hide="*"></div>
					</div>
					</div>
					</div>
			</section>
                        <!-- Start categori menu -->
                        <!-- End categori menu -->


			<div data-wb-role="include" src="template" data-wb-name="news.inc.php" data-wb-hide="*"></div>

<section class="site-content site-section-mini site-section-light themed-background-warning">
<div class="container">
<h2 class="site-heading h3 site-block">
<i class="fa fa-fw fa-laptop"></i> {{_LANG[comfort]}}
</h2>
</div>
</section>

            <!-- Promo #3 -->
            <section class="site-section site-content border-bottom overflow-hidden">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-6 push" data-wb-role="formdata" data-wb-form="pages" data-wb-item="comfort" data-wb-hide="wb">
				{{lang[{{_SESS[lang]}}].data.text}}
                        </div>
                        <div class="col-sm-6 clearfix push overflow-hidden">
                            <img src="img/placeholders/screenshots/promo_3.png" alt="" class="img-responsive pull-right visibility-none" data-toggle="animation-appear" data-animation-class="animation-fadeInLeft" data-element-offset="-200" style="max-width: 450px; margin-right: -130px;">
                        </div>
                    </div>
                </div>
            </section>

</div>

