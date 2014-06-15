<?php 
	$tz = new \DateTimeZone('Pacific/Auckland');
	$now = new \DateTime('now', $tz);
?>

<div class="row-fluid">
	<div class="span3">
		<div class="thumbnail">
			<img src="/img/px.png" class="listing" data-src="/img/listings/<?php echo $listing->photo();?>" alt="" title="" />
		</div>
	</div>
	<div class="span9">
		<div class="row-fluid">
			<div class="span9"><h3><a href="/pages/listings/view?id=<?php echo $listing->id; ?>"><?php echo $listing->title?></a></h3></div>
			<div class="span3 text-right"><strong class="label label-success"><?php echo $listing->price()?></strong></div>
		</div>
		<p><?php echo join(', ', $listing->attributes(5));?></p>
		<p><?php echo $listing->location;?></p>
		<p>
		<?php if($listing->listed > strtotime('today')):?>
			<strong class="label label-warning">Listed today!</strong></p>
		<?php elseif($listing->listed > strtotime('-1 week')):?>
			<strong class="label">Listed in last 7 days</strong>
		<?php else: ?>
			<?php 
				$dt = new DateTime(null, $tz);
				$dt->setTimestamp($listing->listed);
				$format = 'D jS F';
				if ($dt->format('Y') != $now->format('Y')) $format.= ' Y';
			?>
			Listed: <strong><?php echo $dt->format($format);?></strong>
		<?php endif;?>
		<p>
	</div>
</div>