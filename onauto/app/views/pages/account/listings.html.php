<div class="row">
	<div class="span3">
		<?php echo $this->element->render('layout/sidebar', array('sidebar' => array('account')));?>
	</div>
	<div class="span9">
		<ul class="breadcrumb">
			<li><a href="/pages/account">MyAuto</a><span class="divider">/</span></li>
			<li class="active">My Listings</li>
		</ul>
		<?php echo $this->element->render('listings', array('layout' => 'small', 'split' => true));?>
	</div>
</div>