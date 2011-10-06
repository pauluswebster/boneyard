<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

?>

<?php if($user()):?>
<header id="header">
	<h1>Daily Grind</h1>
	<nav id="userNav">
		<ul>
			<li><?php echo $user->first_name;?></li>
			<li><?php echo $this->html->link($t('My Details'), 'users::edit'); ?></li>
			<li><?php echo $this->html->link($t('Logout'), '/logout'); ?></li>
		</ul>
	</nav>

	<?php if($active = $user->job()):?>
	<div id="activeJob">
	<p><?php echo "#{$active->job->id}: {$active->job->title}";?></p>
	<?php
		echo $this->html->link($t('Stop'), 'jobs::stop', array(
			'title' => $t('{:action} {:entity}', array('action' => $t('Stop'), 'entity' => $t('Work'))),
			'class' => 'clean-gray clean-gray-small'
		));
	?>
	<span class="dateFormat" data-time="<?php echo $active->start;?>"></span>
	</div>
	<?php endif;?>

	<nav id="primaryNav">
		<ul>
			<li id="reportLink"><?php echo $this->html->link($t('Reports'), 'reports::index');?></li>
			<li id="jobLink"><?php echo $this->html->link($t('Jobs'), 'jobs::index'); ?></li>
		</ul>
		<div class="clear"></div>
	</nav>

	<div class="clear"></div>
</header>
<?php endif;?>