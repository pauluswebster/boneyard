<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Application &gt; <?php echo $this->title(); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php echo $this->html->style('app'); ?>
	<?php echo $this->html->script('modernizr.min'); ?>
	<?php echo $this->scripts(); ?>
	<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>
</head>
<body class="container" id="container">
	<header id="header" class="page-header">
		<h1><a href="/">OnAuto</a></h1>
		<h3>Wholesale Vehicle Classifieds</h3>
	</header>
	<section class="container" id="content">
		<?php if ($this->request()->url != '/'):?>
			<?php echo $this->element->render('layout/user-nav');?>
		<?php endif;?>
		<?php echo $this->flashMessage->output(null, array('options' => array()));?>
		<?php echo $this->content(); ?>
	</section>
	<br><br>
	<footer id="footer">
		<hr>
		&copy; <?php echo date('Y')?>
	</footer>
	<script>
		yepnope({
			load: [
				'/js/jquery.min.js',
//				'/js/bootstrap/bootstrap-transition.js',
				'/js/bootstrap/bootstrap-alert.js',
//				'/js/bootstrap/bootstrap-modal.js',
//				'/js/bootstrap/bootstrap-dropdown.js',
//				'/js/bootstrap/bootstrap-scrollspy.js',
				'/js/bootstrap/bootstrap-tab.js',
//				'/js/bootstrap/bootstrap-tooltip.js',
//				'/js/bootstrap/bootstrap-popover.js',
//				'/js/bootstrap/bootstrap-button.js',
//				'/js/bootstrap/bootstrap-collapse.js',
//				'/js/bootstrap/bootstrap-carousel.js',
//				'/js/bootstrap/bootstrap-typeahead.js',
				'/js/app.js'
			]
		});
	</script>
	<?php echo $this->element->render('debug/connection');?>
</body>
</html>