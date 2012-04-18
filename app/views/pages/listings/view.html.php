<?php
	use lithium\util\Inflector;
	use tmimport\models\TMListings;

	$listing = TMListings::getImported($this->request()->query['id']);
	
?>
<div class="row">
	<div class="span3">
		<?php echo $this->element->render('layout/sidebar', array('sidebar' => array('search', 'actions')));?>
	</div>
	<div class="span9">
		<h3><?php echo $listing->title; ?> <span class="pull-right"><?php echo $listing->price()?></span></h3>
		<hr>
		<div class="row-fluid">
			<div class="span7">
				<ul>
				<?php 
					foreach ($listing->attributes() as $name => $value):
						echo '<li><strong>' . Inflector::humanize($name) . ':</strong> ' . $value;
					endforeach;
				?>
				</ul>
			</div>
			<div class="span5">
				<div class="thumbnail">
					<img src="/img/px.png" class="listing" data-src="/img/listings/<?php echo $listing->photo();?>" alt="" title="" />
				</div>
				<?php foreach($listing->photos as $i => $photo):?>
					<?php if(!($i%3)):?><div class="row-fluid"><?php endif;?>
					<div class="span4">
						<div class="thumbnail">
							<img src="/img/px.png" class="listing left" data-src="/img/listings/<?php echo $listing->id . '/' . $photo;?>.jpg" alt="" title="<?php echo $i;?>" />
						</div>
					</div>
					<?php if($i%3==2):?></div><?php endif;?>
				<?php endforeach;?>
			</div>
		</div>
		<hr>
		<div>
			<p style="line-height:2;"><?php echo nl2br($listing->description);?></p>
		</div>
		<hr>
		<div class="row">
		</div>
	</div>
</div>