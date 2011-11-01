<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

?>

<header id="header">
	<?php if($user()):?>
	<h1>Daily Grind</h1>

	<?php if($active = $user->active()):?>
	<div id="activeJob">
	<p><?php echo "#{$active->unit()->id}: {$active->unit()->title}";?></p>
	<?php
		echo $this->html->link($t('Stop'), 'jobs::stop', array(
			'title' => $t('{:action} {:entity}', array('action' => $t('Stop'), 'entity' => $t('Work'))),
			'class' => 'button button-small button-red button-stop'
		));
	?>
	<span class="timed" data-time="<?php echo $active->start;?>"><?php echo $active->time(true);?></span>
	<span id="activeTotal">(<span class="timed" data-time="<?php echo time() - $active->unit()->time();?>"><?php echo $active->unit()->time(true);?></span>)</span>
	</div>
	<?php endif;?>

	<nav id="primaryNav">
		<ul>
			<li id="reportLink"><?php echo $this->html->link($t('Reports'), 'reports::index');?></li>
			<li id="taskLink"><?php echo $this->html->link($t('Tasks'), 'tasks::index'); ?></li>
			<li id="jobLink"><?php echo $this->html->link($t('Jobs'), 'jobs::index'); ?></li>
		</ul>
		<div class="clear"></div>
	</nav>

	<div class="clear"></div>
	<?php endif;?>
	<?php echo $this->flashMessage->output();?>
</header>