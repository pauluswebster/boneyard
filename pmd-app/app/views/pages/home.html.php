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
		<form action='/contact' id='ContactForm' method='post'>
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
<?php echo $this->_render('element', 'layout/sidebar', array('sidebar' => 'recommends'))?>