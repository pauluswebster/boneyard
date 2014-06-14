<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2010, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

$this->title('Register');
?>
<div class="sli-users sli-users-register users-<?php echo $sliUserConfig;?>">
<h3>Register</h3>
<?php
	echo $this->form->create(null, array('action' => 'register'));
	foreach ($fields as $field => $options):
		if (is_numeric($field)):
			$field = $options;
			$options = array();
		endif;
		if (!$options):
			if (strpos($field, 'password') !== false):
				$options['type'] = 'password';
			endif;
		endif;
		echo $this->form->field($field, $options);
	endforeach;
	echo $this->form->submit('Submit');
	echo '<br>';
	echo $this->html->link('Login', array('action' => 'login'));
	echo $this->form->end();
?>
</div>