<div class="row">
	<div class="span10 offset1">
      <div class="hero-unit">
        <h1>Hello, world!</h1>
        <p>This is a simple hero unit, a simple jumbotron-style component for calling extra attention to featured content or information.</p>
        <p><a class="btn btn-primary btn-large">Learn more</a></p>
      </div>
    </div>
</div>
<hr>
<div class="row">
	<div class="span3 offset2 text-right">
		<p>First timer? Getting started is easy, we just need a few details:</p>
		<?php echo $this->form->create(null, array('url' => '/pages/listings'));?>
		<?php echo $this->form->field('email', array('type' => 'email', 'class' => 'input-text'));?>
		<?php echo $this->form->field('trader', array('type' => 'text', 'label' => 'Trader Number'))?>
		<?php echo $this->form->submit('Register', array('class' => 'btn'));?>
		<?php echo $this->form->end();?>
	</div>
	<div class="span3 offset2">
		<p>Been here before? Looks like you need to login:</p>
		<?php echo $this->form->create(null, array('url' => '/pages/listings'));?>
		<?php echo $this->form->field('email', array('type' => 'email'));?>
		<?php echo $this->form->field('password', array('type' => 'password'));?>
		<?php echo $this->form->label('Remember', $this->form->checkbox('remember') . ' Remember', array('escape' => false, 'class' => 'checkbox pull-right'));?>
		<?php echo $this->form->submit('Login', array('class' => 'btn pull-left'));?>
		<?php echo $this->form->end();?>
	</div>
</div>