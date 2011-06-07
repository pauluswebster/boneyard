<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

?>

<?php if($user->get()):?>
<div id="announce">
	<div class="inner">
		<?=app\App::announce();?>
	</div>
	<?php if($user->admin):?>	
	<?php endif;?>
</div>

<div id="header">
	<ul id="userMenu">
		<li>Welcome back <?=$user->first_name;?></li>
		<li><a href="/users/edit/<?=$user->id;?>" id="userEdit">My Details</a></li>
		<li><a href="/logout">Logout</a></li>
	</ul>
	<h1><?=$t($settings['siteName']);?></h1>
</div>
<?php endif;?>