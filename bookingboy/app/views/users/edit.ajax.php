<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */
 

?>

<div class="<?php echo $plural;?>">
	<div class="edit <?php echo $singular;?>">
		<?php
			echo $this->form->create($record, array('url' => $this->_request->url));
		?>
		<h2>Account Settings</h2>
		<?php
			echo $this->form->field('first_name');
			echo $this->form->field('last_name');
			echo $this->form->field('username');
			echo $this->form->field('email');
			echo $this->form->field('new_password', array('type' => 'password', 'label' => 'Change Password'));
			echo $this->form->submit('Update');
			echo $this->form->end();
		?>
	</div>
</div>