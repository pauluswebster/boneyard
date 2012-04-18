<?php 
	use tmimport\models\TMListings;
	
	$layout = isset($layout) ? $layout : 'full';
	$limit = isset($limit) ? $limit : 40;
	$split = isset($split) ? (is_numeric($split) ? $split : 2) : false;
	$listings = TMListings::listImported($limit);
?>

<hr>
	<?php echo $this->form->create(null, array('class' => 'form-inline', 'id' => 'listing-filter-form'));?>
	<?php echo $this->form->field('sort', array('type' => 'select', 'label' => false, 'list' => array('List date', 'Price')));?>
	<?php echo $this->form->end();?>
<hr>


<?php if(!$split):?>

<?php foreach($listings as $listing):?>

<?php echo $this->element->render('listings/' . $layout, compact('listing'));?>

<hr>

<?php endforeach;?>


<?php else:?>

<?php $i = 0; foreach($listings as $listing):?>

<?php if(!($i%$split)):?>
<div class="row-fluid">
<?php endif;?>

<div class="span<?php echo 12/$split;?>">
<?php echo $this->element->render('listings/' . $layout, compact('listing'));?>
</div>

<?php if($i%$split == $split-1):?>
</div>
<hr>
<?php endif;?>

<?php $i++; endforeach;?>

<?php endif;?>

<?php echo $this->element->render('pagination');?>