<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Centrifuge &gt; <?php echo $this->title(); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php echo $this->html->style(array('/sli_bootstrap/css/app', '/centrifuge/css/app')); ?>
	<?php echo $this->styles(); ?>
	<?php echo $this->head(); ?>
	<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>
	<?php $this->_render('element', 'scripts', array(), array('library' => 'sli_bootstrap'));?>
	<?php echo $this->scripts(); ?>
</head>
<body class="container" id="container">
	<header id="header" class="page-header">
		<h1>Centrifuge &gt; <?php echo $this->title(); ?></h1>
	</header>
	<section class="container" id="content">
		<?php echo $this->_render('element', 'scaffold_menu', array(), array('library' => 'sli_bootstrap'));?>
		<?php echo $this->_render('element', 'scaffold_breadcrumbs', array(), array('library' => 'sli_bootstrap'));?>
		<?php echo $this->flashMessage->output(null, array('options' => array('library' => 'sli_bootstrap')));?>
		<?php echo $this->content(); ?>
	</section>
	<br><br>
	<footer id="footer">
		<hr>
	</footer>
	<?php echo $this->_render('element', 'debug/connection', array(), array('library' => 'centrifuge'));?>
</body>
</html>