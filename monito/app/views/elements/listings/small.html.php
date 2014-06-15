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
		<h4><a href="/pages/listings/view?id=<?php echo $listing->id; ?>"><?php echo $listing->title?></a></h4>
		<p><strong class="label label-success"><?php echo $listing->price()?></strong></p>
		<p>Listed:
			<?php 
				$dt = new DateTime(null, $tz);
				$dt->setTimestamp($listing->listed);
				$format = 'D jS F';
				if ($dt->format('Y') != $now->format('Y')) $format.= ' Y';
			?>
			<strong><?php echo $dt->format($format);?></strong>
		<p>
	</div>
</div>