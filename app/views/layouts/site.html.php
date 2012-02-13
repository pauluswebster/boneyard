<!DOCTYPE html>
<html>
<head>
	<?php echo $this->html->charset();?>
	<title>Progression Media & Development &gt; <?php echo $this->title() ; ?></title>
	<?php echo $this->html->style(array('debug', 'lithium')); ?>
	<?php echo $this->scripts(); ?>
	<?php echo $this->html->link('Icon', null, array('type' => 'icon')); ?>
</head>
<body class="app">
	<div id="container">
		<div id="header">
			<h1>Progression Media & Development</h1>
		</div>
		<div id="content">
			<?php echo $this->content(); ?>
		</div>
	</div>
</body>
</html>