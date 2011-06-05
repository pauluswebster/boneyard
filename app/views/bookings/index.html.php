<?php
	$this->html->script('bookem', array('inline' => false));
	$height = 60;
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
<div id="header">
	<h1>Bookings</h1>
</div>
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
		<table>
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
					?>
					<div class="booking" style="height:<?php echo $_h;?>px;top:<?php echo $_t;?>px;">
						<?php echo $booking->title;?>
						<?php
							echo $this->form->hidden('booking[]', array(
								'value' => $booking->id
							));
						?>
					</div>
					<?php endif; endforeach; endif;?>
					</div>
				</td>
				<?php endforeach;?>
			</tr>
			<?php $start->add($interval); $i++; endwhile;?>
		</table>
	</div>