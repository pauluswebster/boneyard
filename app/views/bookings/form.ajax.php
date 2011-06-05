<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

$this->title($t($plural));
$users = $record->Users();
?>
<div class="<?php echo $plural;?>">
	<div class="edit <?php echo $singular;?>">
		<?php
			echo $this->form->create($record, array('url' => $this->_request->url));
			if($record->exists()):
				echo $this->form->hidden('id', array('id' => 'BookingId'));
			endif;
			echo $this->form->hidden('item_id');
			echo $this->form->hidden('start');
			echo $this->form->hidden('end');
			echo $this->form->hidden('creator_id');
			echo $this->form->hidden('users[0]', array(
				'id' => 'Users0', 
				'value' => ($users->first() && $_user = $users->current()->User()) ? $_user->id : $user->id
			));
			echo $this->form->hidden('users[1]', array(
				'id' => 'Users1', 
				'value' => ($users->next() && $_user = $users->current()->User()) ? $_user->id : ''
			));
		?>
		<h2>Booking Details</h2>
		<div class="details">
			<dl>
				<dt>Court:</dt>
				<dd><?php echo $record->Item()->title;?></dd>
				<dt>Date:</dt>
				<dd><?php echo $record->_start->format('l jS F Y');?></dd>
				<dt>Time:</dt>
				<dd><?php echo $record->_start->format($settings['listingIntervalFormat']) . ' - ' . $record->_end->format($settings['listingIntervalFormat']);?></dd>
			</dl>
		</div>
		<div class="players">
			<?php 
				echo $this->form->field('player_1', array(
					'value' => ($users->first() && $_user = $users->current()->User()) ? $_user->username : $user->username
				));
				echo $this->form->field('player_2', array(
					'value' => ($users->next() && $_user = $users->current()->User()) ? $_user->username : ''
				));
			?>
		</div>
		<?php
			echo $this->form->end();
		?>
	</div>
</div>
<?php 
	array_walk($userList, function(&$user, $id){
		$user = compact('user', 'id');
	});
?>
<script>
	var users = <?php echo json_encode(array_values($userList));?>;
</script>