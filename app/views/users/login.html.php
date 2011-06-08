<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */
 
$this->title('Login');
?>
<script>
window.addEvent('domready', function(){
	new LightFace({
		width:400,
		height:330,
		content: $('content').get('html'),
		onOpen: function(){
			var form = this.contentBox.getElement('form');
			this.form = new Li3Form.Request(form, {}, this.messageBox);	
		}
	}).open(true);
});
</script>
<div id="content" style="display:none;">
	<div id="login">
		<h3><?=$settings['siteName'];?></h3>
		<?=$this->flashMessage->output();?>
		<?php
			echo $this->form->create(null, array('action' => 'login'));
			echo $this->form->field('username');
			echo $this->form->field('password', array('type' => 'password'));
			echo '<div style="float:right">';
			echo $this->form->field('remember_me', array('type' => 'checkbox'));
			echo $this->html->link('Forgot your password?', array('action' => 'password_reset'));
			echo '</div>';
			echo $this->form->submit('Login', array('style' => 'clear:none;'));
		?>
	</div>
</div>