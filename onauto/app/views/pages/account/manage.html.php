<div class="row">
	<div class="span3">
		<?php echo $this->element->render('layout/sidebar', array('sidebar' => array('account')));?>
	</div>
	<div class="span9">
		<ul class="breadcrumb">
			<li><a href="/pages/account">MyAuto</a><span class="divider">/</span></li>
			<li class="active">Manage Account</li>
		</ul>
		<hr>		
		<div class="tabbable">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#1" data-toggle="tab">Personal Details</a></li>
				<li><a href="#2" data-toggle="tab">Trader Details</a></li>
				<li><a href="#3" data-toggle="tab">Account Credit</a></li>
				<li><a href="#4" data-toggle="tab">Preferences</a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="1">
					<p>I'm in Section 1.</p>
				</div>
				<div class="tab-pane" id="2">
					<p>Howdy, I'm in Section 2.</p>
				</div>
				<div class="tab-pane" id="3">
					<p>Howdy, I'm in Section 3.</p>
				</div>
				<div class="tab-pane" id="4">
					<p>Howdy, I'm in Section 4.</p>
				</div>
			</div>
		</div>
	</div>
</div>