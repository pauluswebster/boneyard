<?php
	use app\models\Categories;
	use app\models\Cars;
	
	$types = Cars::bodyTypes();
	$makes = Categories::selectList(1);
?>


<div class="well" id="search-sidebar">
	<ul class="nav nav-list">
		<li class="nav-header"><a href="#"><i class="icon-search"></i> Search</a></li>
	</ul>
	<hr>
	<?php echo $this->form->create(null, array());?>
	<?php echo $this->form->field('type', array('type' => 'select', 'list' => $types));?>
	<?php echo $this->form->field('make', array('type' => 'select', 'list' => $makes));?>
	<?php echo $this->form->field('keywords', array());?>
	<?php echo $this->form->submit('Search', array('class' => 'btn'));?>
	<?php echo $this->form->end();?>
	<hr>
</div>
