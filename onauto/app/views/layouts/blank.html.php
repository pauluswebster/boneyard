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
	<header id="header" class="page-header"></header>
	<section class="container" id="content">
		<?php echo $this->flashMessage->output(null, array('options' => array()));?>
		<?php echo $this->content(); ?>
	</section>
	<br><br>
	<footer id="footer"></footer>
	<?php echo $this->element->render('scripts');?>
</body>
</html>