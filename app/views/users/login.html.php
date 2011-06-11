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
		width:360,
		content: $('content').get('html'),
		onOpen: function(){
			var resize = this._resize.bind(this);
			var fade = this.fade.bind(this);
			var unfade = this.unfade.bind(this);
			var form = this.contentBox.getElement('form');
			this.form = new Li3Form.Request(form, {
				onSend: function() {
					fade();
				},
				onSuccess: function(){
					resize();
					unfade(1);
				}
			}, this.messageBox);
			this.addButton('Login', function(){
				this.form.submit();
			}.bind(this), 'green');
			resize();
		}
	}).open(true);
});
</script>
<div id="content" style="display:none;">
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
</div>