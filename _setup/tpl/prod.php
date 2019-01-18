<meta data-wb-role="include" src="template" data-wb-name="wrapper.inc.php">

<div data-wb-append="#content" data-wb-hide="*">

            <section class="site-section site-section-top site-section-light themed-background-dark">
                <div class="container text-center">
                    <h1 class="animation-fadeInQuickInv"><strong>{{name}}</strong></h1>
                </div>
            </section>

            <section class="site-content site-section">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 site-block">
                            <div class="row">
                                <div class="col-sm-5">
                                    <div id="project-carousel" class="carousel slide" data-ride="carousel" data-interval="4000">
                                        <div class="carousel-inner text-center" data-wb-role="foreach" data-wb-from="images" data-wb-tpl="false" data-wb-hide="wb">
                                            <div class="item">
                                                <img data-wb-role="thumbnail" data-wb-size="500px;500px;src" data-wb-contain="true" class="img-responsive" src="/uploads/{{%_table}}/{{%id}}/{{img}}" alt="{{%name}}">
                                            </div>
                                        </div>

                                        <a class="left carousel-control" href="#project-carousel" data-slide="prev" role="button">
                                            <span><i class="fa fa-chevron-left"></i></span>
                                        </a>
                                        <a class="right carousel-control" href="#project-carousel" data-slide="next" role="button">
                                            <span><i class="fa fa-chevron-right"></i></span>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-sm-7">
					<h3>
						<strong data-wb-where='"{{lang[{{_SESS[lang]}}].name}}" == ""' data-wb-hide="wb">{{name}}</strong>
						<strong data-wb-where='"{{lang[{{_SESS[lang]}}].name}}" != ""' data-wb-hide="wb">{{lang[{{_SESS[lang]}}].name}}</strong>
					</h3>
					<p>{{lang[{{_SESS[lang]}}].data.text}}</p>

					<table class="table table-stripped" data-wb-where='property > "" '>
						<tbody data-wb-role="foreach" data-wb-from="property" data-wb-tpl="false" data-wb-hide="wb">
							<tr>
								<td data-wb-where='"{{prop->substr(@,0,1)}}" == "_" ' data-wb-hide="wb">{{_LANG[{{prop}}]}}</td>
								<td data-wb-where='"{{prop->substr(@,0,1)}}" != "_" ' data-wb-hide="wb">{{prop}}</td>
								<td>{{value}}</td>
							</tr>
						</tbody>
					</table>

					<p><strong>{{_LANG[price]}}: {{price}}</strong></p>

					<form data-wb-role="cart">
						<input type="hidden" name="id" value="{{id}}">
						<input type="hidden" name="quant" value="1">
						<input type="hidden" name="form" value="{{_form}}">
						<input type="hidden" name="price" value="{{price}}">
						<a class="btn btn-success add-to-cart" href="javascript:false;">
                                                        <i class="fa fa-cart-plus"></i>&nbsp;&nbsp; {{_LANG[add_to_cart]}}
						</a>
					</form>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- END Project Info -->

<section class="site-content site-section-mini site-section-light themed-background-social">
	<div class="container">
		<h2 class="site-heading h3 site-block">
		<i class="fa fa-fw fa-comments"></i> {{_LANG[comments]}}
		</h2>
	</div>
</section>

<section class="site-content site-section border-bottom">
	<div class="container">
		<div class="row">
			<div data-wb-role="include" src="form" data-wb-name="comments_widget" class="content"></div>
		</div>
	</div>
</section>


<script type="text/locale">
[eng]
	_cpu		= "CPU"
	_display	= "Display"
	_material	= "Drum Material"

[rus]
	_cpu		= "Процессор"
	_display	= "Дисплей"
	_material	= "Материал барабана"
</script>
</div>
