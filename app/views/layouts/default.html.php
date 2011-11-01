<!doctype html>
<html lang="<?php echo str_replace('_', '-', $locale); ?>">
<head>
	<?php echo $this->html->charset();?>
	<title>Daily Grind > <?php echo $this->title(); ?></title>
	<?php echo $this->_render('element', 'script');?>
	<?php echo $this->scripts(); ?>
	<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>
</head>
<body class="<?php echo $bodyClass;?>">
	<div id="container">
		<?php echo $this->_render('element', 'layout/header', compact('user'));?>
		<div id="content">
			<?php if($user()):?>
			<nav id="userNav">
				<ul>
					<li><?php echo $user->first_name;?>:</li>
					<li><?php echo $this->html->link($t('My Details'), 'users::edit'); ?></li>
					<li><?php echo $this->html->link($t('Logout'), '/logout'); ?></li>
				</ul>
			</nav>
			<?php endif;?>
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