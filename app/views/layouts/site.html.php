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
  	<link rel="stylesheet" href="css/styles.css">
  	<!--link rel="stylesheet/less" type="text/css" media="screen" href="css/less/styles.less">
  	<script src="theme/js/libs/less-1.1.5.min.js"></script-->	
  	<!-- /styles -->
  	
  	<!-- scripts -->
	<script src='theme/js/libs/modernizr.custom.07036.js'></script>
	<script>
	yepnope({
		load: ['http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js', 'js/scripts.js']
	});
	</script>
	<!-- /scripts -->
</head>
<body class='page-sidebar-right'>
    <header class='container-block' id='page-header'>
    <div class='container-inner'>
      <div class='container-aligner'>
        <h1>
        	<img src="img/badge-trans.png" />
            Progression Media &amp; Development
        </h1>
      </div>
    </div>
  </header>
  
  <!-- Page body -->
  <section class='container-block' id='page-body'>
    <div class='container-inner'>
      <!-- Page title -->
      <header class='container-aligner' id='page-title'>
        <!-- Title and summary -->
        <hgroup id='title-summary'>
          <h2>Quailty web development, design &amp; consultation services.</h2>
          <h4>Wanaka, New Zealand, Worldwide</h4>
        </hgroup>
        <!-- End - Title and summary -->
        <!-- Title right side -->
        <section class='title-right'>
                <a href='#contact' title='Contact p.m.d. now!'>
                  <span>Contact p.m.d. now!</span>
                </a>
              </section>
        <!-- End - Title right side -->
      </header>
      <!-- End - Page title -->
      <!-- Page body content -->
      <section id='page-body-content'>
        <div id='page-body-content-inner'>
          <!-- Page content -->
                <div id='page-content'>
                  <p>Progression Media &amp; Development (p.m.d.) provides website design, web development,
                  internet consultation, website management and a broad spectrum of web services.</p>
                  <p>p.m.d. is <a href="http://www.webprogression.co.nz" target="_blank" class="wp">Paul Webster, web developer &amp; consultant</a>,
                  projects are undertaken in-house and in conjunction with outsourced contributors as and where required.</p>
                  <p><a href="#contact">Contact p.m.d.</a> today to create, manage & maintain all aspects of your web presence including:</p>
                  <ul class='list-star'>
                    <li>Website design &amp; construction</li>
                    <li>Web application development</li>
                    <li>Search engine optimization &amp; search engine marketing (SEO/SEM)</li>
                    <li>Social media strategy &amp; integration</li>
                    <li>Provisioning of web hosting, domain names &amp; email services</li>
                    <li>General consultancy on how you can best do business online</li>
                  </ul>
                  <p>Any and all related enquiries are also welcome.</p>
                  <hr>
                  
                  <a name="contact"></a>
                  <section class='contact-info'>
                    <h2 class='underlined-header'>Contact p.m.d.</h2>
                    <p>Please provide your details below and I'll endeavour to be in touch same day
                    on weekdays, or the next business day on weekends etc.</p>
                    <form action='contact.php' id='ContactForm' method='post'>
                      <div class="input">
                        <label for='name'>Name *</label>
                        <input id='name' name='name' placeholder='Enter your name...' required='required' title='Name' type='text' />
                      </div>
                      <div class="input">
                        <label for='email'>Email *</label>
                        <input id='email' name='email' placeholder='Email address...' required='required' title='Email address' type='email' />
                      </div>
                      <div class="input">
                        <label for='subject'>Subject *</label>
                        <input id='subject' name='subject' placeholder='Specify subject...' required='required' title='Subject' type='text' />
                      </div>
                      <div class="input">
                        <label for='message'>Message *</label>
                        <textarea id='message' name='message' placeholder='Message text...' required='required' rows='10' title='Message text'></textarea>
                      </div>
                      <div class="box-message box-info">
						 <div class="box-content">
						   <p>Sending....</p>
						 </div>
					  </div>
                      <div class="box-message box-success">
						 <div class="box-content">
						   <p>Thanks for your enquiry, I'll get back to you as soon as possible!</p>
						 </div>
					  </div>
					  <div class="box-message box-warning">
						 <div class="box-content">
						   <p>Please check all fields, there was an issue with your submission.</p>
						 </div>
					  </div>
					  <div class="box-message box-error">
						 <div class="box-content">
						   <p>Hmmm... there was an issue sending that message, please try again.</p>
						 </div>
					  </div>
                      <div class="submit">
                        <input type='submit' value='Send message' class="purple" disabled="disbaled" />
                      </div>
                      <br>
                      <?php 
                    	$tz = new \DateTimeZone('Pacific/Auckland');
						$date = new \DateTime(null, $tz);
                      ?>
                      <p class="color-purple"><b>Note:</b> <i>p.m.d. is New Zealand based, our timezone (currently <?php echo $date->format('l h:ia');?>) is a bit out
                      of whack with the northern part of the world, so please excuse any delays this may cause.</i></p>
                      </form>
                  </section>
                  <hr>
                </div>
                <!-- End - Page content -->
                <!-- Page sidebar -->
                <aside class='page-sidebar'>

                  <section class='widget-container widget-text'>
                  	<h2 class='widget-heading'>p.m.d. recommends:</h2>
                  	<div class='widget-content recommends'>
                  		<a href="recommends/servergrove" title="Server Grove: VPS hosting">
                  			<img src="img/sg.png" width="200" />
                  		</a>
                  		<div class="clear"></div>
                  		<a href="recommends/hostingdirect" title="Hosting Direct: web hosting & domain name services (NZ)">
                  			<img src="img/hd.png" width="200" />
                  		</a>
                  		<div class="clear"></div>
                  		<a href="recommends/traffictravis" title="Traffic Travis: desktop seo software">
                  			<img src="img/tt.png" width="100" />
                  		</a>
                  		<a href="recommends/gittower" title="Tower: The most powerful Git client for Mac">
                  			<img src="img/tower.png" width="80" />
                  		</a>
                  		<div class="clear"></div>
                  	</div>
                  </section>
                  
                </aside>
                <!-- End - Page sidebar -->
        </div>
      </section>
      <!-- End - Page body content -->
    </div>
  </section>
  <!-- End - Page body -->
  <!-- Bottom widgets -->
  <section class='container-block dark-skin' id='bottom-widgets'>
    <div class='container-inner'>
      <div class='container-aligner'>
        <div class='layout-1-4'>
        	&nbsp;
        </div>
        <div class='layout-2-4'>
        	&nbsp;
        </div>
        
        <!-- Widgets column -->
        <div class='layout-1-4 layout-last'>
          &nbsp;
        </div>
        <!-- End - Widgets column -->
      </div>
    </div>
  </section>
  <!-- End - Bottom widgets -->
  <!-- Page footer -->
  <footer id='page-footer'>
    <div class='container-aligner'>
      <!-- Footer left -->
      <section id='footer-left'>
      	 &copy; Copyright <?php echo date('Y');?> Progression Media &amp; Development / Paul Webster
      </section>
      <!-- End - Footer left -->
    </div>
  </footer>
  <!-- End - Page footer -->
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