<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

?>

<?php if($user->get()):?>

<?php if($user->admin):?>
<div id="announce">
	<div class="inner">
		<div id="announceDisplay">
			<span class="data">
			<?=$announce = app\App::announce();?>
			</span>
			<span class="none">Click to add an announcement.</span>
			<span class="edit">&dash; click to edit</span>
		</div>
		<input name="data[announcement]" id="announceData" value="<?php echo $announce;?>" />
	</div>
</div>
<?php elseif($announce = app\App::announce()):?>
<div id="announce">
	<div class="inner">
		<?=$announce;?>
	</div>
</div>
<?php endif;?>

<div id="header">
	<ul id="userMenu">
		<li>Welcome back <?=$user->first_name;?></li>
		<li><a href="/users/edit" id="userEdit">My Details</a></li>
		<li><a href="/logout">Logout</a></li>
	</ul>
	<h1><?=$t($settings['siteName']);?></h1>
</div>
<?php endif;?>