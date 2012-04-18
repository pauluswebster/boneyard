<div class="row">
	<div class="span3">
		<?php echo $this->element->render('layout/sidebar', array('sidebar' => array('search', 'actions')));?>
	</div>
	<div class="span9">
		<ul class="breadcrumb">
		  <li class="active">Listings</li>
		</ul>
		<?php echo $this->element->render('listings');?>
	</div>
</div>