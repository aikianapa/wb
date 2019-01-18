<meta data-wb-role="include" src="template" data-wb-name="wrapper.inc.php">

<div data-wb-append="#content" data-wb-hide="*">
            <section class="site-section site-section-top site-section-light themed-background-dark">
                <div class="container text-center">
                    <h1 class="animation-fadeInQuickInv"><strong>{{header}}</strong></h1>
                </div>
            </section>


<section class="site-content site-section-mini site-section-light themed-background-social">
	<div class="container">
	<h2 class="site-heading h3 site-block">
	<i class="fa fa-fw fa-laptop"></i> {{header}}
	</h2>
	</div>
</section>

			<section class="site-section site-content border-bottom shop-grid overflow-hidden">
				<div class="container">
				<div class="col-sm-3" style="padding-top:45px;">
				    <div class="categori-menu">
					<div class="sidebar-menu-title themed-background-social">
					    <h2><i class="fa fa-th-list"></i>{{_LANG[categories]}}</h2>
					</div>
					<div class="sidebar-menu" data-wb-role="include" src="template" data-wb-name="catalog.inc.php"></div>
				    </div>
				</div>
					<div class="col-sm-9">
						<div class="row">
						<div class="col-sm-12 row-items"
						data-wb-role="foreach" data-wb-form="products"
						data-wb-where='{{_where}}' data-wb-size="12"
						data-wb-sort="name" data-wb-step="3">
							<div class="col-sm-4" src="template" data-wb-role="include" data-wb-name="prod_item.inc.php" data-wb-hide="wb">
							</div>
						</div>
						</div>
					</div>
				</div>
			</section>
			<div data-wb-role="include" src="template" data-wb-name="news.inc.php" data-hide="*"></div>
</div>

