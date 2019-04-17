<!DOCTYPE html>
<!--[if IE 9]>
<html class="no-js lt-ie10">
<![endif]-->
<!--[if gt IE 9]>
<!-->
<html class="no-js">
<!--<![endif]-->
    <head>

        <meta charset="utf-8">
	<base href="/tpl/">
        <title>{{header}}</title>
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0">
        <!-- Icons -->
        <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
        <link rel="shortcut icon" href="img/favicon.png">

		<meta data-wb-role="variable" var="description" value="{{header}}">
		<meta data-wb-role="variable" var="description" value="{{meta_description}}" where='meta_description > ""'>
		<meta name="description" content="{{description}}">
		<meta data-wb-role="variable" var="keywords" value="{{meta_keywords}}">
		<meta data-wb-role="variable" var="keywords" value="{{tags}}" data-wb-where='tags > ""'>
		<meta name="keywords" content="{{keywords}}">

		<link data-wb-role="include" src="shortcode" data-wb-name="javascript->bootstrap3->css">
		<link data-wb-role="include" src="shortcode" data-wb-name="javascript->jquery">
		<link data-wb-role="include" src="shortcode" data-wb-name="fonts->fontawesome">
		<link rel="stylesheet" href="css/plugins.css">
		<link rel="stylesheet" href="css/main.css">
		<link rel="stylesheet" href="css/themes.css">
    </head>
    <body>
        <!-- 'boxed' class for a boxed layout -->
        <div id="page-container" class="no_boxed">
            <header>
                <div class="container">
                    <a href="/" class="site-logo wblogo col-sm-4 col-md-2 col-xs-6">
			    <i>WebBasic</i>
                    </a>
                    <nav data-wb-role="formdata" data-wb-form="pages" data-wb-item="mainmenu" data-wb-hide="wb">
                        <a href="javascript:void(0)" class="btn btn-default site-menu-toggle visible-xs visible-sm">{{_LANG[menu]}}</a>
                        {{lang[{{_SESS[lang]}}].data.text}}
                    </nav>
                </div>
	    </header>
		<div id="content">
		</div>
            <footer class="site-footer site-section site-section-light">

                <div class="container">
                    <!-- Footer Links -->
                    <div class="row">
                        <div class="col-sm-7">
                            <h4 class="footer-heading">
				2018 &copy; Created with <a href="http://wbcms.online">WebBasic CMS</a>
				<br>
				<i style="letter-spacing: 9px;font-weight: initial;word-spacing: 2px;font-size: 12px;">
					basic, simple, easy
				</i>
                            </h4>
                        </div>

                        <div class="col-sm-5 hidden-xs">
				<br>
				<a href="/" class="site-logo wblogo">
				<i>WebBasic</i>
				</a>
                        </div>
                    </div>
                    <!-- END Footer Links -->

				<div class="alert alert-danger navbar navbar-inverse navbar-fixed-bottom navbar-glass" style="margin-bottom:0;opacity:0.8;">
					<strong>{{_LANG[warning]}}!!!</strong> {{_LANG[warning_text]}}
				</div>
                </div>

	    </footer>
        </div>

        <!-- Scroll to top link, initialized in js/app.js - scrollToTop() -->
        <a href="#" id="to-top"><i class="fa fa-arrow-up"></i></a>
	<link data-wb-role="include" src="shortcode" data-wb-name="javascript->bootstrap3->js">
        <link data-wb-role="include" src="shortcode" data-wb-name="plugins">
        <link data-wb-role="include" src="shortcode" data-wb-name="plugins->imgviewer">
	<script src="/engine/js/wbengine.js"></script>
        <script src="js/plugins.js"></script>
        <script src="js/app.js"></script>
        <script src="js/scripts.js"></script>
    </body>
</html>



<script type="text/locale" src="locale"></script>
