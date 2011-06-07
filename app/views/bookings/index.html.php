<?php
	$this->html->script('bookem', array('inline' => false));
	$height = 30;
	$format =  'l jS F';
	if($date->format('Y') != $today->format('Y')):
		$format .= ' Y';
	endif;
	$this->title($today->format($format));
?>
<style>
.interval {
	height:<?php echo $height;?>px;
}
</style>
<div id="content">
	<div id="date-selector">
		<div id="date-select"></div>
		<?php 
			echo $this->form->hidden('today', array(
				'id'=>'DateSelect',
				'value' => $today->format($settings['datePickerFormat'])
			));
		?>
	</div>
	<h3>
		<?php
			echo $today->format($format);
		?>
	</h3>
	<div>
		<table id="bookings">
			<tr>
				<th></th>
				<?php foreach($items as $item):?>
				<th class="itemKey"><h4><?php echo $item->title;?></h4></th>
				<?php endforeach;?>
			</tr>
			<?php $i = 0; while (($period = $start->getTimestamp()) < $endTime):?>
			<tr>
				<th class="intervalKey">
					<?php 
						if(!($i%$settings['listingIntervalLabel'])):
							echo $start->format($settings['listingIntervalFormat']);
						endif;
					?>
				</th>
				<?php foreach($items as $item):?>
				<td class="interval">
					<?php
						echo $this->form->hidden('interval[]', array(
							'value' => json_encode(array(
								'item' => $item->id,
								'start' => $period
							))
						));
					?>
					<div class="intervalItems">
					<?php
					if(!empty($item->bookings)):
						foreach($item->bookings as $b => $booking):
							if ($booking->start >= $period && $booking->start < $period + $int):
								$_hm = (($booking->end - $booking->start) / $int);
								$_h = $_hm * $height + ($_hm - 1);
								$_tm = ($booking->start - $period) / $int;
								$_t = $_tm * $height;
								
								$booking->formatDates();
								$users = $booking->Users();
								
								$class = '';
								if($owner = $booking->isOwner($user)):
									$class.= ' owner';
								endif;
								if($attending = $booking->isAttending($user)):
									$class.= ' attending';
								endif;
								if($private = $booking->private):
									$class.= ' private';
								endif;
								if($edit = ($user->admin || $owner || ($attending && $permissions['attendingCanEdit'] && !$private))):
									$class.= ' edit';
								endif;
								if($details = (!$private || $user->admin || $owner || $attending)):
									$class.= ' details';
								endif;
								$title = $edit ? 'Click to edit details' : '';
					?>
					<div class="booking<?=$class;?>" style="<?="height:{$_h}px;top:{$_t}px;";?>">
						<?php
							if ($edit):
								echo $this->form->hidden('booking[]', array(
									'value' => $booking->id
								));
							endif;
						?>
						<div class="summary" style="<?="height:{$_h}px;";?>">
							<p><i><?php echo str_replace(' ', '&nbsp;', $booking->title);?></i></p>
						</div>
						<?php if($details):?>
						<div class="details shadow" title="<?php echo $title;?>">
							<p>
								<b>Player&nbsp;1:</b>&nbsp;<i><?php echo str_replace(' ', '&nbsp;', $users->first()->User()->fullName())?></i><br />
								<b>Player&nbsp;2:</b>&nbsp;<i><?php echo str_replace(' ', '&nbsp;', $users->next()->User()->fullName())?></i><br />
								<b>Time:</b>&nbsp;<i><?php echo str_replace(' ', '&nbsp;', $booking->_start->format($settings['listingIntervalFormat']) . ' - ' . $booking->_end->format($settings['listingIntervalFormat']));?></i>
							</p>
						</div>
						<?php endif;?>
					</div>
					<?php endif; endforeach; endif;?>
					</div>
				</td>
				<?php endforeach;?>
			</tr>
			<?php $start->add($interval); $i++; endwhile;?>
		</table>
	</div>