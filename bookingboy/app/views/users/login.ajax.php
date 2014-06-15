<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

?>
<div id="login">
	<h1><?=$settings['siteName'];?></h1>
	<?=$this->flashMessage->output();?>
	<?php
		echo $this->form->create(null, array('action' => 'login'));
		echo $this->form->field('username');
		echo $this->form->field('password', array('type' => 'password'));
		echo '<div style="float:right">';
		echo $this->form->field('remember_me', array('type' => 'checkbox'));
		//echo $this->html->link('Forgot your password?', array('action' => 'password_reset'));
		echo '</div><div style="clear:right"></div>';
		echo $this->form->submit('Login');
		echo $this->form->end();
	?>
</div>