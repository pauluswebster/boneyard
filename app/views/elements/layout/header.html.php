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
	<h2>Daily Grind</h2>
	<nav id="userNav">
		<ul>
			<li><?php echo $user->first_name;?></li>
			<li><?php echo $this->html->link($t('My Details'), 'users::edit'); ?></li>
			<li><?php echo $this->html->link($t('Logout'), '/logout'); ?></li>
		</ul>
	</nav>
	<div class="clear"></div>
</header>

<nav id="primaryNav" class="navBar">
	<ul>
		<li><?php echo $this->html->link($t('Jobs'), 'jobs::index'); ?></li>
		<li><?php echo $this->html->link($t('Reports'), 'reports::index');?></li>
		<?php if($active = $user->job()):?>
		<li class="right app indicated">
			<?php
				echo $this->html->link("#{$active->job->id}: {$active->job->title}", 'jobs::stop', array(
					'title' => $t('{:action} {:entity}', array('action' => $t('Stop'), 'entity' => $t('Work'))),
					'class' => 'disabled'
				));
			?>
		</li>
		<?php endif;?>
	</ul>
	<div class="clear"></div>
</nav>
<?php endif;?>