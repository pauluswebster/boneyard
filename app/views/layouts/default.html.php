<!doctype html>
<html lang="<?php echo str_replace('_', '-', $locale); ?>">
<head>
	<?php echo $this->html->charset();?>
	<title>Daily Grind > <?php echo $this->title(); ?></title>
	<?php echo $this->_render('element', 'script');?>
	<?php echo $this->scripts(); ?>
	<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>
</head>
<body class="app <?php echo $user() ? 'user' : 'public'?>">
	<div id="container">
		<?php echo $this->_render('element', 'layout/header', compact('user'));?>
		<div id="content">
			<?php echo $this->flashMessage->output();?>
			<?php echo $this->content(); ?>
		</div>
		<footer id="footer">
			<p>
				Daily Grind &copy; <?php echo date('Y')?>
				<a href="http://www.webprogression.co.nz" title="web|progression - Paul Webster web developer" target="blank"><img src="/img/wp.gif"></img></a>
				<a href="http://lithify.me" title="Lithium: the most rad php framework" target="blank"><img src="/img/li3-powered.gif"></img></a>
			</p>
		</footer>
	</div>
</body>
</html>