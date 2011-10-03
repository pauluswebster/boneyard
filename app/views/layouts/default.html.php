<!doctype html>
<html lang="<?php echo str_replace('_', '-', $locale); ?>">
<head>
	<?php echo $this->html->charset();?>
	<title>Daily Grind > <?php echo $this->title(); ?></title>
	<?php echo $this->html->style(array('debug', 'lithium', 'app')); ?>
	<?php echo $this->_render('element', 'script');?>
	<?php echo $this->scripts(); ?>
	<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>
</head>
<body class="app">
	<div id="container">
		<?php echo $this->_render('element', 'layout/header', compact('user'));?>
		<div id="content">
			<?php echo $this->flashMessage->output();?>
			<?php echo $this->content(); ?>
		</div>
	</div>
</body>
</html>