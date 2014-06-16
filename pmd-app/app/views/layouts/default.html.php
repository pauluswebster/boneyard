<!DOCTYPE html>
<!--[if IE 7 ]> <html lang="en" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]> <html lang="en" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]> <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]> <!--> <html class="no-js" lang="en"> <!-- <![endif]-->
<head>
	<meta charset='utf-8' />
	<!--[if IE ]> <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" /> <![endif]-->
	<meta content='web development' name='keywords' />
	<meta content='Progression Media & Development' name='description' />
	<meta content='Paul Webster' name='author' />
 	<title>Progression Media & Development</title>
 	<link href='favicon.ico' rel='shortcut icon' />
	
	<!--  styles -->
	<link rel="stylesheet" href="/css/styles.css">
	<!--link rel="stylesheet/less" type="text/css" media="screen" href="/css/less/styles.less">
	<script src="theme/js/libs/less-1.1.5.min.js"></script-->	
	<!-- /styles -->
	
	<!-- scripts -->
	<script src='theme/js/libs/modernizr.custom.07036.js'></script>
	<script>
	yepnope({
		load: ['http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js', '/js/site.js']
	});
	</script>
	<!-- /scripts -->
</head>
<body class='page-sidebar-right'>
	<header class='container-block' id='page-header'>
		<div class='container-inner'>
			<div class='container-aligner'>
				<h1>
					<img src="/img/site/badge-trans.png" />
	            	Progression Media &amp; Development
	        	</h1>
			</div>
		</div>
	</header>
	<section class='container-block' id='page-body'>
		<div class='container-inner'>
			<header class='container-aligner' id='page-title'>
				<hgroup id='title-summary'>
					<h2>Quailty web development, design &amp; consultation services.</h2>
					<h4>Wanaka, New Zealand, Worldwide</h4>
				</hgroup>
				<section class='title-right'>
					<a href='/#contact' title='Contact p.m.d. now!'>
						<span>Contact p.m.d. now!</span>
					</a>
				</section>
			</header>
			<section id='page-body-content'>
				<div id='page-body-content-inner'>
                	<?php echo $this->content(); ?>
				</div>
			</section>
		</div>
	</section>
	<section class='container-block dark-skin' id='bottom-widgets'>
	    <div class='container-inner'>
			<div class='container-aligner'>
		        <div class='layout-1-4'>
		      	&nbsp;
		        </div>
		        <div class='layout-2-4'>
		      	&nbsp;
		        </div>
		        <div class='layout-1-4 layout-last'>
		          &nbsp;
		        </div>
			</div>
		</div>
	</section>
	<footer id='page-footer'>
		<div class='container-aligner'>
			<section id='footer-left'>
				&copy; Copyright <?php echo date('Y');?> Progression Media &amp; Development / Paul Webster
			</section>
		</div>
	</footer>
	<div id="dark-overlay"></div>
	<div id="body-texture"></div>
	<p id='jsnotice'>Javascript is currently disabled. This site requires Javascript to function correctly. Please <a href="http://enable-javascript.com/">enable Javascript in your browser</a>!</p>
	<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-10534959-3']);
		_gaq.push(['_trackPageview']);
		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
  </script>
</body>
</html>