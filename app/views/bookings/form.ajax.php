<?php
/**
 * Slicedup: a fancy tag line here
 *
 * @copyright	Copyright 2011, Paul Webster / Slicedup (http://slicedup.org)
 * @license 	http://opensource.org/licenses/bsd-license.php The BSD License
 */

$this->title($t($plural));
$users = $record->Users();
$record->formatDates();
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
		?>
		<h2>Booking Details</h2>
		<div class="details">
			<dl>
				<dt></dt>
				<dd><b><?php echo $record->Item()->title;?></b></dd>
				<dt>Date:</dt>
				<dd><?php echo $record->_start->format('l jS F Y');?></dd>
				<dt>Time:</dt>
				<dd><?php echo $record->_start->format($settings['listingIntervalFormat']) . ' - ' . $record->_end->format($settings['listingIntervalFormat']);?></dd>
				<dt>Title:</dt>
				<dd><?php echo $this->form->input('title');?></dd>
			</dl>
		</div>
		<div class="attendees">
			<h4>Attending</h4>
			<div id="Attending">
			<?php
				$userTmpl = '
					<div class="attendee">
						{:user}
						<a href="#" class="remove">x</a>
						' . $this->form->hidden('users[]', array(
							'value' => '{:id}'
						)) . '
					</div>
				';
				if($users->first()):
					do {
						$_user = $users->current()->User();
						$data = array(
							'user' => $_user->displayName(),
							'id' => $_user->id
						);
						echo lithium\util\String::insert($userTmpl, $data);
					} while($users->next());
				endif;
			?>
				</div>
			<?php
				echo $this->form->hidden('users[]', array('value' => 0));
				echo $this->form->field('add_someone', array(
					'value' => ($users->next() && $_user = $users->current()->User()) ? $_user->username : ''
				));
			?>
		</div>
		<?php
			echo $this->form->submit('Save');
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
	var userTmpl = '<?php echo preg_replace("/\s{2,}/", '', $userTmpl);?>';
	var users = <?php echo json_encode(array_values($userList));?>;
</script>